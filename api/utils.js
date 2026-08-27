// Rate limiting in-memory (works in Vercel Serverless)
const rateLimitMap = new Map();

function checkRateLimit(ip, maxRequests = 30, windowMs = 60000) {
  const now = Date.now();
  const record = rateLimitMap.get(ip);

  if (!record || now > record.resetTime) {
    rateLimitMap.set(ip, { count: 1, resetTime: now + windowMs });
    return true;
  }

  if (record.count >= maxRequests) {
    return false;
  }

  record.count++;
  return true;
}

// Known attack IPs
const KNOWN_ATTACK_IPS = [
  '66.234.153.160', // Recent spam attacker from landing-eps
];

// Blocked IPs from environment variable
const BLOCKED_IPS = (process.env.BLOCKED_IPS || '').split(',').filter(Boolean);

function isIPBlocked(ip) {
  return BLOCKED_IPS.includes(ip) || KNOWN_ATTACK_IPS.includes(ip);
}

// Filter offensive words (Spanish and English)
const PALABRAS_OFENSIVAS = [
  'mierda', 'puta', 'pendejo', 'imbecil', 'estupido', 'basura', 'idiota', 'carajo', 'joder', 'maldito',
  'pene', 'vagina', 'pinga', 'culo', 'teta', 'pedo', 'cipote', 'chucha', 'puchaira', 'somawe',
  'huevón', 'huevon', 'cabron', 'cabrón', 'chupamedias', 'soplapollas', 'maricon', 'maricón',
  'puto', 'zorra', 'perra', 'gonorrea', 'gonorea', 'chamaco', 'pendeja',
  'estupida', 'estúpida', 'retardado', 'retrasado', 'mongol', 'mongoloide',
  'ofanim', 't.me', 'telegram', 'whatsapp', 'facebook', 'instagram', 'twitter', 'tiktok',
  'xxx', 'porn', 'sex', 'nude', 'naked', 'fuck', 'shit', 'ass', 'dick', 'pussy', 'cock',
  'bitch', 'slut', 'whore', 'damn', 'hell', 'crap', 'bastard', 'asshole', 'motherfucker',
];

function containsOffensiveWords(text) {
  if (!text) return false;
  const lower = text.toLowerCase();
  return PALABRAS_OFENSIVAS.some(palabra => lower.includes(palabra));
}

// Sanitize text for logging
function sanitizeForLog(text, maxLength = 100) {
  if (!text) return '-';
  return text
    .replace(/[<>"'&]/g, '')
    .trim()
    .slice(0, maxLength) || '-';
}

// Get client IP from request
function getClientIP(req) {
  return req.headers['x-forwarded-for']?.split(',')[0]?.trim()
    || req.headers['x-real-ip']
    || 'unknown';
}

module.exports = {
  checkRateLimit,
  isIPBlocked,
  containsOffensiveWords,
  sanitizeForLog,
  getClientIP,
  PALABRAS_OFENSIVAS,
};

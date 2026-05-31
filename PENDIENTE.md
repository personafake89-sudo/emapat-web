# Pendiente — pagoWEB EMAPAT

## Variables de entorno Vercel (emapat-web)
- [ ] Verificar que `DATABASE_URL` apunta a la base correcta (la de EMAPAT, no la de landing-eps)
- [ ] Agregar `TELEGRAM_BOT_TOKEN` y `TELEGRAM_CHAT_ID` si no aparecen en el dashboard de Vercel (ya se agregaron por CLI)
- [ ] `ADMIN_PASSWORD` default: `emapat2025` — cambiar a algo más seguro antes de producción real

## Base de datos Neon (emapat-pagos)
- [ ] Crear índices en la tabla `pagos` si el volumen crece (ej. `CREATE INDEX ON pagos(codigo_cliente)`)
- [ ] Rotar credenciales antes de ir a producción real

## Pasarela de pago
- [ ] Cuando se tenga las credenciales Niubiz (merchantId, accessKey, sessionToken) integrar el checkout.js real
- [ ] El flujo actual es simulado — tarjetas de prueba aprobadas: `4111111111111111`, `4242424242424242`
- [ ] Tarjetas rechazadas de prueba: `4000000000000002`

## Modal de pago
- [ ] Opción QR / Billetera electrónica → pendiente integrar pasarela real
- [ ] Opción Yape / Plin → pendiente integrar pasarela real

## Página estática (pagoWEB/index.html)
- [ ] `pagoweb_data.json` tiene datos hasta la última exportación — regenerar cuando haya nuevos saldos
  - Script: `/home/personafake/Documents/emapat/saldos_reporte.py` + script generador del JSON
- [ ] Agregar imagen `tarjetas.jpg` como asset local (actualmente carga desde sysco.emapat.com.pe)

## Panel admin
- [ ] URL: `emapat.epsagua.com/pagoWEB/admin_pagos.html`
- [ ] Password: `emapat2025`
- [ ] Cambiar password antes de compartir el link

## Repo pagoweb-emapat (el PHP)
- [ ] Subir también los cambios de `home.php` al repo `pagoweb-emapat` (actualmente solo están en `emapat-web`)

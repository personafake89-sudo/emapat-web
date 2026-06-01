<?php
    require_once('check-login.php');
    require_once("class.Consumer.php");
    require_once('class.Empresa.php');
    require_once("class.Visa.php");

    ini_set('display_errors', '1');
    date_default_timezone_set('America/Lima');

    $curl        = new Consumer();
    $visa        = new Visa();

    $codigo      = $_SESSION['user_session'];
    $correo      = $_SESSION['email_session'];
    $docide      = $_SESSION['dni_session'];
    $valcomision = $_SESSION['comision'];
    $checkfactmes= $_SESSION['checkfactmes'];
    $urlBase     = $_SESSION['urlpag'];

    $datosVisa = $visa->generar_pedido(["codcliente" => $codigo, "dni" => $docide, "correo" => $correo]);
    if (isset($datosVisa->codcliente) && is_numeric($datosVisa->codcliente)) {
        $codcliente = $datosVisa->codcliente;
        $titular    = $datosVisa->cliente;
        $localidad  = $datosVisa->nomsuc;
        $direccion  = $datosVisa->tdir . ' ' . $datosVisa->dir . ' ' . $datosVisa->nrocalle;
        $nropedido  = $datosVisa->nropedido;
    } else {
        exit("Código sin información.");
    }

    $_SESSION['propietario'] = $titular;
    $_SESSION['cdireccion']  = $direccion;

    // Cargar deuda
    $rowsDeuda = $visa->recupear_deuda("001", $codigo);
    $rowsArray = [];
    $maxPeriod = 0;
    $nroCorte  = null;
    foreach ($rowsDeuda as $r) {
        if (isset($r->impmestotal)) {
            $rowsArray[] = $r;
            $period = intval($r->anio) * 100 + intval($r->mes);
            if ($period > $maxPeriod) { $maxPeriod = $period; $nroCorte = $r->nrofacturacion; }
        }
    }
    $mesesDeuda  = count($rowsArray);
    $tot_01      = 0;
    foreach ($rowsArray as $r) $tot_01 += $r->impmestotal;
    $total_deuda = $tot_01 + floatval($valcomision);
    $fecha       = date('d/m/Y');

    // Token VisaNet (solo si hay deuda)
    $sesion = '';
    if ($total_deuda > 0) {
        include_once './config/functions.php';
        $visaToken = generateToken();
        $sesion    = generateSesion($total_deuda, $visaToken, $correo, $codcliente, $docide);
    }
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EPS EMAPAT S.A. - Estado de Cuenta</title>
  <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
  <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="./assets/css/square/blue.css">
  <script src="./assets/js/jquery-3.3.1.min.js"></script>
  <style>
    :root {
      --teal:    #0097a7;
      --teal-dk: #00838f;
      --green:   #2ecc71;
      --green-dk:#27ae60;
      --dark:    #1c3643;
      --text:    #17202A;
      --border:  #dee2e6;
      --page-bg: #f4f6f8;
    }
    * { box-sizing: border-box; }
    body { background: var(--page-bg); font-family: Arial, sans-serif; display:flex; flex-direction:column; min-height:100vh; margin:0; }

    /* NAV */
    .navbar { background: var(--teal) !important; padding: .6rem 1rem; }
    .navbar-brand { font-weight:700; font-size:1.1rem; color:#fff !important; }
    .btn-salir-nav { background:#1c3643; color:#fff; border:none; border-radius:6px; padding:6px 16px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; }
    .btn-salir-nav:hover { background:#0f222c; color:#fff; }

    /* CARDS */
    .card-custom { background:#fff; border-radius:12px; border:1px solid var(--border); box-shadow:0 2px 10px rgba(0,0,0,0.06); margin-bottom:1.25rem; overflow:hidden; }
    .card-custom-header { background:#f8f9fa; border-bottom:1px solid var(--border); padding:10px 18px; font-weight:700; font-size:13px; color:#444; letter-spacing:.3px; text-transform:uppercase; }
    .card-custom-body { padding:18px; }
    .card-custom-footer { background:#f8f9fa; border-top:1px solid var(--border); padding:14px 18px; text-align:center; }

    /* DATOS CLIENTE */
    .dato-label { font-weight:700; color:#555; font-size:13px; }
    .dato-val   { color: var(--text); font-size:14px; }

    /* TABLA DEUDA */
    .tabla-deuda { width:100%; border-collapse:collapse; font-size:14px; }
    .tabla-deuda thead th { border-bottom:2px solid var(--border); padding:8px 10px; color:#555; font-weight:700; font-size:12px; text-transform:uppercase; }
    .tabla-deuda tbody td { padding:10px; border-bottom:1px solid #f0f0f0; color:var(--text); vertical-align:middle; }
    .tabla-deuda .fila-corte  { background:#fff3e0 !important; }
    .tabla-deuda .fila-reclamo { background:#fdecea !important; }
    .tabla-deuda .fila-total td { font-weight:700; color:#1B4F72; font-size:13px; background:#eaf4fb; }
    .badge-corte   { background:#e74c3c; color:#fff; font-size:11px; padding:2px 8px; border-radius:10px; white-space:nowrap; }
    .badge-reclamo { background:#e67e22; color:#fff; font-size:11px; padding:2px 8px; border-radius:10px; }
    .badge-pend    { background:#90a4ae; color:#fff; font-size:11px; padding:2px 8px; border-radius:10px; }

    /* ALERT MESES */
    .alert-corte-bar { background:#fff3e0; border-left:4px solid #e67e22; padding:10px 16px; font-size:.88rem; margin-bottom:14px; border-radius:4px; }

    /* TOTAL */
    .total-pagar-row { background:#eaf4fb; border-radius:8px; padding:14px 18px; margin:14px 0 10px; display:flex; align-items:center; justify-content:center; gap:12px; }
    .total-pagar-row .label { font-size:15px; font-weight:700; color:#1B4F72; }
    .total-pagar-row .monto { font-size:24px; font-weight:800; color:#0097a7; }

    /* BOTONES */
    .btn-pagar { background:var(--green); color:#fff; border:none; border-radius:8px; padding:13px 36px; font-size:15px; font-weight:700; cursor:pointer; letter-spacing:.5px; transition:background .15s; }
    .btn-pagar:hover { background:var(--green-dk); }
    .btn-accion { border-radius:6px; padding:8px 18px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; border:none; display:inline-block; margin:4px; }
    .btn-info-c   { background:#0097a7; color:#fff; } .btn-info-c:hover   { background:#00838f; color:#fff; }
    .btn-dark-c   { background:#343a40; color:#fff; } .btn-dark-c:hover   { background:#23272b; color:#fff; }
    .btn-danger-c { background:#e74c3c; color:#fff; } .btn-danger-c:hover { background:#c0392b; color:#fff; }

    /* FOOTER */
    footer { background:var(--dark); padding:18px 0; margin-top:auto; }
    .footer-marca { color:#0097a7; font-weight:700; font-size:1.2rem; }
    .footer-info  { color:#ccc; font-size:13px; }
    .footer-info p { margin:2px 0; }
    .footer-info .titulo { font-weight:700; color:#fff; font-size:14px; margin-bottom:4px; }

    /* MODAL */
    #overlayPago { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; }
    #modalPago { background:#f0f0f0; border-radius:14px; width:330px; max-width:95vw; max-height:95vh; overflow-y:auto; position:relative; box-shadow:0 8px 32px rgba(0,0,0,0.3); }
    .modal-paso { padding:1.4rem 1.2rem 1.2rem; }
    .modal-lang { font-size:12px; color:#555; margin-bottom:.7rem; display:flex; gap:10px; }
    .modal-lang .act { font-weight:700; color:#222; }
    .modal-logo { width:68px; height:auto; margin-bottom:1rem; }
    .modal-titulo { font-size:14px; font-weight:700; color:#222; margin-bottom:.8rem; }
    .metodo-pago { display:flex; align-items:flex-start; gap:10px; background:#fff; border:1.5px solid #ddd; border-radius:10px; padding:10px 12px; margin-bottom:8px; cursor:pointer; transition:border-color .15s; }
    .metodo-pago.sel { border:2px solid #2196f3; }
    .metodo-pago input[type=radio] { margin-top:3px; accent-color:#2196f3; flex-shrink:0; }
    .met-label { font-size:13px; font-weight:700; color:#222; margin-bottom:2px; }
    .met-sub   { font-size:11px; color:#777; margin-bottom:6px; }
    .logos-row { display:flex; gap:5px; flex-wrap:wrap; align-items:center; }
    .logo-m    { height:23px; width:auto; border-radius:3px; }
    .btn-continuar { width:100%; padding:12px; background:var(--green); border:none; border-radius:8px; color:#fff; font-size:15px; font-weight:700; cursor:pointer; margin-top:.5rem; }
    .btn-continuar:hover { background:var(--green-dk); }
    .btn-volver { background:none; border:none; cursor:pointer; padding:0; font-size:13px; color:#2196f3; display:flex; align-items:center; gap:4px; }
    .modal-top-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .resumen-pago { background:#fff; border-radius:10px; padding:12px 14px; margin-bottom:1rem; border:1px solid #e0e0e0; font-size:12px; }
    .resumen-pago .rtitulo { font-size:13px; font-weight:700; color:#333; margin-bottom:8px; }
    .resumen-pago table { width:100%; border-collapse:collapse; }
    .resumen-pago td { padding:2px 0; color:#555; }
    .resumen-pago .rmonto { font-weight:700; color:#2ecc71; font-size:15px; text-align:right; }
    .resumen-pago .rval   { text-align:right; font-weight:600; color:#333; }
    .ckb-label { font-size:12px; color:#555; margin-bottom:10px; display:block; }
    .ckb-label a { color:#2196f3; }
    .logos-bottom { display:flex; justify-content:center; gap:6px; flex-wrap:wrap; margin-top:1rem; padding-top:10px; border-top:1px solid #e0e0e0; }
    .logos-bottom img { height:18px; width:auto; }
    .btn-cerrar-modal { background:none; border:none; cursor:pointer; padding:0; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="home.php">EPS EMAPAT S.A.</a>
    <a href="logout.php?logout=true" class="btn-salir-nav">Salir</a>
  </div>
</nav>

<div class="container" style="max-width:760px; padding-top:1.5rem; flex:1;">

  <!-- DATOS CLIENTE -->
  <div class="card-custom">
    <div class="card-custom-header">&#128100; Datos del Cliente</div>
    <div class="card-custom-body">
      <div class="row">
        <div class="col-6 mb-2">
          <span class="dato-label">Código: </span>
          <span class="dato-val"><?= htmlspecialchars($codcliente) ?></span>
        </div>
        <div class="col-6 mb-2">
          <span class="dato-label">Localidad: </span>
          <span class="dato-val"><?= htmlspecialchars($localidad) ?></span>
        </div>
        <div class="col-12 col-md-6 mb-2">
          <span class="dato-label">Titular: </span>
          <span class="dato-val"><?= htmlspecialchars($titular) ?></span>
        </div>
        <div class="col-12 col-md-6 mb-2">
          <span class="dato-label">Dirección: </span>
          <span class="dato-val"><?= htmlspecialchars($direccion) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ESTADO DE CUENTA -->
  <div class="card-custom">
    <div class="card-custom-header">&#128203; Estado de Cuenta</div>
    <div class="card-custom-body">

      <form action="./pagar.php" method="POST" name="frm" id="frm" target="_blank">
        <input type="hidden" name="codcliente"  value="<?= htmlspecialchars($codcliente) ?>">
        <input type="hidden" name="nropedido"   value="<?= htmlspecialchars($nropedido) ?>">
        <input type="hidden" name="correo"      value="<?= htmlspecialchars($correo) ?>">
        <input type="hidden" name="dni"         id="dni" value="<?= htmlspecialchars($docide) ?>">
        <input type="hidden" name="pagototal"   id="pagototal" value="<?= $tot_01 ?>">
        <input type="hidden" name="comisionn"   id="comisionn" value="<?= $valcomision ?>">

        <?php if ($mesesDeuda > 0): ?>
        <div class="alert-corte-bar">
          <strong>Meses en deuda: <?= $mesesDeuda ?></strong>
          &nbsp;|&nbsp; El período más reciente está
          <span class="badge-corte">AFECTO AL CORTE</span>
        </div>
        <?php endif; ?>

        <?php if ($checkfactmes == 'SI'): ?>
        <div class="alert alert-info py-2 px-3 mb-3" style="font-size:13px;">
          <strong>Nota:</strong> Desactive los períodos que no desee cancelar en esta operación.
        </div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="tabla-deuda" id="miTabla">
            <thead>
              <tr>
                <?php if ($checkfactmes == 'SI'): ?><th style="width:36px;"></th><?php endif; ?>
                <th>Período</th>
                <th>Recibo</th>
                <th style="text-align:right;">Total Mes</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $tot_02 = 0;
              foreach ($rowsArray as $rows):
                  $tot_02    += $rows->impmestotal;
                  $esCorte    = ($rows->nrofacturacion == $nroCorte);
                  $esReclamo  = ($rows->flagreclamo == 1);
                  $filaClass  = $esCorte ? 'fila-corte' : ($esReclamo ? 'fila-reclamo' : '');
              ?>
              <tr class="<?= $filaClass ?>">
                <td>
                  <?php if (($rows->nrodoc != '0') && ($checkfactmes == 'SI')): ?>
                    <input type="checkbox" name="nrofacturacion[]" checked
                           class="qwerty form-check-input"
                           id="<?= $rows->impmestotal ?>"
                           value="<?= $rows->nrofacturacion ?>">
                  <?php else: ?>
                    <input type="hidden" name="nrofacturacion[]"
                           class="qwerty form-check-input"
                           id="<?= $rows->impmestotal ?>"
                           value="<?= $rows->nrofacturacion ?>">
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($curl->nombremes($rows->mes) . ' - ' . $rows->anio) ?></td>
                <td><?= htmlspecialchars($rows->seriedoc . ' - ' . $rows->nrodoc) ?></td>
                <td style="text-align:right;"><strong>S/ <?= number_format($rows->impmestotal, 2) ?></strong></td>
                <td>
                  <?php if ($esCorte): ?>
                    <span class="badge-corte">AFECTO AL CORTE</span>
                  <?php elseif ($esReclamo): ?>
                    <span class="badge-reclamo">EN RECLAMO</span>
                  <?php else: ?>
                    <span class="badge-pend">PENDIENTE</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <tr class="fila-total">
                <td colspan="<?= ($checkfactmes == 'SI') ? 4 : 3 ?>" style="text-align:right; padding:10px;">
                  TOTAL DEUDA (<?= $mesesDeuda ?> mes<?= $mesesDeuda != 1 ? 'es' : '' ?>)
                </td>
                <td style="text-align:right; padding:10px;">S/ <?= number_format($tot_02, 2) ?></td>
              </tr>
              <?php if ($valcomision > 0): ?>
              <tr class="fila-total">
                <td colspan="<?= ($checkfactmes == 'SI') ? 4 : 3 ?>" style="text-align:right; padding:10px;">
                  Comisión Niubiz
                </td>
                <td style="text-align:right; padding:10px;">S/ <?= number_format($valcomision, 2) ?></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($tot_01 > 0): ?>
        <div class="total-pagar-row">
          <span class="label">TOTAL A PAGAR:</span>
          <span class="monto" id="montoMostrado">S/. <?= number_format($total_deuda, 2) ?></span>
        </div>
        <div style="text-align:center; margin-bottom:6px;">
          <button type="button" class="btn-pagar" id="btnpagar" onclick="abrirModalPago()">
            &#128179; REALIZAR PAGO
          </button>
        </div>
        <?php endif; ?>

      </form>
    </div>

    <div class="card-custom-footer">
      <a href="verPagos.php"           class="btn-accion btn-info-c">Ver pagos</a>
      <a href="buscarClientes.php"     class="btn-accion btn-dark-c">Nueva búsqueda</a>
      <a href="logout.php?logout=true" class="btn-accion btn-danger-c">Salir</a>
    </div>
  </div>

</div>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row align-items-center">
      <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
        <span class="footer-marca">EPS EMAPAT S.A.</span>
      </div>
      <div class="col-12 col-md-6 text-center footer-info">
        <p class="titulo">Dudas o Consultas</p>
        <p>Centro de atención al cliente: 571984</p>
        <p>Av. Ernesto Rivero 782 - Pto. Maldonado</p>
      </div>
    </div>
  </div>
</footer>


<!-- MODAL MÉTODOS DE PAGO -->
<div id="overlayPago">
  <div id="modalPago">

    <!-- PASO 1: Selección de método -->
    <div id="mpaso1" class="modal-paso">
      <button class="btn-cerrar-modal" onclick="cerrarModal()" style="position:absolute;top:10px;right:10px;">
        <img src="https://static-content.vnforapps.com/v2/css/images/close.png" width="20" height="20" alt="Cerrar">
      </button>
      <div class="modal-lang"><span class="act">ESP</span><span>ENG</span></div>
      <img src="<?= $urlBase ?>/assets/img/logotipo.png" class="modal-logo" alt="EMAPAT">
      <div class="modal-titulo">Elige un método de pago</div>

      <div class="metodo-pago sel" onclick="elegirMetodo(this,'tarjeta')">
        <input type="radio" name="mpago" value="tarjeta" checked>
        <div>
          <div class="met-label">Tarjeta de crédito y/o débito</div>
          <div class="met-sub">Paga al contado o en cuotas</div>
          <div class="logos-row">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/bottom/visa.png"     alt="Visa">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/bottom/mc.png"       alt="Mastercard">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/bottom/dc.png"       alt="Diners">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/bottom/unionpay.svg" alt="UnionPay">
          </div>
        </div>
      </div>

      <div class="metodo-pago" onclick="elegirMetodo(this,'qr')">
        <input type="radio" name="mpago" value="qr">
        <div>
          <div class="met-label">Código QR / Billetera electrónica</div>
          <div class="logos-row" style="margin-top:6px;">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/pagoefectivo/wallets/plin.png"     alt="Plin">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/pagoefectivo/wallets/agoraPay.png" alt="Agora">
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/pagoefectivo/wallets/ligo.png"     alt="Ligo">
          </div>
        </div>
      </div>

      <div class="metodo-pago" onclick="elegirMetodo(this,'yapeplin')">
        <input type="radio" name="mpago" value="yapeplin">
        <div>
          <div class="met-label">Pago con Yape / Plin</div>
          <div class="logos-row" style="margin-top:6px;">
            <span style="height:23px;padding:0 9px;background:#6a0dad;border-radius:3px;font-size:11px;font-weight:700;color:#fff;display:inline-flex;align-items:center;">Yape</span>
            <img class="logo-m" src="https://static-content.vnforapps.com/v2/img/pagoefectivo/wallets/plin.png" alt="Plin">
          </div>
        </div>
      </div>

      <button class="btn-continuar" onclick="continuarPaso1()">Continuar</button>
    </div>

    <!-- PASO 2: Confirmación + VisaNet -->
    <div id="mpaso2" class="modal-paso" style="display:none;">
      <div class="modal-top-bar">
        <button class="btn-volver" onclick="irPaso(1)">&#8592; Volver</button>
        <img src="<?= $urlBase ?>/assets/img/logotipo.png" style="width:56px;height:auto;" alt="EMAPAT">
        <button class="btn-cerrar-modal" onclick="cerrarModal()">
          <img src="https://static-content.vnforapps.com/v2/css/images/close.png" width="18" height="18" alt="Cerrar">
        </button>
      </div>

      <div class="resumen-pago">
        <div class="rtitulo">Resumen del pago</div>
        <table>
          <tr><td style="color:#888;">Cliente:</td>   <td class="rval"><?= htmlspecialchars($codcliente . ' - ' . $titular) ?></td></tr>
          <tr><td style="color:#888;">N° Pedido:</td> <td class="rval"><?= htmlspecialchars($nropedido) ?></td></tr>
          <tr><td style="color:#888;">Concepto:</td>  <td class="rval">PAGO SERVICIO DE AGUA</td></tr>
          <tr><td style="color:#888;">Fecha:</td>     <td class="rval"><?= $fecha ?></td></tr>
          <tr>
            <td style="color:#888;padding-top:6px;font-weight:700;">TOTAL:</td>
            <td class="rmonto">S/. <?= number_format($total_deuda, 2) ?></td>
          </tr>
        </table>
      </div>

      <label class="ckb-label">
        <input type="checkbox" id="ckbTermsModal" onchange="toggleVisaBtn(this)">
        Acepto los <a href="assets/doc/Terminos_condiciones_visa.pdf" target="_blank">Términos y condiciones</a>
      </label>

      <div id="wrapVisaModal" style="display:none;">
        <form id="frmVisaNet" action="<?= $urlBase ?>/finalizar.php" method="POST">
          <script
            src="<?= VISA_URL_JS ?>"
            data-sessiontoken="<?= htmlspecialchars($sesion) ?>"
            data-channel="web"
            data-merchantid="<?= VISA_MERCHANT_ID ?>"
            data-merchantlogo="<?= $urlBase ?>/assets/img/logotipo.png"
            data-purchasenumber="<?= htmlspecialchars($nropedido) ?>"
            data-amount="<?= number_format($total_deuda, 2, '.', '') ?>"
            data-expirationminutes="5"
            data-timeouturl="<?= $urlBase ?>/"
            data-cardholderemail="<?= htmlspecialchars($correo) ?>">
          </script>
        </form>
      </div>

      <div class="logos-bottom">
        <img src="https://static-content.vnforapps.com/v2/img/pci.png"             alt="PCI">
        <img src="https://static-content.vnforapps.com/v2/img/bottom/visa.png"     alt="Visa">
        <img src="https://static-content.vnforapps.com/v2/img/bottom/mc.png"       alt="Mastercard">
        <img src="https://static-content.vnforapps.com/v2/img/bottom/dc.png"       alt="Diners">
        <img src="https://static-content.vnforapps.com/v2/img/bottom/unionpay.svg" alt="UnionPay">
      </div>
    </div>

  </div>
</div>


<script src="./assets/js/icheck.min.js"></script>
<script>
/* Calcular total con checkboxes (cuando checkfactmes = SI) */
function calcular() {
  var pagtotal = 0;
  $('.qwerty').each(function() {
    if (this.checked) pagtotal += parseFloat($(this).attr('id'));
  });
  $('#pagototal').val(pagtotal.toFixed(2));
  if (pagtotal == 0) {
    $('#btnpagar').hide();
  } else {
    $('#btnpagar').show();
    $('#montoMostrado').text('S/. ' + pagtotal.toFixed(2));
  }
}

$('#pagototal').val(<?= $tot_01 ?>);

$('input[type="checkbox"]').iCheck({
  checkboxClass: 'icheckbox_square-blue',
  radioClass:    'iradio_square-blue',
  increaseArea:  '20%'
});
$('input[type="checkbox"]').on('ifChanged', function() { calcular(); });

/* Modal */
var metodoElegido = 'tarjeta';

function abrirModalPago() { irPaso(1); document.getElementById('overlayPago').style.display = 'flex'; }
function cerrarModal()     { document.getElementById('overlayPago').style.display = 'none'; }
function irPaso(n) {
  document.getElementById('mpaso1').style.display = n === 1 ? 'block' : 'none';
  document.getElementById('mpaso2').style.display = n === 2 ? 'block' : 'none';
  if (n === 1) {
    var ckb = document.getElementById('ckbTermsModal');
    if (ckb) ckb.checked = false;
    var w = document.getElementById('wrapVisaModal');
    if (w) w.style.display = 'none';
  }
}
document.getElementById('overlayPago').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});
function elegirMetodo(el, metodo) {
  metodoElegido = metodo;
  document.querySelectorAll('.metodo-pago').forEach(function(o) {
    o.classList.remove('sel');
    o.style.border = '1.5px solid #ddd';
    o.querySelector('input').checked = false;
  });
  el.classList.add('sel');
  el.style.border = '2px solid #2196f3';
  el.querySelector('input').checked = true;
}
function continuarPaso1() {
  if (metodoElegido === 'tarjeta') {
    irPaso(2);
  } else {
    alert('Próximamente disponible.');
  }
}
function toggleVisaBtn(ckb) {
  document.getElementById('wrapVisaModal').style.display = ckb.checked ? 'block' : 'none';
}
</script>
</body>
</html>

<?php
require_once('check-login.php');
ini_set('display_errors', '0');

if (!isset($_POST['pagototal']) || $_POST['pagototal'] == 0 || $_POST['pagototal'] == "") {
    header("Location: ./index.php");
    exit;
}
if (!isset($_POST['dni']) || strlen($_POST['dni']) != 8 || $_POST['dni'] == "") {
    echo "<div style='text-align:center;padding:40px;'>";
    echo "<p>Debe ingresar un DNI válido (8 dígitos).</p>";
    echo "<input type='button' onclick='history.back()' value='VOLVER'>";
    echo "</div>";
    exit;
}

date_default_timezone_set('America/Lima');
include './config/functions.php';
require_once('class.Empresa.php');

$codcliente     = $_POST['codcliente'];
$purchaseNumber = $_POST['nropedido'];
$dni            = $_POST['dni'];
$amount         = floatval($_POST['pagototal']) + floatval($_POST['comisionn']);
$email          = $_POST['correo'];
$urlBase        = $_SESSION['urlpag'];

$_SESSION['dni_session']    = $dni;
$_SESSION['email_session']  = $email;
$_SESSION['codcliente']     = $codcliente;
$_SESSION['amount']         = $amount;
$_SESSION['purchaseNumber'] = $purchaseNumber;

$nrofact  = isset($_POST['nrofacturacion']) ? $_POST['nrofacturacion'] : [];
$actualizar = implode(',', $nrofact);
$_SESSION['actualizar'] = $actualizar;

$token  = generateToken();
$sesion = generateSesion($amount, $token, $email, $codcliente, $dni);
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGO VISA — EPS EMAPAT S.A.</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/square/blue.css">
    <script src="./assets/js/jquery-3.3.1.min.js"></script>
</head>
<body class="bg-light d-flex flex-column h-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-info border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="home.php">EPS EMAPAT S.A.</a>
        <a href="logout.php?logout=true" class="btn btn-dark">Salir</a>
    </div>
</nav>

<section id="sectioncontenido" class="p-0 m-0">
    <div class="container mt-4" id="divcontenido">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6" id="divpagovisa">
                <div class="card mt-3 mt-md-2" style="border-radius:15px;box-shadow:0 2px 20px #cbcbcb;">
                    <div class="card-header font-weight-bold text-center">
                        <p>Pago con Visa</p>
                        <a href="assets/doc/MANUAL_01.pdf" target="_blank" class="btn btn-info"><b>Manual de uso</b></a>
                    </div>
                    <div class="card-body">
                        <p class="h5" style="padding-left:20px;">Información del pago</p>
                        <b style="padding-left:20px;">Codigo Cliente: </b><?= htmlspecialchars($codcliente) ?><br>
                        <b style="padding-left:20px;">Importe a pagar: </b> S/. <?= number_format($amount, 2, '.', ',') ?><br>
                        <b style="padding-left:20px;">Número de pedido: </b> <?= htmlspecialchars($purchaseNumber) ?><br>
                        <b style="padding-left:20px;">Concepto: </b> PAGO DE SERVICIO DE AGUA<br>
                        <b style="padding-left:20px;">Fecha: </b> <?= date("d/m/Y") ?><br>
                        <b style="padding-left:20px;">DNI: </b> <?= htmlspecialchars($dni) ?><br>
                        <hr>
                        <input type="checkbox" name="ckbTerms" id="ckbTerms" onclick="visaNetEc3()">
                        <label for="ckbTerms">Acepto los <a href="assets/doc/Terminos_condiciones_visa.pdf" target="_blank">Términos y condiciones</a></label>
                        <form id="frmVisaNet" action="<?= $urlBase ?>/finalizar.php">
                            <script
                                src="<?= VISA_URL_JS ?>"
                                data-sessiontoken="<?= htmlspecialchars($sesion) ?>"
                                data-channel="web"
                                data-merchantid="<?= VISA_MERCHANT_ID ?>"
                                data-merchantlogo="<?= $urlBase ?>/assets/img/logotipo.png"
                                data-purchasenumber="<?= htmlspecialchars($purchaseNumber) ?>"
                                data-amount="<?= number_format($amount, 2, '.', '') ?>"
                                data-expirationminutes="5"
                                data-timeouturl="<?= $urlBase ?>/"
                                data-cardholderemail="<?= htmlspecialchars($email) ?>">
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer mt-auto py-1" style="background:#1c3643;">
    <div class="container py-1">
        <div class="row">
            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center order-1 order-md-0">
                <span class="text-info font-weight-bold h4">EPS EMAPAT S.A.</span>
            </div>
            <div class="col-12 col-md-6 text-white text-md-left text-center">
                <p class="h5 font-weight-bold">Dudas o Consultas</p>
                <p class="my-0 py-0">Centro de atención al cliente: 571984</p>
                <p class="my-0 py-0">Av. Ernesto Rivero 782 - Pto. Maldonado</p>
            </div>
        </div>
    </div>
</footer>

<script>
    var frmVisa = document.getElementById('frmVisaNet');
    if (document.body.contains(frmVisa)) {
        document.getElementById('frmVisaNet').setAttribute("style", "display:none");
    }
    function visaNetEc3() {
        if (document.getElementById('ckbTerms').checked) {
            document.getElementById('frmVisaNet').setAttribute("style", "display:auto");
        } else {
            document.getElementById('frmVisaNet').setAttribute("style", "display:none");
        }
    }
</script>
</body>
</html>

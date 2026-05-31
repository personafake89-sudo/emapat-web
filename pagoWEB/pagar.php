<?php
require_once('check-login.php');
ini_set('display_errors', '0');
if (isset($_POST['pagototal']) && $_POST['pagototal'] != 0 && $_POST['pagototal'] != "") {
    if (isset($_POST['dni']) && strlen($_POST['dni']) == 8 && $_POST['dni'] != "") {
    date_default_timezone_set('America/Lima');
    $codcliente = $_POST['codcliente'];
    $purchaseNumber = $_POST['nropedido'];
    $dni = $_POST['dni']; //$_SESSION['dni_session'];
    // $purchaseNumber = '1003';
    include './config/functions.php';
    $amount = $_POST['pagototal']; $comisionn = $_POST['comisionn'];
    $amount = $amount+$comisionn;
    $detallePago = "PAGO DE SERVICIO DE AGUA";
    $email = $_POST['correo'];
    $_SESSION['dni_session'] = $dni;
    $_SESSION['email_session'] = $email;
    $token = generateToken();
    $sesion = generateSesion($amount, $token, $email, $codcliente, $dni);

    $nrofact    = isset($_POST['nrofacturacion']) ? $_POST['nrofacturacion'] :  array();
    $actualizar = "";
    foreach ($nrofact as $id => $detalle) {
        $actualizar .= $detalle . ",";
    }
    $actualizar = substr($actualizar, 0, -1);

    $_SESSION['codcliente'] = $codcliente;
    $_SESSION['amount'] = $amount;
    $_SESSION['purchaseNumber'] = $purchaseNumber;
    $_SESSION['actualizar'] = $actualizar; $direccion = $_SESSION['urlpag'];
    require_once('class.Empresa.php');
?>
        <?php include('head.php');?>
        <section id="sectioncontenido" class="p-0 m-0 ">
            <!-- DIV CONTENIDO -->
            <div class="container mt-4" id="divcontenido">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-6" id="divpagovisa">
                        <div class="card mt-3 mt-md-2" style="border-radius: 15px 15px;box-shadow: 0 2px 20px #cbcbcb;">
                            <div class="card-header font-weight-bold text-center">
                                <p>Pago con Visa</p>
                                <a style="padding-left:20px;" href="assets/doc/MANUAL_01.pdf" target="_blank" class="btn btn-info"><b>Manual de uso</b></a>
                            </div>
                            <div class="card-body">
                                <p class="h5" style="padding-left:20px;">Información del pago</p>
                                <b style="padding-left:20px;">Codigo Cliente: </b><?php echo $codcliente; ?> <br>
                                <b style="padding-left:20px;">Importe a pagar: </b> S/. <?php echo number_format($amount, 2, '.', ','); ?> <br>
                                <b style="padding-left:20px;">Número de pedido: </b> <?php echo $purchaseNumber; ?> <br>
                                <b style="padding-left:20px;">Concepto: </b> <?php echo $detallePago; ?> <br>
                                <b style="padding-left:20px;">Fecha: </b> <?php echo date("d/m/Y"); ?> <br>
                                <b style="padding-left:20px;">DNI: </b> <?php echo $dni; ?> <br>
                                <hr>
                                <!-- <h3>Realiza el pago</h3> -->
                                <input type="checkbox" name="ckbTerms" id="ckbTerms" onclick="visaNetEc3()"> <label for="ckbTerms">Acepto los <a href="#" target="_blank">Términos y condiciones</a></label>
                                <form id="frmVisaNet" action="<?php echo $direccion;?>/finalizar.php">
                                    <script src="<?php echo VISA_URL_JS ?>"
                                     data-sessiontoken="<?php echo $sesion; ?>" 
                                     data-channel="web" 
                                     data-merchantid="<?php echo VISA_MERCHANT_ID ?>" 
                                     data-merchantlogo="<?php echo $direccion;?>/assets/img/logotipo.png" 
                                     data-purchasenumber="<?php echo $purchaseNumber; ?>" 
                                     data-amount="<?php echo $amount; ?>" data-expirationminutes="5" 
                                     data-timeouturl="<?php echo $direccion;?>/" data-cardholderemail="<?php echo $email; ?>">
                                    </script>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php include('foot.php');?>
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

<?php
    } else {
        echo "<center>";
        echo "INGRESAR DNI<br>";
        echo "<input type='button' onClick='history.back()' value='VOLVER'>";
        echo "</center>";
        exit;
    }

} else {
    header("Location: ./index.php");
    exit;
}
?>
<?php
require_once('check-login.php');
ini_set('display_errors', '0');
date_default_timezone_set('America/Lima');
require_once("class.Consumer.php");

$codcliente     = $_SESSION['codcliente'];
$amount         = $_SESSION['amount']; $valcomision = $_SESSION['comision'];
$purchaseNumber = $_SESSION['purchaseNumber'];
$actualizar     = $_SESSION['actualizar'];

$ip   = $_SESSION['pub'];
$user = $_SESSION['log'];
$pass = $_SESSION['pwd'];

$telefono = $_SESSION['telefono'];
$curl = new Consumer();

include 'config/functions.php';
$transactionToken = $_POST["transactionToken"];
$email = $_POST["customerEmail"];
// $purchaseNumber = generatePurchaseNumber();
$token = generateToken();
$data = generateAuthorization($amount, $purchaseNumber, $transactionToken, $token);
?>
    <?php include('head.php');?>
    <div class="container">
        <div class="row mt-3 justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title h6">Respuesta.</span>
                    </div>
                    <div class="card-body">
                        <?php

                        if (isset($data->dataMap)) {
                            if ($data->dataMap->ACTION_CODE == "000") {
                                $c = preg_split('//', $data->dataMap->TRANSACTION_DATE, -1, PREG_SPLIT_NO_EMPTY);
                        ?>
                                <div class="alert alert-success" role="alert">
                                    <strong><?php echo $data->dataMap->ACTION_DESCRIPTION; ?></strong>
                                    <p>
                                        Hemos enviado un correo electrónico a <?php echo $email; ?>, con la constancia de pago, de la operación realizada por este medio de pago.<br>
                                        Si no está en la bandeja de entrada, es posible que se encuentre en la carpeta Spam, Correo no deseado.
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>Número de pedido: </b> <?php echo $purchaseNumber; ?>
                                    </div>
                                    <?php $tarjete =  $data->dataMap->CARD . " (" . $data->dataMap->BRAND . ")"; ?>
                                    <div class="col-md-12">
                                        <b>Fecha y hora del pedido: </b> <?php echo $c[4] . $c[5] . "/" . $c[2] . $c[3] . "/" . $c[0] . $c[1] . " " . $c[6] . $c[7] . ":" . $c[8] . $c[9] . ":" . $c[10] . $c[11]; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Tarjeta: </b> <?php echo $tarjete ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Importe pagado: </b> <?php echo $data->order->amount . " " . $data->order->currency; ?>
                                    </div>
                                    <?php
                                    $rspt =  $curl->registerPayment($codcliente, $amount-$valcomision , $actualizar, $ip, $user, $pass);
                                    ?>
                                    <div class="col-md-12">
                                        <b>Codigo CLiente: </b> <?php echo $codcliente; ?>
                                    </div>

                                    <div class="col-md-12">
                                        <b>Titular de la conexión: </b> <?php echo $_SESSION['propietario']; ?>
                                    </div>

                                    <div class="col-md-12">
                                        <b>Dirección de la conexión: </b> <?php echo $_SESSION['cdireccion']; ?>
                                    </div>

                                    <div class="col-md-12">
                                        <b>Nrofacturacion: </b> <?php echo $actualizar; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Mensaje: </b> <?php echo $rspt['message']; ?>
                                    </div>
                                    <div class="col-md-12">
                                    <p style='font-family:sans-serif;color:#000;padding:10px 0px;'>
                                    Ante cualquier consulta o información adicional que usted necesite, puede comunicarse al número <b><?php echo $telefono;?></b></p>
                                    <p style='font-family:sans-serif;color:#000;padding:0 0px;'>Atentamente,<br>Oficina de Cobranza</p>
                                    </div>
                                </div>

                                <a href="home.php" class="btn btn-outline-dark">Regresar</a>
                                <a href="buscarClientes.php" class="btn btn btn-success">Realizar nuevo pago</a>
                            <?php
                                ini_set('display_errors', '1');
                                require_once 'class.user2.php';
                                $user = new USER();
                                $now = new DateTime();
                                $timestring = $now->format('d/m/Y h:i:s');
                                $message = "<table style='margin:auto;max-width:600px' border='0' cellpadding='0' cellspacing='0' valign='top'>
    <tbody>
        <tr>
            <td width='2px'>&nbsp;</td>
            <td>
                <table border='0' width='100%' valign='top' cellpadding='0' cellspacing='0'>
                    <tbody>
                        <tr>
                            <td><h2 style='margin-top:30px;font-family:sans-serif;padding:0 30px;color:#0a6486'>".$_SESSION['propietario']."</h2>
                            <p style='font-family:sans-serif;padding:0 30px;color:#0a6486'>
                            ".$_SESSION['empresa']." le informa, se procesó el registro de pago de manera satisfactoria.<br></p>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:0 30px'><br>
                                <table cellpadding='0' style='border-collapse:collapse'>
                                <tbody><tr style='background-color:#51b1c8'>
                                <td style='color:#fff;padding:10px;text-align:center;font-family:sans-serif'>&nbsp;Número de suministro&nbsp;</td>
                                <td style='color:#fff;padding:10px;text-align:center;font-family:sans-serif'>Número de pedido </td>
                                <td style='color:#fff;padding:10px;text-align:center;font-family:sans-serif'>&nbsp;&nbsp;&nbsp;&nbsp;Monto&nbsp;&nbsp;&nbsp;&nbsp;</td><td style='color:#fff;padding:10px;text-align:center;font-family:sans-serif'>Fecha de pago(*)</td>
                                </tr>
                                <tr style='background-color:#c8e9f1'>
                                <td style='color:#e80f8f;text-align:center;font-family:sans-serif;font-size:14px;padding:10px'>".$codcliente."</td>
                                <td><b>".$purchaseNumber."</b></td>
                                <td style='color:#e80f8f;text-align:center;font-family:sans-serif;font-size:14px;padding:10px'>
                                S/ ".number_format($amount, 2, ',', ' ')."</td>
                                <td style='color:#e80f8f;text-align:center;font-family:sans-serif;font-size:14px;padding:10px'>".$timestring."</td>
                                </tr>
                                </tbody>
                                </table>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <p style='font-family:sans-serif;color:#0a6486;padding:0 30px'>
                            Ante cualquier consulta o información adicional que usted necesite, puede comunicarse al número ".$telefono."</p>
                            <p style='font-family:sans-serif;color:#0a6486;padding:0 30px'>Atentamente,<br>Oficina de Cobranza</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <p style='margin:20px 0 40px;text-align:center;padding:0 30px'>
                            <div class='a6S' dir='ltr' style='opacity: 0.01; left: 521.321px; top: 887px;'>
                            </div>
                            </p>
                            <p style='box-sizing:border-box;border-top:1px solid #999998;font-family:sans-serif;text-align:center;font-size:12px;padding:20px 0 40px;color:#999998'>(*) La fecha de pago es la que figura en nuestro sistema.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width='2px'>&nbsp;</td>
        </tr>
    </tbody>
</table>";
                                $subject = "".$_SESSION['empresa'].": Confirmacion de pago de recibo ".$purchaseNumber;
                                $user->send_mail($email, $message, $subject);
                                $user->send_mail('pagosvisa@epsmoyobamba.com.pe', $message, $subject);
                            }
                        } else {
                            $c = preg_split('//', $data->data->TRANSACTION_DATE, -1, PREG_SPLIT_NO_EMPTY);
                            ?>
                            <div class="alert alert-danger" role="alert">
                                <p><?php
                                    echo $data->data->ACTION_DESCRIPTION; ?>
                                </p>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>Número de pedido: </b> <?php echo $purchaseNumber; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Fecha y hora del pedido: </b> <?php echo $c[4] . $c[5] . "/" . $c[2] . $c[3] . "/" . $c[0] . $c[1] . " " . $c[6] . $c[7] . ":" . $c[8] . $c[9] . ":" . $c[10] . $c[11]; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Tarjeta: </b> <?php echo $data->data->CARD . " (" . $data->data->BRAND . ")"; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <b>Titular de la conexión: </b> <?php echo $_SESSION['propietario']; ?>
                                    </div>

                                    <div class="col-md-12">
                                        <b>Dirección de la conexión: </b> <?php echo $_SESSION['cdireccion']; ?>
                                    </div>

                                    <div class="col-md-12">
                                        <b>Importe pagado: </b> S/ 0.00
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row">
                                <p style='font-family:sans-serif;color:#000;padding:0 30px'>
                                Ante cualquier consulta o información adicional que usted necesite, puede comunicarse al número <b><?php echo $telefono;?></b></p>
                                <p style='font-family:sans-serif;color:#000;padding:0 30px'>Atentamente,<br>Oficina de Cobranza</p>
                                </div>
                                <a href="home.php" class="btn btn-outline-dark">Regresar</a>
                                <a href="buscarClientes.php" class="btn btn btn-success">Realizar nuevo pago</a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('foot.php');?>
</body>
</html>

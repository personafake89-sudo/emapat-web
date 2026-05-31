<?php 
require_once('includes/connect.php');
require_once('check-login.php');
require_once("class.Consumer.php");
ini_set('display_errors', '0');
$curl = new Consumer();
$codigo = $_SESSION['user_session'];
$correo = $_SESSION['email_session'];
$nomemp  = $_SESSION['empresa'];
$ip  = $_SESSION['pub'];
$user  = $_SESSION['log'];
$pass  = $_SESSION['pwd'];
$nropedido = '';
foreach ($curl->vereficarCodigo($codigo, $ip, $user, $pass) as $key => $value) {
    if (isset($value['cliente']) and is_numeric($value['codcliente'])) {

        $codcliente = $value['codcliente'];
        $propietario = $value['cliente'];
        $tdir = $value['tdir'];
        $dir = $value['dir'];
        $nrocalle = $value['nrocalle'];
        $codsuc = $value['codsuc'];
        $nropedido = $value['nropedido'];
        $nomsuc = $value['nomsuc'];
    } else {
        $error = "Codigo sin Informacion !";
        exit();
    }
    $_SESSION['propietario'] = $propietario;
}
?>
    <?php include('head.php');?>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title h6">Datos Cliente.</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <span class="font-weight-bold">Codigo :</span> <?php echo $codcliente  ?>
                            </div>
                            <div class="col-12 col-lg-6">
                                <span class="font-weight-bold">Localidad :</span> <?php echo $nomsuc  ?>
                            </div>
                            <div class="col-12 col-lg-6">
                                <span class="font-weight-bold">Titular :</span> <?php echo $propietario  ?>
                            </div>
                            <div class="col-12 col-lg-6">
                                <span class="font-weight-bold">Dirección :</span> <?php echo $tdir . ' ' . $dir . ' ' . $nrocalle; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title h6">RELACIÓN DE PAGOS EFECTUADOS</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-sm">
                              <thead class="thead-dark">
                                <tr>
                                    <th>Id</th>
                                    <th>COMPROBANTE</th>
                                    <th>DESCRIPCION</th>
                                    <th>FECHA PAGO</th>
                                    <th>IMPORTE</th>
                                    <th>LUGAR DE PAGO</th>
                                    <th>CAJERO</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $ij3 =1;
                                foreach ($curl->vereficarPagos('001', $codsuc , $codigo , $ip , $user , $pass) as $key5=>$value5)
                                {
                                if(isset($value5['nropago'])) {
                                  ?>
                                  <tr>
                                    <td><?php echo $ij3++; ?></td>
                                    <td><?php echo $value5['seriedoc'].'-'.$value5['nrodoc']; ?></td>
                                    <td><?php echo $value5['detalle']; ?></td>
                                    <td><?php echo $value5['diapago']; ?></td>
                                    <td align="right" class="table-success"><?php echo number_format($value5['imptotal'],2); ?></td>
                                    <td><?php echo $value5['car']; ?></td>
                                    <td><?php echo $value5['codusu']; ?></td>
                                  </tr>
                                <?php } else { ?>
                                  <tr>
                                    <td colspan="8">No Existen Pagos registrados.</td>
                                  </tr>
                                <?php } ?>
                                <?php } ?>
                              </tbody>
                            </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <a href="home.php" class="btn btn-info"><b>Volver</b></a>
                            <a href="logout.php?logout=true" class="btn btn-danger"><b>Salir</b></a>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('foot.php');?>

    <script src="./assets/js/jquery-3.3.1.min.js"></script>
</body>

</html>
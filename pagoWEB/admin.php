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
    $_SESSION['cdireccion'] = $tdir . ' ' . $dir . ' ' . $nrocalle;
}
?>
<?php include('head.php'); ?>
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
                <form action="./pagar.php" method="POST">
                    <input type='hidden' name='correo' value='<?php echo $correo; ?>' />
                    <input type='hidden' name='codcliente' value='<?php echo $codcliente; ?>' />
                    <input type='hidden' name='nropedido' value='<?php echo $nropedido; ?>' />
                    <div class="card-header">
                        <span class="card-title h6">Estado de cuenta.</span>
                    </div>

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Nota:</strong> Desactivar  la casilla del periodo de deuda que no pueda cancelar en caso tenga inconvenientes en pagar la totalidad de la deuda.
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-sm" id="miTabla" >
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Período</th>
                                        <th scope="col">Recibo</th>
                                        <th scope="col"  style=" text-align: right;">Total Mes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ij = 1;
                                    $tot_01 = 0; // flagreclamo
                                    $tot_02 = 0;
                                    foreach ($curl->vereficarDeuda('001', $codsuc, $codigo, $ip, $user, $pass) as $key2 => $value2) {
                                        if (isset($value2['impmestotal'])) {
                                            if ($value2['flagreclamo'] == 0) {
                                                $tot_01 += $value2['impmestotal'];
                                                $tot_02 += $value2['impmestotal'];
                                            }
                                    ?>
                                            <tr <?php if ($value2['flagreclamo'] == 1) {
                                                    echo 'style="background-color: #F1948A;"';
                                                } ?>>
                                                <td>
                                                    <!-- <input type=hidden name='nrofacturacion[]' value="<?php echo $value2['nrofacturacion']; ?>"> -->
                                                    <?php if ($value2['nrodoc'] != '0') {  ?>
                                                        <!-- <button class="btn btn-danger" id="btnborrar">X</button> -->
                                                        <input type="checkbox" name="nrofacturacion[]" checked class="qwerty form-check-input" id="<?php echo number_format($value2['impmestotal'], 2); ?>" value="<?php echo $value2['nrofacturacion']; ?>">
                                                    <?php       } else { ?>
                                                        
                                                        <input type="hidden" name="nrofacturacion[]" checked class="qwerty form-check-input" id="<?php echo number_format($value2['impmestotal'], 2); ?>" value="<?php echo $value2['nrofacturacion']; ?>">

                                                    <?php    } ?>
                                                </td>
                                                <td><?php echo $curl->nombremes($value2['mes']) . ' - ' . $value2['anio']; ?></td>
                                                <td><?php echo $value2['seriedoc'] . ' - ' . $value2['nrodoc']; ?></td>
                                                <td align="right"><?php echo number_format($value2['impmestotal'], 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td align="right" style="color: #1B4F72;"><b>TOTAL DEUDA</b></td>
                                                <td align="right" style="color: #1B4F72;"><b><?php echo number_format($tot_02, 2); ?></b></td>
                                            </tr>
                                </tbody>
                                <tfoot style="font-size: 24px;">
                                    <tr>
                                        <th><input type="hidden" name='pagototal' id="pagototal" /></th>
                                        <th style="text-align: right;">TOTAL A PAGAR: </th>
                                        <th style="text-align: left;"><?php echo number_format($tot_01, 2); ?></th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="text-center">
                        <?php if ($tot_01 == '0') {
                        } else { ?>
                            <button class="btn btn-success btn-lg" type="submit" id="btnpagar"><b>REALIZAR PAGO</b></button>
                        <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <hr>
                        <a href="verPagos.php" class="btn btn-info"><b>Ver pagos</b></a>
                        <!-- <button class="btn btn-warning" id="btnactualizar"><b>Actualizar</b></button> -->
                        <a href="buscarClientes.php" class="btn btn-dark"><b>Nueva busqueda</b></a>
                        <a href="logout.php?logout=true" class="btn btn-danger"><b>Salir</b></a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php include('foot.php'); ?>
<script src="./assets/js/icheck.min.js"></script>
<script>
    function calcular() {
        var pagtotal = 0;
        $('.qwerty').each(function() {
            if (this.checked) {
                pagtotal += parseFloat($(this).attr("id"));
            }
        });
        $('#pagototal').val(pagtotal.toFixed(2));


        if (pagtotal == 0) {
            $('#btnpagar').addClass('d-none');
        } else {
            $('#btnpagar').removeClass('d-none');
        }

        // mostramos la suma total
        var filas = document.querySelectorAll("#miTabla tfoot tr th");
        filas[2].textContent = pagtotal.toFixed(2);
       
    }
    $('#pagototal').val(<?php echo number_format($tot_01, 2); ?>);

    $('input[type="checkbox"], input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    })
    $('input[type="checkbox"]').on('ifChanged', function(event) {
        calcular();
    });
</script>
</body>

</html>
<?php
    require_once('check-login.php');
    require_once("class.Consumer.php");

    require_once('class.Empresa.php');
    require_once("class.Visa.php");

    ini_set('display_errors', '1');

    $curl           = new Consumer();
    $visa           = new Visa();

    $codigo         = $_SESSION['user_session'];
    $correo         = $_SESSION['email_session'];
    $docide         = $_SESSION['dni_session'];
    $nomemp         = $_SESSION['empresa'];
    $ip             = $_SESSION['pub'];
    $user           = $_SESSION['log'];
    $pass           = $_SESSION['pwd'];
    $valcomision    = $_SESSION['comision'];
    $nroordensys    = $_SESSION['nroordensys'];
    $checkfactmes   = $_SESSION['checkfactmes'];
    $nropedido      = '';

    $datosVisa = $visa->generar_pedido(array("codcliente" => $codigo,"dni" => $docide,"correo" => $correo ));
    if(isset($datosVisa->codcliente) && is_numeric($datosVisa->codcliente)){
        $codcliente     = $datosVisa->codcliente;
        $propietario    = $datosVisa->cliente;
        $tdir           = $datosVisa->tdir;
        $dir            = $datosVisa->dir;
        $nrocalle       = $datosVisa->nrocalle;
        $codsuc         = $datosVisa->codsuc;
        $nropedido      = $datosVisa->nropedido;
        $nomsuc         = $datosVisa->nomsuc;
    }else{
        $error = "Codigo sin Informacion !";
        exit();
    }

    $_SESSION['propietario']    = $propietario;
    $_SESSION['cdireccion']     = $tdir . ' ' . $dir . ' ' . $nrocalle;

    // foreach ($curl->generaOrden($codigo , $docide ,   $correo , $ip, $user, $pass) as $key => $value) {
    //     if (isset($value['cliente']) and is_numeric($value['codcliente'])) {

    //         $codcliente = $value['codcliente'];
    //         $propietario = $value['cliente'];
    //         $tdir = $value['tdir'];
    //         $dir = $value['dir'];
    //         $nrocalle = $value['nrocalle'];
    //         $codsuc = $value['codsuc'];
    //         $nropedido = $value['nropedido'];
    //         $nomsuc = $value['nomsuc'];
            
    //     } else {
    //         $error = "Codigo sin Informacion !";
    //         exit();
    //     }
    //     $_SESSION['propietario'] = $propietario;
    //     $_SESSION['cdireccion'] = $tdir . ' ' . $dir . ' ' . $nrocalle;
    // }
?>
<?php include('head.php'); ?>
<style type="text/css">
.form-control::-webkit-input-placeholder {
  color: #34495E;
}
.form-control{border-width: 2px;  color: #17202A; font-weight: bold;}  
</style>
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
                <form action="./pagar.php" method="POST"  name="frm" id="frm">
                    <input type='hidden' name='codcliente' value='<?php echo $codcliente; ?>' />
                    <input type='hidden' name='nropedido' value='<?php echo $nropedido; ?>' />
                    <div class="card-header">
                        <span class="card-title h6">Estado de cuenta.</span>
                    </div>
                    <?php if($checkfactmes == 'SI'){ ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Nota:</strong> Desactivar  la casilla del periodo de deuda que no pueda cancelar.
                    </div>
                    <?php } ?>
                    <?php
                    // Pre-cargar deuda para calcular meses y detectar último período (afecto al corte)
                    $rowsDeuda    = $visa->recupear_deuda("001", $codigo);
                    $rowsArray    = [];
                    $maxPeriod    = 0;
                    $nroCorte     = null; // nrofacturacion del período más reciente

                    foreach ($rowsDeuda as $r) {
                        if (isset($r->impmestotal)) {
                            $rowsArray[] = $r;
                            $period = intval($r->anio) * 100 + intval($r->mes);
                            if ($period > $maxPeriod) {
                                $maxPeriod = $period;
                                $nroCorte  = $r->nrofacturacion;
                            }
                        }
                    }
                    $mesesDeuda = count($rowsArray);
                    ?>
                    <?php if ($mesesDeuda > 0): ?>
                    <div class="alert alert-warning mb-0 py-2 px-3" role="alert" style="border-radius:0; border-left: 4px solid #e67e22;">
                        <strong>Meses en deuda: <?php echo $mesesDeuda; ?></strong>
                        &nbsp;|&nbsp; El período más reciente está <span class="badge badge-danger">AFECTO AL CORTE</span>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-sm" id="miTabla" >
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Período</th>
                                        <th scope="col">Recibo</th>
                                        <th scope="col" style="text-align: right;">Total Mes</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ij    = 1;
                                    $tot_01 = 0;
                                    $tot_02 = 0;
                                    foreach ($rowsArray as $rows):
                                        $tot_01 += $rows->impmestotal;
                                        $tot_02 += $rows->impmestotal;
                                        $esCorte = ($rows->nrofacturacion == $nroCorte);
                                        $rowStyle = '';
                                        if ($esCorte)            $rowStyle = 'background-color: #FDEBD0;';
                                        elseif ($rows->flagreclamo == 1) $rowStyle = 'background-color: #F1948A;';
                                    ?>
                                            <tr style="<?php echo $rowStyle; ?>">
                                                <td>
                                                    <?php if (($rows->nrodoc != '0') && ($checkfactmes == 'SI')): ?>
                                                        <input type="checkbox" name="nrofacturacion[]" checked class="qwerty form-check-input" id="<?php echo $rows->impmestotal; ?>" value="<?php echo $rows->nrofacturacion; ?>">
                                                    <?php else: ?>
                                                        <input type="hidden" name="nrofacturacion[]" class="qwerty form-check-input" id="<?php echo $rows->impmestotal; ?>" value="<?php echo $rows->nrofacturacion; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $curl->nombremes($rows->mes) . ' - ' . $rows->anio; ?></td>
                                                <td><?php echo $rows->seriedoc . ' - ' . $rows->nrodoc; ?></td>
                                                <td align="right"><strong>S/ <?php echo number_format($rows->impmestotal, 2); ?></strong></td>
                                                <td>
                                                    <?php if ($esCorte): ?>
                                                        <span class="badge badge-danger">AFECTO AL CORTE</span>
                                                    <?php elseif ($rows->flagreclamo == 1): ?>
                                                        <span class="badge badge-warning">EN RECLAMO</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">PENDIENTE</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php endforeach; ?>
                                            <tr>
                                                <td align="right" style="color: #1B4F72;" colspan="4"><b>TOTAL DEUDA (<?php echo $mesesDeuda; ?> mes<?php echo $mesesDeuda != 1 ? 'es' : ''; ?>)</b></td>
                                                <td align="right" style="color: #1B4F72;"><b>S/ <?php echo number_format($tot_02, 2); ?></b></td>
                                            </tr>

                                            <?php if($valcomision > 0 ) { ?>
                                            <tr>
                                                <td align="right" style="color: #1B4F72;" colspan="3"><b>Comisión Niubiz</b></td>
                                                <td align="right" style="color: #1B4F72;"><b><?php echo  $valcomision; ?></b></td>
                                            </tr>
                                            <?php } ?>
                                </tbody>
                                <tfoot style="font-size: 24px;">
                                    <tr>
                                        <th>
                                            <input type="hidden" name='pagototal' id="pagototal" />
                                            <input type="hidden" name='comisionn' id="comisionn" value = "<?php echo  $valcomision; ?>" />
                                        </th>
                                        <th colspan="2" style="text-align: right;">TOTAL A PAGAR: </th>
                                        <th style="text-align: left;">S/ <?php echo number_format($tot_01+$valcomision,2); ?></th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                            <div class="text-center">
                        <?php if ($tot_01 == '0') {  } else { ?>
                            <div class="form-group" align="center">
                            <?php if (isset($correo) && $correo != "") { ?>
                                <input type='hidden' name='correo' value='<?php echo $correo; ?>' >
                            <?php } else { ?>
                                <div class="col-sm-6">
                                    <input type='email' class="form-control" name='correo' value='<?php echo $correo; ?>' placeholder="Digite su correo electrónico valido" required="required" />
                                </div>
                            <?php } ?>
                            </div>

                            <div class="form-group" align="center">
                            <?php if (isset($docide) && $docide != "") { ?>
                                <input type='hidden' name='dni' value='<?php echo $docide; ?>' >
                            <?php } else { ?>
                                <div class="col-sm-6">
                                    <input type='number' class="form-control" name='dni' id="dni" value='<?php echo $docide; ?>' placeholder="Digite su DNI valido" maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required="required" />
                                </div>
                            <?php } ?>
                            </div>
                            
                            <div class="form-group">
                                <button class="btn btn-success btn-lg" type="button" id="btnpagar"
                                    data-toggle="modal" data-target="#modalConfirmarPago">
                                    <b>💳 REALIZAR PAGO</b>
                                </button>
                            </div>
                        <?php } ?>
                            </div>

                    </div>
                    <div class="card-footer text-center">
                        <hr>
                        <a href="verPagos.php" class="btn btn-info" style="margin: 0px 10px 5px 0pc; "><b>Ver pagos</b></a>
                        <!-- <button class="btn btn-warning" id="btnactualizar"><b>Actualizar</b></button> -->
                        <a href="buscarClientes.php" class="btn btn-dark" style="margin: 0px 10px 5px 0pc; "><b>Nueva busqueda</b></a>
                        <a href="logout.php?logout=true" class="btn btn-danger" style="margin: 0px 10px 5px 0pc; "><b>Salir</b></a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php include('foot.php'); ?>

<!-- ── Modal de confirmación de pago ────────────────────────────────────── -->
<div class="modal fade" id="modalConfirmarPago" tabindex="-1" role="dialog"
     aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header" style="background:#1c3643;">
                <h5 class="modal-title text-white" id="modalConfirmarLabel">
                    Confirmar Pago
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">Titular</td>
                        <td><strong><?php echo htmlspecialchars($propietario); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Código</td>
                        <td><?php echo $codcliente; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dirección</td>
                        <td><?php echo htmlspecialchars($tdir . ' ' . $dir . ' ' . $nrocalle); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Meses en deuda</td>
                        <td><span class="badge badge-warning"><?php echo $mesesDeuda; ?> mes<?php echo $mesesDeuda != 1 ? 'es' : ''; ?></span></td>
                    </tr>
                    <?php if ($valcomision > 0): ?>
                    <tr>
                        <td class="text-muted">Comisión Niubiz</td>
                        <td>S/ <?php echo number_format($valcomision, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <hr>
                <div class="text-center">
                    <span class="text-muted">Total a pagar</span><br>
                    <span class="display-4 font-weight-bold text-success" id="modalTotal">
                        S/ <?php echo number_format($tot_01 + $valcomision, 2); ?>
                    </span>
                </div>
                <div class="alert alert-info mt-3 mb-0 py-2 text-center" style="font-size:13px;">
                    Serás redirigido a la plataforma segura de <strong>Niubiz/Visa</strong> para completar el pago.
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btnConfirmarPagar">
                    <b>✓ Confirmar y Pagar</b>
                </button>
            </div>

        </div>
    </div>
</div>
<!-- ──────────────────────────────────────────────────────────────────────── -->

<script src="./assets/js/icheck.min.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>
<script>
    function calcular() {
        var pagtotal = 0;
        var comision = <?php echo $valcomision; ?>;
        $('.qwerty').each(function() {
            if (this.checked) {
                pagtotal = pagtotal + parseFloat($(this).attr("id"));
            }
        });
        $('#pagototal').val(pagtotal.toFixed(2));

        if (pagtotal == 0) {
            $('#btnpagar').addClass('d-none');
        } else {
            $('#btnpagar').removeClass('d-none');
        }

        var total = pagtotal + parseFloat(comision);
        // Actualiza el total en el tfoot
        var filas = document.querySelectorAll("#miTabla tfoot tr th");
        filas[2].textContent = total > comision ? total.toFixed(2) : 0;

        // Actualiza el total mostrado en el modal en tiempo real
        $('#modalTotal').text('S/ ' + (total > 0 ? total.toFixed(2) : '0.00'));
    }

    $('#pagototal').val(<?php echo $tot_01; ?>);

    $('input[type="checkbox"], input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('input[type="checkbox"]').on('ifChanged', function() {
        calcular();
    });

    // Abrir modal: validar DNI antes de mostrar
    $('#btnpagar').on('click', function() {
        var dni = $('#dni').val();
        // Solo valida si el campo DNI es visible (no hidden)
        if ($('#dni').attr('type') !== 'hidden' && dni.length !== 8) {
            alert('Ingrese un número de DNI válido (8 dígitos)');
            $('#dni').focus();
            return;
        }
        $('#modalConfirmarPago').modal('show');
    });

    // Confirmar → enviar el form real
    $('#btnConfirmarPagar').on('click', function() {
        $('#modalConfirmarPago').modal('hide');
        document.getElementById('frm').submit();
    });
</script>
</body>

</html>


<?php 
require_once('includes/connect.php');
require_once('check-login.php');
require_once("class.Consumer.php");
ini_set('display_errors', '0');
$curl = new Consumer();
$codigo = $_SESSION['user_session'];
$correo = $_SESSION['email_session'];
$nomemp  = $_SESSION['empresa'];
$ip    = $_SESSION['pub'];
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
                <div class="card mb-4">
                    <form method="POST" id="frm">
                    <div class="container-md px-2">
                        <div><!-- class="shadow-lg mb-5 bg-white rounded card" -->
                            <div class="card-header" style="padding: 8px;">
                                <h5 style="margin-bottom: 0px;"><b>búsqueda de Suministro</b></h5>
                            </div>

                            <div class="card-body" style="padding: 8px;">
                                <div class="card-title"></div>
                                <div class="alert alert-primary fade show" role="alert"> Puedes consultar los Suministros que tienes con nosotros y si deseas puedes realizar el pago.</div>
                                <div class="row" align="center">
                                    <div class="col">
                                        <div class="text-center">
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd3" name="rdTipoBusqueda" class="custom-control-input" checked="" value="1">
                                                <label class="custom-control-label" for="rd3">Suministro</label>
                                            </div>

                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd1" name="rdTipoBusqueda" class="custom-control-input" value="2">
                                                <label class="custom-control-label" for="rd1">DNI</label>
                                            </div>

                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd2" name="rdTipoBusqueda" class="custom-control-input" value="3">
                                                <label class="custom-control-label" for="rd2">Apellidos y Nombres</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col"></div>
                                    <div class="col-sm-6">
                                        <div class="text-center">
                                        <div class="input-group">
                                            <input autocomplete="nope" placeholder="Ingrese el número de Suministro" type="tel" class="form-control" name="codvalor" id="codvalor" value="">
                                            <div class="input-group-prepend">
                                            </div>
                                        <div class="invalid-feedback"></div>
                                        </div>
                                        </div>
                                    </form>
                                    </div>

                                    <div class="col"></div>
                                </div>
                                <br>
                            </div>

                            <div class="card-footer" style="padding: 8px;">
                                <div class="text-center">
                                    <input type="submit" name="enviar" class="btn btn-primary" value="Consultar Suministro">
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                <div id="loading" style="display:none;  ">Calculando...</div>
                <div id="response" style="margin-top: 10px; "></div>

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
    <script type="text/javascript">
  $(function(){
    $("#frm").submit(function(){
    var codvalor  = $('#codvalor').val();
    var tipo      = $("input:radio[name=rdTipoBusqueda]:checked").val();
    $.ajax({
      type:"POST",
      url:"ajax_buscarClientes.php",
      dataType:"html",
      data: {
              'codvalor':codvalor,
              'tipo':tipo
            },
      beforeSend:function(){
        $("#loading").show();
      },
      success:function(response){
        $("#response").html(response);
        $("#loading").hide();
      }
      });
      return false;
    });

    $("#codvalor").click(function(){ 
        $('#codvalor').val("");
    });
});
  </script>
</body>

</html>
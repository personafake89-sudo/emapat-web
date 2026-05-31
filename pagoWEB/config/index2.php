<?php
ini_set('display_errors', '0');
require_once('class.Empresa.php');
$nomemp  = Empresa::EMPRESA; $urlPagina = Empresa::URLPAGE; $telefono = Empresa::TELEFON; $direccion = Empresa::DIRECCI;
?>
<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--  CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="assets/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <title><?php echo Empresa::EMPRESA;?></title>
</head>

<body>
    <section class="fxt-template-animation fxt-template-layout1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-12 fxt-bg-color">
                    <div  >
                        <div class="fxt-form">
                            <h2><?php echo Empresa::EMPRESA;?></h2>
                            <p>¡Bienvenido, pague su recibo!</p>
                            <?php
                            if (isset($error)) {
                            ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error; ?>!
                                </div>
                            <?php
                            }
                            ?>
                            <!--<p>Estimado cliente, desde ahora Ud. puede pagar sus recibos desde la comodidad de su hogar, oficina o desde cualquier parte del mundo.</p>-->

                            <div class="fxt-transformY-50 fxt-transition-delay-2">
                                <div class="alert alert-dark" role="alert"> 
                                Puedes consultar los Suministros que tienes con nosotros y si deseas puedes realizar el pago.
                                </div>

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
                            </div>

                            <div class="form-group" style="margin-top: 3%;">
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <input autocomplete="off" type="text" class="form-control" name="codvalor" id="codvalor" placeholder="Ingresar Suministro" required="required">
                                        <i class="flaticon-user"></i>
                                </div>
                            </div>

                            <div class="form-group" style="padding: 8px;">
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="calc" >Consultar Suministro</button>
                                    </div>
                                </div>
                            </div>

                        </div>

                            <div class="fxt-form" align="center">
                                <div id="loading" style="display:none;  ">Calculando...</div>
                                <div id="response" style="margin-top: 10px; "></div>
                            </div>

                        <div class="fxt-footer">
                            <p class="alert alert-dark" role="alert">
                                 Aceptamos tarjetas de crédito y debito.
                            </p>
                            <ul class="fxt-socials">
                                <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-4">
                                    <img src="assets/img/figure/niubus-logo-final_2.jpg" alt="Logo2">
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-6 col-12 fxt-none-767 fxt-bg-img" data-bg-image="assets/img/figure/bg1-l.jpg"></div>-->
            </div>
        </div>
    </section>



    <!-- jquery-->
    <script src="assets/js/jquery-3.5.0.min.js"></script>
    <!-- Popper js -->
    <script src="assets/js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Imagesloaded js -->
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- Validator js -->
    <script src="assets/js/validator.min.js"></script>
    <!-- Custom Js -->
    <script src="assets/js/main.js"></script>
    <script type="text/javascript">
      $(function(){
        //$("#frm").submit(function(){
        $('#codvalor').focus();
        $('#calc').click(function() {
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
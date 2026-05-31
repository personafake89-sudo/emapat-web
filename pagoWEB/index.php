<?php
    if(!session_start()){session_start();}

    ini_set('display_errors', '2');
    require_once('class.Empresa.php');
    // require_once('class.Consumer.php');
    require_once('class.Clientes.php');

    $emp                = new Empresa();
    $clientes           = new Clientes();

    $error              = null;
    $ip                 = Empresa::IPPUBLI;
    $user               = Empresa::_COMPTE;
    $pass               = Empresa::_PASSED;
    $nomemp             = $emp->nombreEmpresa(); 
    $urlPagina          = Empresa::URLPAGE; 
    $telefono           = $emp->telefonoEmpresa(); 
    $direccion          = $emp->direccionEmpresa(); 
    $comision           = Empresa::COMISIO; 
    $checkfactmes       = Empresa::CHECMES;
    $urlPagina          = 'https://'.$_SERVER["HTTP_HOST"];
    $_SESSION['token']  = $emp->tokenSistema(); 
    // $curl               = new Consumer();

    $codigo             = isset($_POST['codigo']) ? $_POST['codigo'] : "";
    $correo             = isset($_POST['email']) ? $_POST['email'] : "emaq@gmail.com";
    $dni                = isset($_POST['dni']) ? $_POST['dni'] : "12345678";
    $cod                = isset($_GET['id']) ? base64_decode($_GET['id']) : '';

    if ($codigo != "") {
        $codigo         = strip_tags($_POST['codigo']);
        $datosClientes  = $clientes->recupera_datos_clientes($codigo);
        if($datosClientes != null){
            if(isset($datosClientes->codcliente)){
                $_SESSION['user_session']   = $codigo;
                $_SESSION['email_session']  = $correo;
                $_SESSION['dni_session']    = $dni;
                $_SESSION['pub']            = $ip;
                $_SESSION['log']            = $user;
                $_SESSION['pwd']            = $pass;
                $_SESSION['empresa']        = $nomemp;
                $_SESSION['urlpag']         = $urlPagina;
                $_SESSION['telefono']       = $telefono;
                $_SESSION['direccion']      = $direccion;
                $_SESSION['login']          = true;
                $_SESSION['id']             = $codigo;
                $_SESSION['last_login']     = time();
                $_SESSION['comision']       = $comision;
                $_SESSION['nroordensys']    = 0;
                $_SESSION['checkfactmes']   = $checkfactmes;

                header("Location: home.php");
            }else{
                $error = "Codigo sin Informacion !";
            }
        }else{
            $error = "Codigo de Cliente, no Existe !";
        }
        // if ($curl->vereficarCodigo($codigo, $ip, $user, $pass)) {
        //     foreach ($curl->vereficarCodigo($codigo, $ip, $user, $pass) as $key => $value) {
        //         if (isset($value['cliente'])) {
        //             $_SESSION['user_session'] = $codigo;
        //             $_SESSION['email_session'] = $correo;
        //             $_SESSION['dni_session'] = $dni;
        //             $_SESSION['pub'] = $ip;
        //             $_SESSION['log'] = $user;
        //             $_SESSION['pwd'] = $pass;
        //             $_SESSION['empresa'] = $nomemp;
        //             $_SESSION['urlpag'] = $urlPagina;
        //             $_SESSION['telefono'] = $telefono;
        //             $_SESSION['direccion'] = $direccion;
        //             $_SESSION['login'] = true;
        //             $_SESSION['id'] = $codigo;
        //             $_SESSION['last_login'] = time();
        //             $_SESSION['comision'] = $comision;
        //             $_SESSION['nroordensys'] = 0;
        //             $_SESSION['checkfactmes'] = $checkfactmes;
        //             $curl->redirect('home.php');
        //         } else {
        //             $error = "Codigo sin Informacion !";
        //         }
        //     }
        // } else {
        //     $error = "Codigo de Cliente, no Existe !";
        // }
    }
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
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="assets/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <title><?php echo $emp->nombreEmpresa();?></title>
</head>

<body>
    <section class="fxt-template-animation fxt-template-layout1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-12 fxt-bg-color">
                <div class="col-xs-8 col-sm-4 col-md-6 fxt-content">
                    <img src="assets/img/visa2021.png" alt="flayer" >
                </div>

                    <div class="fxt-content">
                        <div class="fxt-form">
                            <h2><?php echo $emp->nombreEmpresa();?></h2>
                            <span>¡Bienvenido, pague su recibo!</span>
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

                            <form method="POST" onSubmit="return validar();">

                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <div id="codigo1"></div>
                                        <input   type="number" class="form-control" name="codigo" id="codigo" placeholder="Ingresar Código de suministro" required="required" style="background-color: #EAFAF1; color: #080909;"  value="<?php echo $cod;?>">
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input autocomplete="off" type="email" class="form-control" name="email" id="email" placeholder="Ingresar Correo electrónico"  title="Llenar el email valido" required="required">
                                        <i class="flaticon-envelope"></i>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input autocomplete="off" type="number" class="form-control" name="dni" id="dni" placeholder="Ingresar Número de DNI" required="required" maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" >
                                        <i class="flaticon-eye"></i>
                                    </div>

                                    <div class="checkbox" style="margin-top: 8px;">
                                        <input id="checkbox1" type="checkbox" required="required" class="filled-in" title="Marcar los Términos y Condiciones">
                                        <label for="checkbox1">
                                            Acepto los
                                            <a href="#" target="_blank"> Términos y Condiciones</a>
                                        </label>
                                    </div>

                                    <div class="fxt-content-between">
                                        <button type="submit" class="fxt-btn-fill">CONSULTAR</button>
                                        <!--<a href="register.php" class="fxt-btn-fill2">¿Aún no estás registrado?</a>-->
                                    </div>
                                    <!--
                                    <div class="alert alert-dark" role="alert">
                                    Puedes consultar los recibos que tienes y si deseas puedes realizar el pago, aceptamos tarjetas de crédito y debito.
                                    </div>
                                    -->
                                    <ul class="fxt-socials">
                                        <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-4">
                                            <img src="assets/img/figure/niubus-logo-final_2.jpg" alt="Logo2">
                                        </li>
                                    </ul>

                                </div>

                            </form>
                        </div>

                    </div>
                </div>
                <!--<div class="col-md-6 col-12 fxt-none-767 fxt-bg-img" data-bg-image="assets/img/figure/bg1-l.jpg"></div>-->
            </div>
        </div>
    </section>

    <!-- Large modal -->

<div class="modal fade " id="myModal" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <p>Enter text:</p>
        <input type="text" id="inscri">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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
    <script>
    function validar()
    {
        nombre=$('#dni').val();
        if (nombre.length == 8){
            return true;
        }
        else {
            alert('Llenar nurmero de DNI Valido');
            $('#dni').focus();
            return false;
        }

        if ($('input[type=checkbox]:checked').length === 0) { alert('No has aceptado los términos y condiciones'); return false; }
    }


$(function() {
  $('#btnLaunch').click(function() {
    //$('#myModal').modal('show');
      $('.modal-body').load('buscarClientes_ini.php',function(){
          $('#myModal').modal({show:true});
      });

  });

  //$('#btnSave').click(function() {
 $(document).on('click', '.info', function(){
    var _id=$(this).val();
    var value = _id; //$('#inscri').val();
    $('#codigo').val(value);
    $('#myModal').modal('hide');
  });
});
    </script>
</body>

</html>

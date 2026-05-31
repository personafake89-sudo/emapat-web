<?php
session_start();
ini_set('display_errors', '0');
require_once('class.Empresa.php');
require_once('class.Consumer.php');
$error = null;
$ip   = Empresa::IPPUBLI;
$user = Empresa::_COMPTE;
$pass = Empresa::_PASSED;
$nomemp  = Empresa::EMPRESA; $urlPagina = Empresa::URLPAGE; $telefono = Empresa::TELEFON; $direccion = Empresa::DIRECCI;
$comision = Empresa::COMISIO;
$urlPagina = 'https://'.$_SERVER["HTTP_HOST"].'/pagoseguro';
$curl = new Consumer();

$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : "";
$correo = isset($_POST['email']) ? $_POST['email'] : "ilo@gmail.com";
$dni = isset($_POST['dni']) ? $_POST['dni'] : "12345678";
$cod = isset($_GET['id']) ? base64_decode($_GET['id']) : '';
if ($codigo != "") {
    $codigo   = strip_tags($_POST['codigo']);
    //$apellido = strip_tags($_POST['apellido']);
    if ($curl->vereficarCodigo($codigo, $ip, $user, $pass)) {
        foreach ($curl->vereficarCodigo($codigo, $ip, $user, $pass) as $key => $value) {
            if (isset($value['cliente'])) {
                $_SESSION['user_session'] = $codigo;
                $_SESSION['email_session'] = $correo;
                $_SESSION['dni_session'] = $dni;
                $_SESSION['pub'] = $ip;
                $_SESSION['log'] = $user;
                $_SESSION['pwd'] = $pass;
                $_SESSION['empresa'] = $nomemp;
                $_SESSION['urlpag'] = $urlPagina;
                $_SESSION['telefono'] = $telefono;
                $_SESSION['direccion'] = $direccion;
                $_SESSION['login'] = true;
                $_SESSION['id'] = $codigo;
                $_SESSION['last_login'] = time();
                $_SESSION['comision'] = $comision;
                $_SESSION['nroordensys'] = 0;
                $curl->redirect('home.php');
            } else {
                $error = "Codigo sin Informacion !";
            }
        }
    } else {
        $error = "Codigo de Cliente, no Existe !";
    }
}
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Xmee | Login and Register Form Html Templates</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->     
    <section class="fxt-template-animation fxt-template-layout16">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-sm-12 col-12 fxt-bg-color">
                    <div class="fxt-content">
                        <div class="fxt-header">
                            <a href="login-16.html" class="fxt-logo"><img src="assets/img/logo-16.png" alt="Logo"></a>                        
                            <p>Register for create an account</p>
                        </div>                            
                        <div class="fxt-form"> 
                            <form method="POST">
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="text" id="name" class="form-control" name="name" placeholder="Name" required="required">
                                    </div>
                                </div>
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="email" id="email" class="form-control" name="email" placeholder="Email" required="required">
                                    </div>
                                </div>
                                <div class="form-group">  
                                    <div class="fxt-transformY-50 fxt-transition-delay-2">                                              
                                        <input id="password" type="password" class="form-control" name="password"  placeholder="******" required="required">
                                        <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">  
                                        <div class="fxt-checkbox-area">
                                            <div class="checkbox">
                                                <input id="checkbox1" type="checkbox">
                                                <label for="checkbox1">Keep me logged in</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">  
                                        <button type="submit" class="fxt-btn-fill">Log in</button>
                                    </div>
                                </div>
                            </form>                
                        </div> 
                        <div class="fxt-style-line"> 
                            <div class="fxt-transformY-50 fxt-transition-delay-5">                                
                                <h3>Or Login With</h3> 
                            </div>
                        </div>
                        <ul class="fxt-socials">
                            <li class="fxt-google">
                                <div class="fxt-transformY-50 fxt-transition-delay-6">  
                                <a href="#" title="google"><i class="fab fa-google-plus-g"></i><span>Google +</span></a>
                                </div>
                            </li>                                    
                            <li class="fxt-twitter"><div class="fxt-transformY-50 fxt-transition-delay-7">  
                                <a href="#" title="twitter"><i class="fab fa-twitter"></i><span>Twitter</span></a>
                                </div>
                            </li>
                            <li class="fxt-facebook"><div class="fxt-transformY-50 fxt-transition-delay-8">  
                                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i><span>Facebook</span></a>
                                </div>
                            </li>                                    
                        </ul>
                        <div class="fxt-footer">
                            <div class="fxt-transformY-50 fxt-transition-delay-9">  
                                <p>Already have an account?<a href="login-16.html" class="switcher-text2">Log In</a></p>
                            </div> 
                        </div> 
                    </div>
                </div>                    
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

</body>

</html>
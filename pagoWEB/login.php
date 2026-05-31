<?php 
session_start();
require_once('includes/connect.php');
require_once('if-loggedin.php');
//include('includes/header.php');
$sql = "SELECT * FROM empresa WHERE idempresa=?";
$result = $db->prepare($sql);
$result->execute(array(1));//  id empresas
$count = $result->rowCount();
$res = $result->fetch(PDO::FETCH_ASSOC);
    if($count == 1){
        $eps = $res['nombre'];
        $telefono = $res['telefono'];
        $direccion = $res['direccion'];
        $ip = $res['ipserver'];
        $user = $res['user'];
        $pass = $res['pwd'];
        $urlPagina = $res['url'];
    }
    else
    {
        $errors[] = "Definir datos de la empresa";
    }
if(isset($_POST) & !empty($_POST)){
    // PHP Validaciones de formulario
    if(empty($_POST['email'])){ $errors[]="El campo Nombre de usuario / Correo electrónico es obligatorio"; }
    if(empty($_POST['password'])){ $errors[]="El campo de contraseña es obligatorio"; }
    // CSRF Token Validaciones
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problema con la validación de token CSRF";
        }
    }
    // CSRF Token Time Validaciones
    $max_time = 60*60*24; // en segundos
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time() ){
        }else{
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }

    if(empty($errors)){
        // Verifique las credenciales de inicio de sesión
        $sql = "SELECT * FROM users WHERE ";
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $sql .= "email=?";
        }else{
            $sql .= "username=?";
        }
        $result = $db->prepare($sql);
        $result->execute(array($_POST['email']));
        $count = $result->rowCount();
        $res = $result->fetch(PDO::FETCH_ASSOC);
        if($count == 1){
            // Compara password con password hash
            if(password_verify($_POST['password'], $res['password'])){
                if($res['userStatus']=="Y"){
                    // regenerar session id
                    session_regenerate_id();
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $res['id'];
                    $_SESSION['last_login'] = time();
                    $_SESSION['user_session'] = $res['username'];
                    $_SESSION['email_session'] = $res['email'];
                    $_SESSION['dni_session'] = $res['docident'];
                    $_SESSION['pub'] = $ip;
                    $_SESSION['log'] = $user;
                    $_SESSION['pwd'] = $pass;
                    $_SESSION['empresa'] = $eps;
                    $_SESSION['urlpag'] = $urlPagina;
                    $_SESSION['telefono'] = $telefono;
                    $_SESSION['direccion'] = $direccion;
                    // redirigir al usuario al área de miembros / página de inicio
                    header("location: admin.php");
                }else{
                    $errors[] = "Esta cuenta no está activada Vaya a su Bandeja de entrada y actívela.";
                }

            }else{
                $errors[] = "La combinación de nombre de usuario / correo electrónico y contraseña no funciona";
            }
        }else{
            $errors[] = "Nombre de usuario / correo electrónico no válido";
        }
    }
} 
// 1. Create CSRF token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();
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
    <title><?php echo $eps;?></title>
</head>

<body>
    <section class="fxt-template-animation fxt-template-layout1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-12 fxt-bg-color">
                    <div class="fxt-content">
                        <div class="fxt-form"><!-- style="color: #546e7a;" -->
                            <h2><?php echo $eps;?></h2>
                            <p>¡Bienvenido, pague su recibo!</p>
                        <?php
                            if(!empty($errors)){
                                echo "<div class='alert alert-danger'>";
                                foreach ($errors as $error) {
                                    echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;".$error."<br>";
                                }
                                echo "</div>";
                            }
                        ?>
                            <!--<p>Estimado cliente, desde ahora Ud. puede pagar sus recibos desde la comodidad de su hogar, oficina o desde cualquier parte del mundo.</p>-->
                            <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                    <!-- autocomplete="off"-->
                                        <input  class="form-control" placeholder="Correo electrónico" name="email" type="text" autofocus value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>" title="Llenar el email valido" required="required">
                                        <i class="flaticon-envelope"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input class="form-control" placeholder="Contraseña" name="password" type="password" value="" required="required">
                                        <i class="flaticon-padlock"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">  
                                        <div class="fxt-checkbox-area">
                                            <a href="reset.php" class="switcher-text">¿OLVIDASTE TU CONTRASEÑA?</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">
                                        <div class="fxt-content-between">
                                            <button type="submit" class="fxt-btn-fill">Inicia sesión</button>
                                            <a href="register.php" class="fxt-btn-fill2">¿Aún no estás registrado?</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="fxt-footer">
                            <ul class="fxt-socials">
                                <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-4">
                                    <img src="assets/img/figure/niubus-logo-final_2.jpg" alt="Logo2">
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 fxt-none-767 fxt-bg-img" data-bg-image="assets/img/figure/bg1-l.jpg"></div>
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
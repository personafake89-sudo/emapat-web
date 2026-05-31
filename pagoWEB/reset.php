<?php 
session_start();
require_once('includes/connect.php');
require_once('if-loggedin.php');
ini_set('display_errors', '0');
require_once 'class.user2.php';
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
    if(empty($_POST['email'])){ $errors[]="El campo Nombre de usuario / Correo electrónico es obligatorio"; }
    // CSRF Token
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problema con la validación de token CSRF";
        }
    }
    // CSRF Token Time Validation
    $max_time = 60*60*24; // in seconds
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time() ){
        }else{
            $errors[] = "Token CSRF caducado";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }
    if(empty($errors)){
        // Compruebe que el nombre de usuario / correo electrónico existe en la base de datos, si existe, cree un token de reinicio y envíe un correo electrónico
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
        $userid = $res['id'];
        $nombres = $res['nombres'];
        $apellidos = $res['apellidos'];
        if($count == 1){
            ////$messages[] = "El nombre de usuario / correo electrónico existe, crea un token de reinicio y envía un correo electrónico";
            // Generando e insertando token de reinicio en la tabla DB
            $reset_token = md5($res['username'].time());
            $resetsql = "INSERT INTO password_reset (uid, reset_token) VALUES (:uid, :reset_token)";
            $resetresult = $db->prepare($resetsql);
            $values = array(':uid'          => $userid,
                            ':reset_token'  => $reset_token
                            );
            $resetres = $resetresult->execute($values);
            if($resetres){
                ////$messages[] = "Enviar correo electrónico con token de reinicio";
                try {
                    $user = new USER();
                    $email = $_POST['email'];
                    //$key = base64_encode($userid);
                    $message = "Hola ".$nombres." ".$apellidos.",<br /><br />{$urlPagina}/reset-password.php?key={$reset_token}&id={$userid}";
                    $subject = "Restablecer su contraseña - ".$eps."";
                    $user->send_mail($email, $message, $subject);
                    $messages[] = 'Correo electrónico de restablecimiento de contraseña enviado, siga las instruccioness';
                } catch (Exception $e) {
                    echo "El mensaje no pudo ser enviado. Error de correo: {$user->ErrorInfo}";
                }
            }
        }else{
            $errors[] = "Su cuenta no está disponible en nuestra base de datos, consulte con el administrador del sitio!";
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
                    <!-- style="color: #546e7a;" -->
                        <div class="fxt-form">
                            <h2><?php echo $eps;?></h2>
                            <p>Recuperar clave<br> ingresa el Correo electrónico de tu cuenta.</p>
                        <?php
                            if(!empty($errors)){
                                echo "<div class='alert alert-danger'>";
                                foreach ($errors as $error) {
                                    echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;".$error."<br>";
                                }
                                echo "</div>";
                            }
                        ?>
                        <?php
                            if(!empty($messages)){
                                echo "<div class='alert alert-success'>";
                                foreach ($messages as $message) {
                                    echo "<span class='glyphicon glyphicon-ok'></span>&nbsp;".$message."<br>";
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
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">
                                        <div class="fxt-content-between">
                                            <button type="submit" class="fxt-btn-fill">Restablecer la contraseña</button>
                                            <a href="login.php" class="fxt-btn-fill2">Cancelar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
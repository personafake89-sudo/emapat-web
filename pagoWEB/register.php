<?php 
session_start();
require_once('includes/connect.php');
require_once('if-loggedin.php');
ini_set('display_errors', '1');
require_once 'class.user2.php';
require_once('class.Consumer.php');
$curl = new Consumer();
$sql = "SELECT * FROM empresa WHERE idempresa=?";
$result = $db->prepare($sql);
$result->execute(array(1));//  id empresas
$count = $result->rowCount();
$res = $result->fetch(PDO::FETCH_ASSOC);
$estado = 0;
$rv = "";
if(isset($_POST) & !empty($_POST)){
    if($count == 1){
        $eps = $res['nombre'];
        $telefono = $res['telefono'];
        $direccion = $res['direccion'];
        $urlPagina = $res['url'];
        foreach ($curl->vereficarCodigo($_POST['username'], $res['ipserver'], $res['user'], $res['pwd']) as $key => $value) 
        {
            if (isset($value['cliente'])) {
                $codcliente = $value['cliente'];
            } else {
                $errors[] = "Código de suministro no esta registrado!";
            }
        }

    }
    else
    {
        $errors[] = "Definir datos de la empresa";
    }
    
    if(empty($_POST['username'])){ $errors[]="El campo de Código de suministro es obligatorio"; }else{
        $sql = "SELECT * FROM users WHERE username=?";
        $result = $db->prepare($sql);
        $result->execute(array($_POST['username']));
        $count = $result->rowCount();
        if($count == 1){
            $errors[] = "Código de suministro ya existe en la base de datos";
        }
    }
    if(empty($_POST['apellidos'])){ $errors[]="El campo Apellidos es obligatorio"; }
    if(empty($_POST['nombres'])){ $errors[]="El campo Nombres telefónico es obligatorio"; }
    if(empty($_POST['dni'])){ $errors[]="El campo DNI es obligatorio"; }
    if(empty($_POST['email'])){ $errors[]="El campo de correo electrónico es obligatorio"; }else{
        // Comprobar correo electrónico es único con consulta de base de datos
        $sql = "SELECT * FROM users WHERE email=?";
        $result = $db->prepare($sql);
        $result->execute(array($_POST['email']));
        $count = $result->rowCount();
        if($count == 1){
            $errors[] = "El correo electrónico ya existe en la base de datos";
        }
    }
    if(empty($_POST['mobile'])){ $errors[]="El campo número telefónico es obligatorio"; }
    if(empty($_POST['password'])){ $errors[]="El campo de contraseña es obligatorio"; }else{
        // verifique la contraseña de repetición
        if(empty($_POST['passwordr'])){ $errors[]="El campo Repetir contraseña es obligatorio"; }else{
            // compare ambas contraseñas, si coinciden. Generar el hash de contraseña
            if($_POST['password'] == $_POST['passwordr']){
                // password hash
                $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }else{
                // Mostrar mensaje de error
                $errors[] = "Ambas contraseñas deben coincidir";
            }
        }
    }

    // CSRF Token Validación
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problema con la validación de token CSRF";
        }
    }
    // CSRF Validación de tiempo de token
    $code = md5(uniqid(rand()));
    $max_time = 60*60*24; // en segundos
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time() ){
        }else{
            $errors[] = "Token CSRF caducado";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }

    // Si no hay errores, inserte los valores en la tabla de usuarios
    if(empty($errors)){
        $sql = "INSERT INTO users (username, email, password, nombres, apellidos, docident, tokenCode, celular, pwsiinco , created) VALUES (:username, :email, :password, :nombres, :apellidos, :docident, :tokenCode, :celular, :pwsiinco , now())";
        $result = $db->prepare($sql);
        $values = array(':username'     => $_POST['username'],
                        ':email'        => $_POST['email'],
                        ':password'     => $pass_hash ,
                        ':nombres'      => $_POST['nombres'],
                        ':apellidos'    => $_POST['apellidos'],
                        ':docident'     => $_POST['dni'],
                        ':tokenCode'    => $code,
                        ':celular'      => $_POST['mobile'],
                        ':pwsiinco'     => md5($_POST['password'])
                        );
        $res = $result->execute($values);
        if($res){
            $messages[] = "Usuario registrado";
            // obtenga la identificación de la última consulta de inserción e inserte un nuevo registro en la tabla user_info con número de teléfono móvil
            $userid = $db->lastInsertID();
            $uisql = "INSERT INTO user_info (uid, mobile) VALUES (:uid, :mobile)";
            $uiresult = $db->prepare($uisql);
            $values = array(':uid'          => $userid,
                            ':mobile'       => $_POST['mobile']
                            );
            $uires = $uiresult->execute($values) or die(print_r($result->errorInfo(), true));
            if($uires){
                $user = new USER();
                $email = $_POST['email'];
                $key = base64_encode($userid);
                $message = "Hola ".$_POST['nombres']." ".$_POST['apellidos'].",
                                        <br /><br />
                                        <b>Su usuario se registró correctamente.</b><br/>
                                        Para empezar a utilizar nuestros servicios, por favor, active su usuario haciendo clic en el enlace de verificación que se encuentra a continuación. Este enlace será válido durante 24 hs desde el momento de la registración.<br/>
                                        <br /><br />
                                        <a href='".$urlPagina."/verify.php?id=".$key."&code=".$code."'>Enlace de verificación de usuario</a>
                                        <br /><br />
                                        No responda a esta dirección de correo, ya que no se encuentra habilitada para recibir mensajes";
                $subject = "Activación de cuenta - ".$eps."";
                $user->send_mail($email, $message, $subject);
                $messages[] = "- Hemos enviado un correo electrónico a ".$email.".<br>
                - Haga clic en el enlace de confirmación en el correo electrónico para activar su cuenta.
                Si no está en la bandeja de entrada, es posible que se encuentre en la carpeta Spam, Correo no deseado.";
                $estado = 1;
                $rv = "readonly";
            }

        }
    }
}
// CSRF Proteccion
// 1. Create CSRF token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();
// 2. agregar token CSRF al formulario
// 3. verifique el token CSRF en el envío del formulario
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
                        <!--
                        <div class="fxt-header">
                            <a href="login-1.html" class="fxt-logo"><img src="assets2/img/logo-1.png" alt="Logo"></a>
                            <div class="fxt-page-switcher">
                                <a href="login-1.html" class="switcher-text1">Ingresar</a>
                                <a href="register-1.html" class="switcher-text1 active">Register</a>
                            </div>
                        </div>
                        -->
                        <div class="fxt-form">
                            <h2>Oficina Virtual</h2>
                            <p>Completa tus datos personales.</p>
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
                            <form method="POST" id="frm" name = "form">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input class="form-control" placeholder="Código de suministro" name="username" type="number" autofocus value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } ?>"  required="required" <?php echo $rv;?> >
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                                
                                        <input type="text" class="form-control" name="apellidos" placeholder="Apellidos" value="<?php if(isset($_POST['apellidos'])){ echo $_POST['apellidos']; } ?>"  required="required" <?php echo $rv;?> >
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                                
                                        <input type="text" class="form-control" name="nombres" placeholder="Nombres" value="<?php if(isset($_POST['nombres'])){ echo $_POST['nombres']; } ?>"  required="required" <?php echo $rv;?> >
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input autocomplete="off" type="number" class="form-control" name="dni" id="dni" placeholder="Número de DNI" required="required" maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php if(isset($_POST['dni'])){ echo $_POST['dni']; } ?>" <?php echo $rv;?> >
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-2">
                                        <input class="form-control" placeholder="Correo Electronico" name="email" type="email" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>"  required="required" <?php echo $rv;?> >
                                        <i class="flaticon-envelope"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">
                                        <input class="form-control" placeholder="Número telefónico" name="mobile" type="text" value="<?php if(isset($_POST['mobile'])){ echo $_POST['mobile']; } ?>" maxlength="9" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required="required" <?php echo $rv;?> >
                                        <i class="flaticon-user"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">                                                
                                        <input class="form-control" placeholder="Contraseña" name="password" type="password" required="required" value="" <?php echo $rv;?>>
                                        <i class="flaticon-padlock"></i>
                                    </div>
                                </div>

                                <div class="form-group">                                                
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">                                                
                                        <input class="form-control" placeholder="Confirmar Contraseña" name="passwordr" type="password" required="required" value="">
                                        <i class="flaticon-padlock"></i>
                                    </div>
                                </div>
<?php if ($estado==0) {?>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-5">
                                        <div class="fxt-checkbox-area">
                                            <div class="checkbox">
                                                <input id="checkbox1" type="checkbox"  class="filled-in">
                                                <label for="checkbox1">Acepto los <a href="#" target="_blank"> Términos y Condiciones</a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">
                                        <div class="fxt-content-between">
                                            <button type="submit" class="fxt-btn-fill">Crear una cuenta</button>
                                            <a href="login.php" class="fxt-btn-fill2">Inicia sesión</a>
                                            <!--<a href="javascript:history.go(-1)" class="fxt-btn-fill2">Cancelar</a>-->
                                        </div>
                                    </div>
                                </div>
<?php } else{ ?>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">
                                        <div class="fxt-content-between">
                                            <a href="login.php" class="fxt-btn-fill2">Inicia sesión</a>
                                        </div>
                                    </div>
                                </div>
<?php } ?>
                            </form>                            
                        </div> 
                        <div class="fxt-footer">
                        <!--
                            <ul class="fxt-socials">
                                <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-5"><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            </ul>
                        -->
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

    <script>
    $("#frm").submit(function(){
        if($("#checkbox1").is(':checked')) {  
            return true; 
        } else {  
            alert("Aceptar Términos y Condiciones");
            return false;
        }  
    });
    </script>
</body>

</html>
<?php
require_once('includes/connect.php');
require_once('class.Consumer.php');
ini_set('display_errors', '1');
$sql = "SELECT * FROM empresa WHERE idempresa=?";
$result = $db->prepare($sql);
$result->execute(array(1));//  id empresas
$count = $result->rowCount();
$res = $result->fetch(PDO::FETCH_ASSOC);
    if($count == 1){
        $eps  = $res['nombre'];
		$ip   = $res['ipserver'];
		$cuen = $res['user'];
		$pass = $res['pwd'];     
    }
    else
    {
        $msg  = "Definir datos de la empresa";
    }

$curl = new Consumer();
if(empty($_GET['id']) && empty($_GET['code']))
{
	$user->redirect('index.php');
}
if(isset($_GET['id']) && isset($_GET['code']))
{
	$id = base64_decode($_GET['id']);
	$code = $_GET['code'];
	$statusY = "Y";
	$statusN = "N";

    $sql = "SELECT id,userStatus, email, password,username, celular, pwsiinco FROM users WHERE id=:uID AND tokenCode=:code LIMIT 1";
	$result = $db->prepare($sql);
	$values = array(':uID'		=> $id,
					':code'		=> $code
					);
	$result->execute($values);
	$count = $result->rowCount();
	$res = $result->fetch(PDO::FETCH_ASSOC);
	if($count == 1)
	{
		if($res['userStatus']==$statusN)
		{
			foreach ($curl->updateEmail($res['username'] , $res['email']  , $res['celular'] ,$res['pwsiinco']  , $ip , $cuen , $pass  ) as $key=>$value) 
				if(isset($value['codcliente']))
				{
					$updsql = "UPDATE users SET userStatus=:status , updated = now() WHERE id=:uID";
					$updresult = $db->prepare($updsql);
					$values = array(':status'		=> $statusY,
									':uID'			=> $id
									);
					$updres = $updresult->execute($values);
					$msg = "
				           <div class='alert alert-success'>
						   <button class='close' data-dismiss='alert'>&times;</button>
							  <strong>Exito !</strong> <p>Su cuenta ahora está activada: <a href='login.php'>Entre aquí</a></p>
					       </div>
					       ";
				}
				else
				{
					$msg  = "
					<div class='alert alert-danger'>
				    <button class='close' data-dismiss='alert'>&times;</button>
					  <strong>Lo siento !</strong>  <p>El Codigo de Inscripcion no esta Registrado en nuestro sistema. ! <a href='register.php'>Entre aquí</a></p>
			        </div>";
				}
		}
		else
		{
			$msg = "
		           <div class='alert alert-danger'>
				   <button class='close' data-dismiss='alert'>&times;</button>
					  <strong>Lo siento !</strong>  <p>Su cuenta ya está activada : <a href='login.php'>Entre aquí</a></p>
			       </div>
			       ";
		}
	}
	else
	{
		$msg = "
		       <div class='alert alert-danger'>
			   <button class='close' data-dismiss='alert'>&times;</button>
			   <strong>Lo siento !</strong><p> No se encontró una cuenta(".$id.") : <a href='register.php'>Registrate aquí</a></p>
			   </div>
			   ";
	}	
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
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="assets/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <title><?php echo $eps; ?> - CONFIRMAR REGISTRO</title>
</head>

<body>
    <section class="fxt-template-animation fxt-template-layout1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-12 fxt-bg-color">
                        <div class="fxt-form" style="color: #546e7a;">
                            <h2><?php echo $eps;?></h2>
                            <p style="color: #546e7a;">¡Bienvenido!</p>
                    		<?php if(isset($msg)) { echo $msg; } ?>
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
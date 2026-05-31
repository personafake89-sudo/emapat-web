<?php
session_start();
date_default_timezone_set('America/Los_Angeles');
ini_set('display_errors', '1');
require_once('class.Empresa.php');
require_once 'class.user2.php';
$id = 100;
$code = 'aaaa';
$user = new USER();
		//---$email = "georshemill_01@hotmail.com";
		$email = "georshemill_01@hotmail.com";
		$message= "
				   Hola , $email
				   <br /><br />
				   Se nos solicitó restablecer su clave, si lo hace, simplemente haga clic en el siguiente enlace para restablecer su clave, si no, simplemente ignore este correo electrónico,
				   <br /><br />
				   Haga clic en el siguiente enlace para restablecer su clave 
				   <br /><br />
				   <a href='https://www.sedaayacucho.pe/consultaweb/resetpass.php?id=$id&code=$code'>Haga clic aquí para restablecer la clave</a>
				   <br /><br />
				   gracias :)
				   ";
		$subject = "Restablecimiento de clave XXXX";
		
		$user->send_mail($email,$message,$subject);
		
		$msg = "<div class='alert alert-success'>
					Hemos enviado un correo electrónico a $email.
                    Haga clic en el enlace de restablecimiento de clave en el correo electrónico para generar una nueva clave.
			  	</div>";
?>
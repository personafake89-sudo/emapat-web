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
$curl = new Consumer();

$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : "";
$correo = isset($_POST['email']) ? $_POST['email'] : $_SESSION['email_session'];
$dni = isset($_POST['dni']) ? $_POST['dni'] : $_SESSION['dni_session'];
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
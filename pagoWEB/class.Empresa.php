<?php
    include "conexion.php";

    class Empresa extends conexion {
        var $datosEmpresa;
        var $datosLogin;
        var $datosSistema;
        var $usuario    = "WEVISA";
        var $password   = "Jul@Ces@r";

        const URLPAGE   = 'http://localhost/pagoWEB/';
        const IPPUBLI   = 'localhost';
        const COMISIO   = 0;
        const CHECMES   = 'NO';
        const _COMPTE   = "WEVISA";
        const _PASSED   = "Jul@Ces@r";

        function __construct(){            
            $cuenta 			= base64_encode($this->usuario.":".$this->password.":0");
	        $this->datosEmpresa = $this->conectarse_api_get("api-externa/ResponseParamae/obtener-datos-login",null);
            $this->datosLogin   = $this->conectarse_api_post("api-seguridad/usersystema/vLogin",array("cuenta" => $cuenta),null);
           if($this->datosLogin->response == "EXITO"){
                $this->datosSistema = $this->conectarse_api_get("api-seguridad/sistemas/seleccionar-sistema/002",$this->datosLogin->token);
           }
        }

        function nombreEmpresa(){
            return $this->datosEmpresa->data->emp->nombre;
        }

        function direccionEmpresa(){
            return $this->datosEmpresa->data->emp->direccion;
        }

        function telefonoEmpresa(){
            return $this->datosEmpresa->data->emp->telefono;
        }

        function tokenSistema(){
            return $this->datosSistema->token;
        }

        // const EMPRESA = 'EPS MOYOBAMBA S.A.';
        
        // const _COMPTE = 'INSWEB';
        // const _PASSED = 'juli0ces@r';
        
        // const TELEFON = ' 042 562201';
        // const DIRECCI = 'Calle San Lucas C-1 URB. Vista Alegre';

        function mostrarConstante(){
            echo self::EMPRESA;
        }
    }
?>


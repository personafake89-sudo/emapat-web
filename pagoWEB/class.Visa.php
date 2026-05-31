<?php
    class Visa extends conexion{
        function generar_pedido($parametros){
            return $this->conectarse_api_post("api-externa/visa/generar-orden",$parametros,$_SESSION['token']);
        }

        function recupear_deuda($codsuc,$codcliente){
            return $this->conectarse_api_get("api-externa/visa/recupera-deuda-cliente/$codsuc/$codcliente",$_SESSION['token']);
        }
    }
?>
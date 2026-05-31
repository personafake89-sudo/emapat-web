<?php
    // include "conexion.php";

    class Clientes extends conexion{
        function recupera_datos_clientes($codcliente){
            return $this->conectarse_api_get("api-catastro/clientes/001/$codcliente",$_SESSION['token']);
        }

    }
?>
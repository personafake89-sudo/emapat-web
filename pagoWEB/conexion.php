<?php
    $URL_BACKEND = "https://sysco-api.epsemaq.com.pe/sysco-comercial/backend/";

    class conexion{

        function conectarse_api_get($url,$authorizacion){
            global $URL_BACKEND;

            $curl = curl_init(); 

            curl_setopt_array($curl, array(
                CURLOPT_URL             => $URL_BACKEND.$url,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_ENCODING        => "",
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array('Content-Type:application/json',"Authorization:".$authorizacion),
            ));
    
            $response   = curl_exec($curl);
            $err        = curl_error($curl);
    
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                return json_decode($response);
            }
        }

        function conectarse_api_post($url,$jsonData,$authorizacion){
            global $URL_BACKEND;

            $URL_HOST   = $URL_BACKEND.$url;
            $ch = curl_init($URL_HOST);
            $datosJSON = json_encode($jsonData);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $datosJSON);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:'.$authorizacion));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            return json_decode($result);
        }
    }

?>
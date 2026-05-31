<?php
class Consumer
{
    public function sendGetAll()
    {
        $ch = curl_init("http://localhost/slimrest/books");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) {
            return false;
        } else {
            var_dump($response);
        }
    }
    public function vereficarCodigo($id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/c/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function vereficarDeuda($emp, $suc, $id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/e/' . $emp . '/suc/' . $suc . '/id/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function vereficarFact($emp, $suc, $id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/emp/' . $emp . '/suc/' . $suc . '/cliente/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function vereficarReclamo($emp, $suc, $id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/emp/' . $emp . '/suc/' . $suc . '/cliente_recla/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function vereficarPagos($emp, $suc, $id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/emp/' . $emp . '/suc/' . $suc . '/cliente_pago/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function updateEmail($codigo, $email, $celular, $clave, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/codigo/' . $codigo . '/email/' . $email . '/celular/' . $celular . '/pass/' . $clave . '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function vereficarCodigoAsocc($id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/usuario/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function updateUsuarioNew($codigo, $ip, $user, $pass, $usuarioNew)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/codigo/' . $codigo . '/usuarioNew/' . $usuarioNew . '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function verRecibo($emp, $ciclo, $suc, $sec, $anio, $mes, $codigo, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/codempre/' . $emp . '/codiciclo/' . $ciclo . '/codisuc/' . $suc . '/codisector/' . $sec . '/anio/' . $anio . '/mes/' . $mes . '/codigoclie/' . $codigo . '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function redirect($url)
    {
        header("Location: $url");
    }

    public function is_loggedin()
    {
        if (isset($_SESSION['user_session'])) {
            return true;
        }
    }

    public function doLogout()
    {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }

    public function nombremes($mes)

    {
        setlocale(LC_TIME, 'spanish');
        $nombre = strftime("%B", mktime(0, 0, 0, $mes, 1, 2000));
        return strtoupper($nombre);
    }

    public function simularFact($suc, $tar, $tserv, $consumo, $anio, $mes, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/suc/' . $suc . '/tar/' . $tar . '/tserv/' . $tserv . '/consumo/' . $consumo . '/anio/' . $anio . '/mes/' . $mes . '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function simularRango($suc, $tar, $tserv, $consumo, $anio, $mes, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/suc/' . $suc . '/tar/' . $tar . '/tserv/' . $tserv . '/consumo/' . $consumo . '/anio/' . $anio . '/mes/' . $mes . '/per/1';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function registerPayment($id, $importe, $cadena,  $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/nuevo';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            $data = array(
                'codigo' => $id,
                'importe' => '' . $importe . '',
                'cadena' => '' . $cadena . ''
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("user: $user", "pass: $pass"));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function buzCodigo($id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/c/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function buzDNI($id, $ip, $user, $pass)
    {
        try {
            $url = 'http://' . $ip . '/api_consultaSiinco/index.php/dni/' . $id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function buzPropietario($id, $ip, $user, $pass)
    {
        try {
            $url = 'http://'.$ip.'/api_consultaSiinco/index.php/cPropetario/'.urlencode($id);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function generaOrden($id ,$dni , $correo   , $ip, $user, $pass)
    {
        try {
            $url = 'http://'.$ip.'/api_consultaSiinco/index.php/idx/'.$id.'/dni/'.$dni.'/correo/'.$correo;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'user:' . $user, 'pass:' . $pass));
            $result = curl_exec($ch);
            curl_close($ch);
            if (!$result) {
                return false;
            } else {
                //var_dump($result);
                return  json_decode($result, true);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}

<?php
session_start();
ini_set('display_errors', '0');
require_once("class.Consumer.php");
require_once('class.Empresa.php');
$curl = new Consumer();
$codigo = $_POST['codvalor']; //$_SESSION['user_session'];
$correo = $_SESSION['email_session'];
$dni    = $_SESSION['dni_session'];
$nomemp  = Empresa::EMPRESA;
$ip   = Empresa::IPPUBLI;
$user = Empresa::_COMPTE;
$pass = Empresa::_PASSED; $docide = $dni;
$padron = array();
$page = isset($_POST['ll']) ? $_POST['ll'] : "";
if ($_POST['tipo'] == 1) 
{
  foreach ($curl->buzCodigo($codigo , $ip , $user , $pass) as $key=>$value) 
    {
      if(isset($value['cliente']) and is_numeric($value['codcliente']))
        {
            $padron[] = $value;
        }
      else
        {
        echo $error = "Codigo sin Informacion !";
        exit();
        }
    }
}

if ($_POST['tipo'] == 2) 
{
  foreach ($curl->buzDNI($codigo , $ip , $user , $pass) as $key=>$value) 
    {
      if(isset($dni)) { $docide = $dni; } else{$docide = $codigo;}
      if(isset($value['cliente']) and is_numeric($value['codcliente']))
        {
            $padron[] = $value;

        }
      else
        {
        echo $error = "Codigo sin Informacion !";
        exit();
        }
    }
}

if ($_POST['tipo'] == 3) 
{
  foreach ($curl->buzPropietario($codigo , $ip , $user , $pass) as $key=>$value) 
    {
      if(isset($value['cliente']) and is_numeric($value['codcliente']))
        {
            $padron[] = $value;
        }
      else
        {
        echo $error = "Codigo sin Informacion !";
        exit();
        }
    }
}
$NumReg = count($padron);
if ($NumReg > 0 ) {
?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title h6">RELACIÓN DE SUMINISTROS - <?php echo $codigo;?> </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-sm" style="font-size: 12px;">
                              <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>CÓDIGO</th>
                                    <th>PROPIETARIO</th>
                                    <th>DIRECCIÓN</th>
                                    <th>DEUDA</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $ij3 =1;
                                
                                foreach ($padron as $key5=>$value5)
                                {
                                if(isset($value5['codcliente']) && ($NumReg > 0) ) {
                                  ?>
                                  <tr>

                                    <td>
                                    <?php if ($page == '') { ?>
                                      <form method="post" action="irHome.php" name="fomu<?php echo $ij3; ?>">
                                        <input type="hidden" name="codigo" value="<?php echo $value5['codcliente'];?>">
                                        <input type="hidden" name="dni" value="<?php echo $docide;?>">
                                        <button class="btn btn-success"><b>Ver Detalle</b></button>
                                      </form>
                                    <?php } else { ?>
                                    <button class="btn btn-info info btn-sm" value="<?php echo $value5['codcliente']; ?>">
                                      SELECIONAR
                                      </button>
                                    <?php } ?>
                                    </td>
                                    <td><?php echo $value5['codcliente']; ?></td>
                                    <td><?php echo $value5['cliente']; ?></td>
                                    <td><?php echo $value5['tdir'].' '.$value5['dir'].' '.$value5['nrocalle']; ?></td>
                                    <td><?php echo number_format($value5['deuda_mes']+$value5['deuda_anterior'], 2); ?></td>
                                  </tr>
                                <?php } else { ?>
                                  <tr>
                                    <td colspan="8">No existe información.</td>
                                  </tr>
                                <?php } ?>
                                <?php } ?>
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="5" align="left">Total: <?php echo $ij3; ?></td>
                                </tr>
                              </tfoot>
                            </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
<?php
}
else
{
  echo 'No existe información con el valor ingresado.';
}
?>
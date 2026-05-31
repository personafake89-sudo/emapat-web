<?php
ini_set('display_errors', '0');
require_once('class.Empresa.php');
$nomemp  = Empresa::EMPRESA; $urlPagina = Empresa::URLPAGE; $telefono = Empresa::TELEFON; $direccion = Empresa::DIRECCI;
?>
                            <div class="fxt-transformY-50 fxt-transition-delay-2">
                                <div class="alert alert-dark" role="alert"> 
                                Puedes consultar los Suministros que tienes con nosotros y si deseas puedes realizar el pago.
                                </div>

                                <div class="row" align="center">
                                    <div class="col">
                                        <div class="text-center">
                                        <!--
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd3" name="rdTipoBusqueda" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="rd3">Suministro</label>
                                            </div>
                                        -->
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd1" name="rdTipoBusqueda" class="custom-control-input" value="2" checked="" >
                                                <label class="custom-control-label" for="rd1">DNI</label>
                                            </div>

                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input type="radio" id="rd2" name="rdTipoBusqueda" class="custom-control-input" value="3">
                                                <label class="custom-control-label" for="rd2">Apellidos y Nombres</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 3%;">
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <input autocomplete="off" type="text" class="form-control" name="codvalor" id="codvalor" placeholder="Ingresar Datos" required="required">
                                    <input type="hidden" name="page" id="page" value="ini">
                                </div>
                            </div>

                            <div class="form-group" style="padding: 8px;">
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="calc" >Consultar Suministro</button>
                                    </div>
                                </div>
                            </div>

                        </div>

                            <div class="fxt-form" align="center">
                                <div id="loading" style="display:none;  ">Cargando...</div>
                                <div id="response" style="margin-top: 10px; "></div>
                            </div>

    <script type="text/javascript">
      $(function(){
        //$("#frm").submit(function(){
        $("#codvalor").focus();
        $('#calc').click(function() {
        var codvalor  = $('#codvalor').val();
        var tipo      = $("input:radio[name=rdTipoBusqueda]:checked").val();
        var ll        = $("#page").val();
        $.ajax({
          type:"POST",
          url:"ajax_buscarClientes.php",
          dataType:"html",
          data: {
                  'codvalor':codvalor,
                  'tipo':tipo,
                  'll':ll
                },
          beforeSend:function(){
            $("#loading").show();
          },
          success:function(response){
            $("#response").html(response);
            $("#loading").hide();
          }
          });
          return false;
        });

        $("#codvalor").click(function(){ 
        $('#codvalor').val("");
        });
    });
  </script>
        <footer class="footer mt-auto py-1" style="background:#1c3643;">
        <div class="container py-1">
            <div class="row">
                <div class="col-12 col-md-6 d-flex align-items-center justify-content-center order-1 order-md-0 ">
                    <!--<img src="assets/img/otro.png" width="70px" class="img-fluid" alt="">-->
                    <span class="text-info font-weight-bold h4"><?php echo $_SESSION['empresa'];?></span>
                </div>
                <div class="col-12 col-md-6 text-white text-md-left text-center">
                    <p class="h5  font-weight-bold">Dudas o Consultas</p>
                    <p class="my-0 py-0">Centro de atención al cliente: <?php echo $_SESSION['telefono'];?></p>
                    <p class="my-0 py-0"><?php echo $_SESSION['direccion'];?></p>
                </div>
            </div>

        </div>
    </footer>

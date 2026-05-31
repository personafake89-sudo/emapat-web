<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGO VISA</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/square/blue.css">
    <script src="./assets/js/jquery-3.3.1.min.js"></script>
</head>

<body class="bg-light d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-info border-bottom shadow-sm ">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <?php echo $_SESSION['empresa'];?>
            </a>
            <a href="logout.php?logout=true" class="btn btn-dark">Salir</a>
        </div>
    </nav>
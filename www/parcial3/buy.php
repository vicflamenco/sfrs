<?php
session_start();

$success = false;

if (isset($_SESSION) && isset($_SESSION['products']) && sizeof($_SESSION['products']) > 0){
    session_destroy();
    $success = true;
}

?>

<html>

<head>
    <meta charset="utf-8"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="css/index.css"></link>
</head>

<body>
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <h1>
                Resultado de la compra
            </h1>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <p>
                <?= ($success) ? "¡La compra se realizó con éxito!" : "¡No se encontraron productos en el carrito!" ?>

            </p>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <a href="index.php" class="btn btn-default">Nueva compra</a>
        </div>
    </div>

</div>

</body>
</html>
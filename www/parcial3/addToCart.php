<?php

    session_start();

    function searchForId($idproducto, $array, $cantidad) {
        foreach ($array as $key => &$val) {
            if ($val['idproducto'] == $idproducto) {
                $val['cantidad'] += $cantidad;
                $_SESSION['products'] = $array;
                return true;
            }
        }
        return false;
    }

    if (!isset($_SESSION['products'])){
        $_SESSION['products'] = array();
    }

    if (!searchForId($_POST['idproducto'],$_SESSION['products'],$_POST['cantidad'])){
        array_push($_SESSION['products'], array('idproducto' => $_POST['idproducto'], 'cantidad' => $_POST['cantidad']));
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
                    Resultado de operación
                </h1>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <p>
                    ¡El producto fue agregado al carrito de compra!
                </p>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <a href="cart.php" class="btn btn-default">Ver carrito de compras</a>
                <a href="index.php" class="btn btn-default">Seguir comprando</a>
            </div>
        </div>

    </div>

</body>
</html>
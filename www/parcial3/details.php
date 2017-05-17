<?php
    require_once("dbConnection.php");

    $product = getProductById($_GET['id'])[0];
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
                    Detalles de producto
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form method="post" action="addToCart.php">
                    <?php
                    if (isset($product)) {
                        echo '<input type="hidden" name="idproducto" value="' . $product['idproducto'] . '" />';
                        echo '<div>Producto: <strong>' . $product['nombre'] . '</strong></div>';
                        echo '<div>Precio: <strong>$' . $product['precio'] . '</strong></div>';
                        echo '<div>' . '<img src="img/' . $product['imagen'] . '" />' . '</div>';
                        echo '<div>Cantidad: ' . '<input type="number" step="1.0" value="1" min="1" max="10" name="cantidad"/><br/><br/>';
                        echo '<div><p><input type="submit" value="Añadir al carrito" class="btn btn-primary"/> ';
                        echo '<a href="index.php" class="btn btn-default">Regresar</a></p></div>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>


</body>
</html>
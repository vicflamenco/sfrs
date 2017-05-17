<?php
    require_once("dbConnection.php");

    $products = getProducts();
?>

<html>

<head>
    <meta charset="utf-8"/>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link type="text/css" rel="stylesheet" href="css/index.css" />
</head>

<body>
    <div class="container">

        <div clas="row">
            <div class="col-md-12">
                <h1>
                    Mi librería en línea
                </h1>
            </div>
        </div>

        <div clas="row">
            <div class="col-md-12">
                <a href="cart.php" class="btn btn-primary" >Ver carrito de compras</a>
            </div>
            <hr/>
        </div>

        <div class="row" style="margin-top: 100px;">
            <div class="col-md-12">
                <table class="table table-bordered">

                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php
                    $i = 1;
                    foreach ($products as $product){
                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td>' . $product['nombre'] . '</td>';
                        echo '<td>$' . $product['precio'] . '</td>';
                        //echo '<td>' . '<img src="img/' . $product['imagen'] . '" />' . '</td>';
                        echo '<td>' . '<a href="' . 'details.php?id=' . $product['idproducto'] . '">Añadir al carrito</a>';
                        echo '</tr>';
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
<?php
session_start();
require_once("dbConnection.php");
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
                    Carrito de compras
                </h1>
            </div>
        </div>

        <br/>
        <br/>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal item</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php

                    $subtotal = 0;
                    if (isset($_SESSION['products'])){
                        $i = 1;

                        foreach ($_SESSION['products'] as $product){

                            $record = getProductById($product['idproducto'])[0];

                            $subtotalItem = $record['precio'] * $product['cantidad'];
                            $subtotal += $subtotalItem;

                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td>' . $record['nombre'] . '</td>';
                            echo '<td>$' . $record['precio'] . '</td>';
                            echo '<td>' . $product['cantidad'] . '</td>';
                            echo '<td>$' . $subtotalItem . '</td>';
                            echo '<td>' . '<a href="removeItem.php?id=' . $record['idproducto'] . '">Quitar</a>' . '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4">
                            Subtotal
                        </td>
                        <td colspan="2">
                            <?= '$' . $subtotal ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                            IVA (13%)
                        </td>
                        <td colspan="2">
                            <?= '$' . ($subtotal * 0.13) ?>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="4">
                            Total
                        </td>
                        <td colspan="2">
                            <?= '$' . ($subtotal * 1.13) ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="buy.php" class="btn btn-primary">Realizar compra</a>
                <a href="index.php" class="btn btn-default">Añadir más productos</a>
            </div>
        </div>
    </div>

</body>
</html>
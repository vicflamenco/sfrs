<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 23/10/2016
 * Time: 10:26
 */

$view_name = "unauthorized";

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Acceso no autorizado</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>
                Usted no tiene permisos suficientes para acceder a esta opción.
            </p>

            <p>
                Contacte al administrador del sistema.
            </p>
        </div>
    </div>
</div>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>
<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 30/10/2016
 * Time: 16:33
 */

$view_name = "myprofile";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';
$pdo = getDbConnection();

//===================================================================================================
//  Obteniendo información de usuario
//===================================================================================================
$user = null;
$iduser = $_SESSION['iduser'];
$params = array('iduser' => $iduser);
$stmt = $pdo->prepare('SELECT iduser,firstname,lastname,email FROM user WHERE iduser = :iduser');

if ($stmt->execute($params) && $row = $stmt->fetch()){
    $user = array(
        'iduser' => $row['iduser'],
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'email' => $row['email']);
}

?>

<script type="text/javascript" src="/sfrs/js/modules/common/myprofile.js"></script>

<div id="page-wrapper">
    <div class="row" id="header">
        <div class="col-md-12">
            <h1 class="page-header">Mi perfil</h1>
        </div>
    </div>
    <div class="row" id="readUserInformationDiv">

        <div class="col-md-12">
            <?php
                if ($user != null) {
                    ?>

                    <form class="form-horizontal">
                        <fieldset>

                            <!-- Form Name -->
                            <legend>Datos personales</legend>

                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label">Nombres:</label>
                                <div class="col-md-4">
                                    <?php echo $user['firstname'] ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Apellidos:</label>
                                <div class="col-md-4">
                                    <?php echo $user['lastname'] ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Correo:</label>
                                <div class="col-md-4">
                                    <?php echo $user['email'] ?>
                                </div>
                            </div>
                            <?php
                                if ($user['iduser'] != 1) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"></label>
                                        <div class="col-md-4">
                                            <input type="button" id="btnChangePassword" value="Cambiar contraseña"
                                                   class="btn btn-primary"/>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>

                        </fieldset>
                    </form>

                    <?php
                } else {
                    ?>
                    <p>
                        Ocurrió un error al intentar consultar su información. Intente nuevamente
                    </p>
                    <?php
                }
            ?>
        </div>
    </div>


    <div clas="row" id="changePasswordDiv">

        <div class="col-md-12">

            <form class="form-horizontal" id="frmChangePassword">
                <fieldset>

                    <!-- Form Name -->
                    <legend>Cambiar contraseña</legend>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label">Contraseña actual:</label>
                        <div class="col-md-4">
                            <input type="password" name="password" id="password" class="form-control" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Nueva contraseña:</label>
                        <div class="col-md-4">
                            <input type="password" name="newPassword" id="newPassword" class="form-control" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Repetir contraseña nueva:</label>
                        <div class="col-md-4">
                            <input type="password" name="newPassword2" id="newPassword2" class="form-control" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-4">
                            <input type="submit" value="Guardar" class="btn btn-primary" />
                            <input type="button" value="Cancelar" id="btnCancel" class="btn btn-default" />
                        </div>
                    </div>

                </fieldset>
            </form>

        </div>
    </div>
</div>


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>

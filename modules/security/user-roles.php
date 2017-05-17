<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 19/10/2016
 * Time: 21:19
 */

$view_name = "users";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

//============================================================
//      Declaración/Instanciación de variables
//============================================================
$pdo = getDbConnection();       // Conexión a base de datos
$iduser = $_GET['iduser'];     // Id del usuario a configurar
$currentRoles = array();        // Roles asignados al usuario
$availableRoles = array();      // Todos los roles disponibles

//============================================================
//      Consulta de roles asignados al usuario
//============================================================
$params = array(':iduser' => $iduser);
$stmt = $pdo->prepare('
                SELECT
                      ur.idrole idrole,
                      r.rolename rolename
                FROM user_role ur
                INNER JOIN role r ON r.idrole = ur.idrole
                WHERE ur.iduser = :iduser');

if ($stmt->execute($params)) {
    while ($row = $stmt->fetch()) {
        $item = array('idrole' => $row['idrole'], 'rolename' => $row['rolename']);
        array_push($currentRoles, $item);
    }
}
//============================================================
//      Consulta de todos los roles disponibles
//============================================================
$stmt = $pdo->prepare('SELECT * FROM role');
if ($stmt->execute($params)) {
    while ($row = $stmt->fetch()) {
        $item = array('idrole' => $row['idrole'], 'rolename' => $row['rolename']);
        array_push($availableRoles, $item);
    }
}
//============================================================
//      Consulta de nombre de usuario a configurar
//============================================================
$params = array(':iduser' => $iduser);
$stmt = $pdo->prepare('
                SELECT
                      firstname, 
                      lastname
                FROM user
                WHERE iduser = :iduser');

$username = ($stmt->execute($params) && $row = $stmt->fetch()) ?
    $row['firstname'] . ' ' . $row['lastname'] : "";

//============================================================
//      Configuración de control MultiSelect
//============================================================
$select = new \Kendo\UI\MultiSelect('rolesMultiSelect');
$select->dataSource($availableRoles)
    ->dataTextField('rolename')
    ->dataValueField('idrole')
    ->autoBind(false)
    ->filter('contains')
    ->ignoreCase(false)
    ->placeholder('Seleccione los roles a asignar.')
    ->value(sizeof($currentRoles) > 0 ? $currentRoles : null);
?>

<script type="text/javascript" src="/sfrs/js/modules/security/user-roles.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Configuración de roles de usuario</h1>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <h4>Usuario: <strong><?php echo $username; ?></strong></h4>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <h4>Roles asignados:</h4><?php echo $select->render(); ?>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="/sfrs/modules/security/user-roles-assign.php" id="frmAssignRoles">
                <input type="hidden" name="selectedRoles" id="selectedRoles"/>
                <input type="hidden" name="iduser" value="<?php echo $iduser; ?>" />
                <input type="button" class="btn btn-primary" id="btnAssignRoles" value="Guardar">
                <input type="button" class="btn btn-default" id="btnCancel" value="Cancelar">
            </form>


        </div>
    </div>

</div>


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>
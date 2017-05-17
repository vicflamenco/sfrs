<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 19/10/2016
 * Time: 21:19
 */

$view_name = "roles";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

//============================================================
//      Declaración/Instanciación de variables
//============================================================
$pdo = getDbConnection();         // Conexión a base de datos
$idrole = $_GET['idrole'];        // Id del rol a configurar
$currentModules = array();        // Modulos asignados al rol
$availableModules = array();      // Todos los modulos disponibles

//============================================================
//      Consulta de modulos asignados al rol
//============================================================
$params = array(':idrole' => $idrole);
$stmt = $pdo->prepare('
                SELECT
                      rm.idmodule idmodule,
                      m.name name
                FROM role_module rm
                INNER JOIN module m ON m.idmodule = rm.idmodule
                WHERE rm.idrole = :idrole');

if ($stmt->execute($params)) {
    while ($row = $stmt->fetch()) {
        $item = array('idmodule' => $row['idmodule'], 'name' => $row['name']);
        array_push($currentModules, $item);
    }
}
//============================================================
//      Consulta de todos los modulos disponibles
//============================================================
$stmt = $pdo->prepare('SELECT * FROM module');
if ($stmt->execute($params)) {
    while ($row = $stmt->fetch()) {
        $item = array('idmodule' => $row['idmodule'], 'name' => $row['name']);
        array_push($availableModules, $item);
    }
}
//============================================================
//      Consulta de nombre de rol a configurar
//============================================================
$params = array(':idrole' => $idrole);
$stmt = $pdo->prepare('
                SELECT
                      rolename
                FROM role
                WHERE idrole = :idrole');

$rolename = ($stmt->execute($params) && $row = $stmt->fetch()) ? $row['rolename'] : "";

//============================================================
//      Configuración de control MultiSelect
//============================================================
$select = new \Kendo\UI\MultiSelect('modulesMultiSelect');
$select->dataSource($availableModules)
    ->dataTextField('name')
    ->dataValueField('idmodule')
    ->autoBind(false)
    ->filter('contains')
    ->ignoreCase(false)
    ->placeholder('Seleccione los módulos a asignar.')
    ->value(sizeof($currentModules) > 0 ? $currentModules : null);
?>

<script type="text/javascript" src="/sfrs/js/modules/security/role-modules.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Configuración de módulos de rol</h1>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <h4>Rol: <strong><?php echo $rolename; ?></strong></h4>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <h4>Módulos asignados:</h4><?php echo $select->render(); ?>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="/sfrs/modules/security/role-modules-assign.php" id="frmAssignModules">
                <input type="hidden" name="selectedModules" id="selectedModules"/>
                <input type="hidden" name="idrole" value="<?php echo $idrole; ?>" />
                <input type="button" class="btn btn-primary" id="btnAssignModules" value="Guardar">
                <input type="button" class="btn btn-default" id="btnCancel" value="Cancelar">
            </form>
        </div>
    </div>

</div>


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>
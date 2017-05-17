<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 19/10/2016
 * Time: 23:08
 */

$view_name = "roles";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

//============================================================
//      Declaración de variables
//============================================================
$pdo = getDbConnection();       // Conexión a base de datos
$selectedModules = trim($_POST['selectedModules']);
$idrole = $_POST['idrole'];

//========================================================================
//      Borrado de modulos asignados que fueron desmarcados en pantalla
//========================================================================
$paramsDelete = array(':idrole' => $idrole, ':selectedModules' => $selectedModules);
$stmtDelete = $pdo->prepare('
                DELETE
                FROM role_module
                WHERE idrole = :idrole
                AND NOT FIND_IN_SET(idmodule, :selectedModules)');

if ($stmtDelete->execute($paramsDelete)) {

    //============================================================
    //      Consulta de modulos que aún no están asignados al rol
    //============================================================
    $paramsQuery = array(':idrole' => $idrole, ':selectedModules' => $selectedModules);
    $stmtQuery = $pdo->prepare('
                SELECT m.idmodule
                FROM module m
                WHERE NOT EXISTS( SELECT 1
                                  FROM role_module rm
                                  WHERE rm.idrole = :idrole
                                  AND rm.idmodule = m.idmodule)
                AND FIND_IN_SET(m.idmodule, :selectedModules)');

    if ($stmtQuery->execute($paramsQuery)) {
        //========================================================================
        //      Asignación de nuevos modulos
        //========================================================================
        $modulesToAssign = array();
        while ($row = $stmtQuery->fetch()) {
            array_push($modulesToAssign, $row['idmodule']);
        }
        foreach ($modulesToAssign as $moduleToAssign) {
            $paramsInsert = array(':idrole' => $idrole, ':moduleToAssign' => $moduleToAssign);
            $stmtInsert = $pdo->prepare('INSERT INTO role_module (idrole,idmodule)
                                     VALUES (:idrole, :moduleToAssign)');
            $stmtInsert->execute($paramsInsert);
        }
    }
}

?>

<script type="text/javascript">
    window.location = "/sfrs/modules/security/roles.php"
</script>
<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 19/10/2016
 * Time: 23:08
 */

$view_name = "users";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

//============================================================
//      Declaración de variables
//============================================================
$pdo = getDbConnection();       // Conexión a base de datos
$selectedRoles = trim($_POST['selectedRoles']);
$iduser = $_POST['iduser'];

//========================================================================
//      Borrado de roles asignados que fueron desmarcados en pantalla
//========================================================================
$paramsDelete = array(':iduser' => $iduser, ':selectedRoles' => $selectedRoles);
$stmtDelete = $pdo->prepare('
                DELETE
                FROM user_role
                WHERE iduser = :iduser
                AND NOT FIND_IN_SET(idrole, :selectedRoles)');

if ($stmtDelete->execute($paramsDelete)) {

    //============================================================
    //      Consulta de roles que aún no están asignados al usuario
    //============================================================
    $paramsQuery = array(':iduser' => $iduser, ':selectedRoles' => $selectedRoles);
    $stmtQuery = $pdo->prepare('
                SELECT r.idrole
                FROM role r
                WHERE NOT EXISTS( SELECT 1
                                  FROM user_role ur
                                  WHERE ur.iduser = :iduser
                                  AND ur.idrole = r.idrole)
                AND FIND_IN_SET(r.idrole, :selectedRoles)');

    if ($stmtQuery->execute($paramsQuery)) {
        //========================================================================
        //      Asignación de nuevos roles
        //========================================================================
        $rolesToAssign = array();
        while ($row = $stmtQuery->fetch()) {
            array_push($rolesToAssign, $row['idrole']);
        }
        foreach ($rolesToAssign as $roleToAssign) {
            $paramsInsert = array(':iduser' => $iduser, ':roleToAssign' => $roleToAssign);
            $stmtInsert = $pdo->prepare('INSERT INTO user_role (iduser,idrole)
                                     VALUES (:iduser, :roleToAssign)');
            $stmtInsert->execute($paramsInsert);
        }
    }
}

?>

<script type="text/javascript">
    window.location = "/sfrs/modules/security/users.php"
</script>
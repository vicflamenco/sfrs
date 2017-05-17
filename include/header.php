
<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/8/2016
 * Time: 10:07 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/Kendo/Autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';

session_start();

if (isset($_SESSION['authenticated'])) {
    $authenticated = $_SESSION['authenticated'];
} else {
    $authenticated = false;
}

if (!$authenticated and !$isLoginPage) {
    header("Location: login.php");
}

//================================================================================
//  Verificar permisos del usuario para acceder a la vista actual
//================================================================================
if (!in_array($view_name,array("unauthorized", "login", "logout", "index", "myprofile", "myreservations"))){

    if (!userHasPermission($_SESSION['iduser'], $view_name)){
        header("Location: /sfrs/unauthorized.php");
    }
}

function userHasPermission($iduser, $view_name){
    $pdo = getDbConnection();
    $params = array(':iduser' => $iduser, ':view_name' => $view_name);
    $stmt = $pdo->prepare('
                SELECT 1 FROM module m
                INNER JOIN role_module rm ON rm.idmodule = m.idmodule
                INNER JOIN user_role ur ON ur.idrole = rm.idrole
                WHERE ur.iduser = :iduser
                AND m.view_name = :view_name');
    return ($stmt->execute($params) && $row = $stmt->fetch());
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Soccer Field Reservation System</title>

    <link href="/sfrs/css/kendo/kendo.common.min.css" rel="stylesheet" type="text/css" />
    <link href="/sfrs/css/kendo/kendo.default.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/sfrs/css/font-awesome.min.css" type="text/css">
    <!-- MetisMenu CSS -->
    <link href="/sfrs/css/metisMenu.min.css" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="stylesheet" href="/sfrs/css/sb-admin-2.min.css" type="text/css">

    <script src="/sfrs/js/jquery.min.js"></script>
    <script src="/sfrs/js/kendo.all.min.js"></script>
    <script src="/sfrs/js/messages/kendo.messages.es-ES.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $(document).ajaxStart(function () {
                $.blockUI({ message: '<h5><img src="/sfrs/content/images/loader.gif" width="35" height="35" />  Cargando...</h5>' });
            }).ajaxStop(function () {
                $.unblockUI();
            });
        });
    </script>
</head>

<body>

<?php
    if(!isset($simpleLayout) or $simpleLayout == null or !$simpleLayout){
        echo "<div id=\"wrapper\">";
        require_once 'sidebar.php';
    }
?>


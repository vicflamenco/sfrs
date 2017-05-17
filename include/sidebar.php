<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/8/2016
 * Time: 10:07 PM
 */

$securityModules = array();
$catalogModules = array();
$pdo = getDbConnection();
$params = array(':iduser' => $_SESSION['iduser']);

$stmt = $pdo->prepare('
                SELECT DISTINCT m.name, m.url, m.is_security_module+0 is_security_module 
                FROM module m
                
                INNER JOIN role_module rm ON rm.idmodule = m.idmodule
                INNER JOIN user_role ur ON ur.idrole = rm.idrole
                
                WHERE ur.iduser = :iduser');

if ($stmt->execute($params)){
    while($row = $stmt->fetch()){

        $item = array('name' => $row['name'], 'url' => $row['url']);
<<<<<<< HEAD

        if ($row['is_security_module'] == 1){
=======
	
        if ($row['is_security_module']){
>>>>>>> a3ef18b1ac78fdb22432de3257a48cbfd37ed503
            array_push($securityModules,$item);
        } else {
            array_push($catalogModules,$item);
        }
    }
}
?>

<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">Soccer Field Reservation System  - Bienvenido <?= $_SESSION['user_firstname'] ?> </a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="/sfrs/modules/common/myprofile.php"><i class="fa fa-user fa-fw"></i> Mi perfil</a>
                <li><a href="/sfrs/modules/common/myreservations.php"><i class="fa fa-user fa-fw"></i> Mis reservas</a>
                </li>
                <li class="divider"></li>
                <li><a href="/sfrs/logout.php"><i class="fa fa-sign-out fa-fw"></i> Cerrar sesión</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li>
                    <a href="/sfrs/index.php"><i class="fa fa-dashboard fa-fw"></i> Inicio</a>
                </li>
                <?php
                if (sizeof($securityModules) > 0) {
                    ?>
                    <li>
                        <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i>Seguridad<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">

                            <?php
                            foreach ($securityModules as $securityModule) {
                                echo '<li>
                                        <a href="/sfrs/modules/security/' . $securityModule["url"] . '">' . $securityModule["name"] . '</a>
                                    </li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }

                if (sizeof($catalogModules) > 0) {
                    ?>
                    <li>
                        <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i>Catálogos<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">

                            <?php
                            foreach ($catalogModules as $catalogModule) {
                                echo '<li>
                                        <a href="/sfrs/modules/catalogs/' . $catalogModule["url"] . '">' . $catalogModule["name"] . '</a>
                                    </li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

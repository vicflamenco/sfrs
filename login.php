<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 12:24 AM
 */
    $simpleLayout = true;
    $message = "";
    $isLoginPage = true;

    $view_name = "login";
    require_once 'include/header.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST['email']) and isset($_POST['password']))
        {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $pdo = getDbConnection();

            $params = array(':email' => $email);
            $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');

            if ($stmt->execute($params)){
                if ($row = $stmt->fetch()){

                    if (password_verify($password,$row['password'])){

                        $_SESSION['authenticated'] = true;
                        $_SESSION['user_firstname'] = $row['firstname'];
                        $_SESSION['user_lastname'] = $row['lastname'];
                        $_SESSION['user_email'] = $row['email'];
                        $_SESSION['iduser'] = $row['iduser'];

                        header("Location: index.php");

                    } else {
                        $message = 'Sus credenciales no son válidas.';
                    }
                } else {
                    $message = 'Sus credenciales no son válidas.';
                }
            } else {
                $message = 'Error al intentar consultar la información del usuario.';
            }
        } else {
            $message = "Error al intentar leer la información.";
        }
    }
?>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Iniciar Sesión</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="post" >
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="E-mail" name="email" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="true">Recordarme
                                </label>
                            </div>
                            <input type="submit" class="btn btn-lg btn-success btn-block" value="Iniciar Sesión" />
                            <br><?= $message ?>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

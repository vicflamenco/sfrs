<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 19/10/2016
 * Time: 2:19 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/email.php';

$request = json_decode(file_get_contents('php://input'));
$ds = getDatasource();
$pdo = getDbConnection();
session_start();
$type = $_GET['type'];
$data = null;

if ($type == 'create') {
    if (sizeof($request->models) > 0) {

        $email = $request->models[0]->email;
        $params = array(':email' => $email);
        $stmt = $pdo->prepare('SELECT 1 FROM user WHERE email = :email');
        if ($stmt->execute($params)){
            if ($stmt->rowCount() <= 0){

                $password = generatePassword();
                $request->models[0]->password = password_hash($password, PASSWORD_DEFAULT);
                $data = $ds->create('user', array('iduser', 'firstname', 'password', 'lastname','email'), $request->models, 'iduser');

                //=======================================================================
                //  Inicia envio de correo electronico a usuario
                //=======================================================================
                $record = $data['data'][0];
                $emailParams = array($record->firstname, $record->lastname, $password);
                $emailResult = sendEmail('create_user',$email,$emailParams);
                //=======================================================================
                //  Finaliza envio de correo electronico a usuario
                //=======================================================================

            } else {
                returnError('Ya existe un usuario con el mismo correo electronico.');
            }
        } else {
            returnError('Error al intentar consultar si existe un usuario con el mismo correo electronico.');
        }
    }
}
else if ($type == 'read') {
    // The 'read' method accepts table name, array of columns to select and request parameters as array
    $data = $ds->read('user', array('iduser', 'firstname', 'lastname','email'),$request);
}
else  if ($type == 'update') {

    $iduser = $request->models[0]->iduser;

    if ($iduser == 1) {
        returnError('El usuario administrador no puede modificarse.');
    } else {
        $data = $ds->update('user', array('iduser', 'firstname', 'lastname','email'), $request->models, 'iduser');
    }

}
else if ($type == 'destroy') {
    // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
    $data = $ds->destroy('user', $request->models, 'iduser');
} else if ($type == 'generate_password'){

    $password = generatePassword();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $params = array(':iduser' => $_GET['iduser'], ':password' => $hashedPassword);
    $stmt = $pdo->prepare('UPDATE user SET password = :password WHERE iduser = :iduser');

    if ($stmt->execute($params)){

        $data = array('message' => 'La contraseña se generó exitosamente.');

        //=======================================================================
        //  Inicia envio de correo electronico a usuario
        //=======================================================================
        $params = array(':iduser' => $_GET['iduser']);
        $stmt = $pdo->prepare('SELECT email FROM user WHERE iduser = :iduser');
        if ($stmt->execute($params) && $row=$stmt->fetch()){
            $emailParams = array($password);
            $emailResult = sendEmail('generate_password',$row['email'],$emailParams);
        }
        //=======================================================================
        //  Finaliza envio de correo electronico a usuario
        //=======================================================================

    } else {
        returnError('Error al intentar actualizar la contraseña del usuario.');
    }
} else if ($type == 'change_password'){

    $password = $_POST['password'];
    $newPassword = $_POST['newPassword'];
    $newPassword2 = $_POST['newPassword2'];

    if ($newPassword == $newPassword2){

        $params = array('iduser' => $_SESSION['iduser']);
        $stmt = $pdo->prepare('SELECT password, email FROM user WHERE iduser = :iduser');
        if ($stmt->execute($params)){
            if ($row = $stmt->fetch()){

                if (password_verify($password,$row['password'])){

                    $email = $row['email'];
                    $params = array(':password' => password_hash($newPassword,PASSWORD_DEFAULT), 'iduser' => $_SESSION['iduser']);
                    $stmt = $pdo->prepare('UPDATE user SET password = :password WHERE iduser = :iduser');
                    if ($stmt->execute($params)){
                        $data = array('message' => 'La contrasena se edito satisfactoriamente');

                        //=======================================================================
                        //  Inicia envio de correo electronico a usuario
                        //=======================================================================
                        $emailParams = array();
                        $emailResult = sendEmail('change_password',$email,$emailParams);
                        //=======================================================================
                        //  Finaliza envio de correo electronico a usuario
                        //=======================================================================

                    } else {
                        returnError('Error al intentar guardar la nueva contrasena.');
                    }
                } else {
                    returnError('La contrasena actual no es correcta.');
                }
            } else {
                returnError('Usuario no encontrado.');
            }
        } else {
            returnError('Error al intentar consultar la informacion del usuario.');
        }
    } else {
        returnError('La contasenas nuevas no coinciden.');
    }
}

function returnError($message) {
    header('HTTP/1.1 500 ' . $message);
}

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}

// Set response content type
header("Content-Type: application/json; charset=utf-8", true);

echo json_encode($data);
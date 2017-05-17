<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 10/10/2016
 * Time: 2:19 PM
 */


require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/email.php';

$request = json_decode(file_get_contents('php://input'));
$ds = getDatasource();
$pdo = getDbConnection();
$type = $_GET['type'];
$data = null;
session_start();

if ($type == 'read') {

    $params = array(':iduser' => $_SESSION['iduser']);
    $stmt = $pdo->prepare('
                SELECT
                    r.idreservation idreservation,
                    r.iduser iduser,
                    r.reservation_date reservation_date,
                    sf.name soccer_field,
                    s.dayofweek dayofweek,
                    r.price price,
                    s.time time,
                    r.create_date create_date,
                    rs.description reservation_state
                FROM reservation r
                LEFT JOIN schedule s ON s.idschedule = r.idschedule
                LEFT JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                LEFT JOIN soccer_field sf ON sf.idsoccer_field = r.idsoccer_field
                WHERE r.iduser = :iduser');
    if ($stmt->execute($params)){
        $innerData = array();
        while($row = $stmt->fetch()){
            array_push($innerData,
                array('idreservation' => $row['idreservation'],
                    'reservation_date' => $row['reservation_date'],
                    'soccer_field' => $row['soccer_field'],
                    'dayofweek' => $row['dayofweek'],
                    'price' => $row['price'],
                    'time' => $row['time'],
                    'create_date' => $row['create_date'],
                    'reservation_state' => $row['reservation_state']));
        }
        $data = new customDataSourceResult($innerData);
    }
} else if ($type == 'change_status') {

    $idreservation = $_GET['idreservation'];
    $idreservation_state = $_GET['idreservation_state'];

    $params = array(':idreservation' => $idreservation, 'idreservation_state' => $idreservation_state);

    $stmt = $pdo->prepare(' SELECT 
                                rs.description description, 
                                r.reservation_date reservation_date
                            FROM reservation r
                            INNER JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                            WHERE r.idreservation = :idreservation 
                            AND (r.idreservation_state = :idreservation_state OR r.idreservation_state != 4) 
                            LIMIT 1'); // Estado 4-> Reservada

    if ($stmt->execute($params) && $stmt->rowCount() <= 0){

        $params = array(':idreservation' => $idreservation);
        $stmt = $pdo->prepare(' SELECT 
                                r.reservation_date reservation_date,
                                s.time time, 
                                r.price price, 
                                sf.name soccer_field,
                                u.email email
                            FROM reservation r
                            INNER JOIN soccer_field sf ON sf.idsoccer_field = r.idsoccer_field
                            INNER JOIN schedule s ON s.idschedule = r.idschedule
                            INNER JOIN user u ON u.iduser = r.iduser
                            WHERE r.idreservation = :idreservation
                            LIMIT 1');

        if ($stmt->execute($params) && $row = $stmt->fetch()){

            $reservation_date = $row['reservation_date'];
            $time = $row['time'] . ':00';
            $soccer_field = $row['soccer_field'];
            $price = '$' . $row['price'];
            $email = $row['email'];

            $params = array(':idreservation' => $idreservation, 'idreservation_state' => $idreservation_state);
            $stmt = $pdo->prepare(' UPDATE reservation
                                SET idreservation_state = :idreservation_state
                                WHERE idreservation = :idreservation');

            if ($stmt->execute($params)){

                //=======================================================================
                //  Inicia envio de correo electronico a usuario
                //=======================================================================
                $emailParams = array($reservation_date, $time, $soccer_field, $price);
                $emailResult = sendEmail('cancel_reservation',$email,$emailParams);
                //=======================================================================
                //  Finaliza envio de correo electronico a usuario
                //=======================================================================

                http_response_code(200);

            } else {
                returnError('Error al actualizar el registro.');
            }
        } else {
            returnError('Error al intentar consultar informacion de reserva');
        }
    } else {
        $row = $stmt->fetch();
        returnError('La reservacion seleccionada no se modifico debido a que ya se encuentra en estado ' . $row['description']);
    }
}

function returnError($message) {
    header('HTTP/1.1 500 ' . $message);
}

// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
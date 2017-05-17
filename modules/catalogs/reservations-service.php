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

if ($type == 'create') {

    // Obteniendo registro a actualizar
    $model = $request->models[0];

    if ($model->username == null || $model->reservation_date == null || $model->time == null || $model->soccer_field == null) {
        returnError('Favor complete todos los campos del formulario antes de continuar.');
    } else {

        //==================================================================================================
        // Aplicando formato a fecha y obteniendo día de la semana en base a la fecha seleccionada
        //==================================================================================================

        $model->reservation_date = date('Y-m-d',strtotime(substr($model->reservation_date,0,10)));
        $dayofweek = date('N', strtotime(str_replace('-','/', $model->reservation_date))) - 1;

        //==================================================================================================
        // Consultando si existe un horario configurado para el día y hora seleccionados
        //==================================================================================================

        $params = array(':dayofweek' => $dayofweek, ':time' => $model->time);
        $stmt = $pdo->prepare(' SELECT idschedule, price
                                FROM schedule
                                WHERE dayofweek = :dayofweek
                                AND time = :time
                                LIMIT 1');

        //==================================================================================================
        // Si se encuentra el Schedule, continuar
        //==================================================================================================
        if ($stmt->execute($params)){

            if ($row = $stmt->fetch()){

                $idschedule = $row['idschedule'];
                $price = $row['price'];

                //==================================================================================================
                // Verificar que la cancha no esté reservada para ese día a esa hora
                //==================================================================================================
                $params = array(
                    ':reservation_date' => $model->reservation_date,
                    ':idsoccer_field' => $model->soccer_field->idsoccer_field,
                    ':idschedule' => $idschedule);

                $stmt = $pdo->prepare('SELECT 1
                                    FROM reservation
                                    WHERE reservation_date = :reservation_date
                                    AND idsoccer_field = :idsoccer_field
                                    AND idschedule = :idschedule');
                //==================================================================================================
                // Si no existe una reserva, continuar
                //==================================================================================================
                if ($stmt->execute($params)){

                    if ($stmt->rowCount() <= 0){

                        $params = array(
                            ':iduser' => $model->username->iduser,
                            ':idsoccer_field' => $model->soccer_field->idsoccer_field,
                            ':idschedule' => $idschedule,
                            ':price' => $price,
                            ':reservation_date' => $model->reservation_date
                        );
                        $stmt = $pdo->prepare(
                            'INSERT INTO reservation (
                                iduser,idsoccer_field,idschedule,reservation_date,price,create_date,idreservation_state
                            ) VALUES (
                                :iduser,:idsoccer_field,:idschedule,:reservation_date,:price,now(),4);');
                        if ($stmt->execute($params)){

                            $model->idreservation = (int)$pdo->lastInsertId('idreservation');

                            $params = array(':idreservation' => $pdo->lastInsertId());
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

                                //=======================================================================
                                //  Inicia envio de correo electronico a usuario
                                //=======================================================================
                                $emailParams = array($reservation_date, $time, $soccer_field, $price);
                                $emailResult = sendEmail('create_reservation',$email,$emailParams);
                                //=======================================================================
                                //  Finaliza envio de correo electronico a usuario
                                //=======================================================================

                                http_response_code(200);
                            } else {
                                returnError('Error al intentar consultar informacion de reserva');
                            }
                        } else {
                            returnError('Error al insertar el registro en la base de datos.');
                        }
                    } else {
                        returnError('No se pudo crear la reserva. Ya existe una reserva para el dia, hora y cancha seleccionados.');
                    }
                } else {
                    returnError('Error al intentar consultar si existe una reserva para el dia, hora y cancha seleccionados.');
                }
            } else {
                returnError('No existe horario configurado para el dia y hora seleccionados.');
            }
        } else {
            returnError('Error al intentar consultar si existe un horario configurado para el día y hora seleccionados.');
        }

        $params = array('idreservation' => $model->idreservation);
        $stmt = $pdo->prepare('
                SELECT
                    r.idreservation idreservation,
                    r.iduser iduser,
                    r.reservation_date reservation_date,
                    r.idsoccer_field idsoccer_field,
                    s.dayofweek dayofweek,
                    r.price price,
                    s.time time,
                    r.create_date create_date,
                    rs.description reservation_state
                FROM reservation r
                LEFT JOIN schedule s ON s.idschedule = r.idschedule
                LEFT JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                WHERE r.idreservation = :idreservation');
        if ($stmt->execute($params)){
            $innerData = array();
            while($row = $stmt->fetch()){
                array_push($innerData,
                    array('idreservation' => $row['idreservation'],
                        'iduser' => $row['iduser'],
                        'reservation_date' => $row['reservation_date'],
                        'idsoccer_field' => $row['idsoccer_field'],
                        'dayofweek' => $row['dayofweek'],
                        'price' => $row['price'],
                        'time' => $row['time'],
                        'create_date' => $row['create_date'],
                        'reservation_state' => $row['reservation_state']));
            }
            $data = new customDataSourceResult($innerData);
        }
    }
}
else if ($type == 'read') {

    $stmt = $pdo->prepare('
                SELECT
                    r.idreservation idreservation,
                    r.iduser iduser,
                    r.reservation_date reservation_date,
                    r.idsoccer_field idsoccer_field,
                    s.dayofweek dayofweek,
                    r.price price,
                    s.time time,
                    r.create_date create_date,
                    rs.description reservation_state
                FROM reservation r
                LEFT JOIN schedule s ON s.idschedule = r.idschedule
                LEFT JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                ORDER BY r.create_date DESC');
    if ($stmt->execute()){
        $innerData = array();
        while($row = $stmt->fetch()){
            array_push($innerData,
                array('idreservation' => $row['idreservation'],
                    'iduser' => $row['iduser'],
                    'reservation_date' => $row['reservation_date'],
                    'idsoccer_field' => $row['idsoccer_field'],
                    'dayofweek' => $row['dayofweek'],
                    'price' => $row['price'],
                    'time' => $row['time'],
                    'create_date' => $row['create_date'],
                    'reservation_state' => $row['reservation_state']));
        }
        $data = new customDataSourceResult($innerData);
    }
}
else  if ($type == 'update') {

    // Obteniendo registro a actualizar
    $model = $request->models[0];

    //==================================================================================================
    // Obteniendo Schedule
    //==================================================================================================
    $dayofweek = date('N', strtotime(str_replace('-','/', $model->reservation_date))) - 1;
    $params = array(':dayofweek' => $dayofweek, ':time' => $model->time);
    $stmt = $pdo->prepare(' SELECT
                              idschedule,
                              price
                            FROM schedule
                            WHERE dayofweek = :dayofweek
                            AND time = :time
                            LIMIT 1');

    //==================================================================================================
    // Si se encuentra el Schedule, realizar actualización
    //==================================================================================================
    if ($stmt->execute($params)){

        if ($row = $stmt->fetch()){

            $idschedule = $row['idschedule'];
            $price = $row['price'];

            //==================================================================================================
            // Verificar que la cancha no esté reservada para ese día a esa hora
            //==================================================================================================
            $params = array(
                ':idreservation' => $model->idreservation,
                ':reservation_date' => substr($model->reservation_date,0,10),
                ':idsoccer_field' => $model->soccer_field->idsoccer_field,
                ':idschedule' => $idschedule);

            $stmt = $pdo->prepare('SELECT 1
                                    FROM reservation
                                    WHERE reservation_date = :reservation_date
                                    AND idsoccer_field = :idsoccer_field
                                    AND idschedule = :idschedule
                                    AND idreservation != :idreservation');
            //==================================================================================================
            // Si no existe una reserva, continuar
            //==================================================================================================
            if ($stmt->execute($params)){

                if ($stmt->rowCount() <= 0){

                    $params = array(
                        ':idreservation' => $model->idreservation,
                        ':iduser' => $model->username->iduser,
                        ':idsoccer_field' => $model->soccer_field->idsoccer_field,
                        ':idschedule' => $idschedule,
                        ':price' => $price,
                        ':reservation_date' => $model->reservation_date
                    );

                    $stmt = $pdo->prepare('UPDATE reservation SET
                            iduser = :iduser, 
                            idsoccer_field = :idsoccer_field,
                            idschedule = :idschedule,
                            reservation_date = :reservation_date,
                            price = :price
                           WHERE idreservation = :idreservation');
                    $stmt->execute($params);
                } else {
                    returnError('No se pudo guardar la reserva. Ya existe una reserva para el dia, hora y cancha seleccionados.');
                }
            } else {
                returnError('Error al intentar consultar si la cancha seleccionada ya está reservada para el día y hora seleccionados.');
            }
        } else {
            returnError('No existe horario configurado para el dia y hora seleccionados.');
        }
    } else {
        returnError('Error al intentar consultar si existe un horario configurado para el día y hora seleccionados.');
    }

    $params = array('idreservation' => $model->idreservation);
    $stmt = $pdo->prepare('
                SELECT
                    r.idreservation idreservation,
                    r.iduser iduser,
                    r.reservation_date reservation_date,
                    r.idsoccer_field idsoccer_field,
                    s.dayofweek dayofweek,
                    r.price price,
                    s.time time,
                    r.create_date create_date,
                    rs.description reservation_state
                FROM reservation r
                LEFT JOIN schedule s ON s.idschedule = r.idschedule
                LEFT JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                WHERE r.idreservation = :idreservation');
    if ($stmt->execute($params)){
        $innerData = array();
        while($row = $stmt->fetch()){
            array_push($innerData,
                array('idreservation' => $row['idreservation'],
                    'iduser' => $row['iduser'],
                    'reservation_date' => $row['reservation_date'],
                    'idsoccer_field' => $row['idsoccer_field'],
                    'dayofweek' => $row['dayofweek'],
                    'price' => $row['price'],
                    'time' => $row['time'],
                    'create_date' => $row['create_date'],
                    'reservation_state' => $row['reservation_state']));
        }
        $data = new customDataSourceResult($innerData);
    }

    // The 'update' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
    //$data = $ds->update('schedule', array('idschedule', 'dayofweek', 'time', 'price'), $request->models, 'idschedule');
}
else if ($type == 'destroy') {
    // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
    //$data = $ds->destroy('schedule', $request->models, 'idschedule');
} else if ($type == 'users') {

    $stmt = $pdo->prepare('
                SELECT
                    iduser iduser,
                    CONCAT_WS(\' \',firstname,lastname) username
                FROM user');
    if ($stmt->execute()){
        $innerData = array();
        while($row = $stmt->fetch()){
            array_push($innerData,array('iduser' => $row['iduser'],'username' => $row['username']));
        }
        $data = new customDataSourceResult($innerData);
    }
} else if ($type == 'soccer_fields') {

    $data = $ds->read('soccer_field', array('idsoccer_field', 'name'));

} else if ($type == 'change_status') {

    $idreservation = $_GET['idreservation'];
    $idreservation_state = $_GET['idreservation_state'];

    $params = array(':idreservation' => $idreservation, 'idreservation_state' => $idreservation_state);

    $stmt = $pdo->prepare(' SELECT rs.description description
                            FROM reservation r
                            INNER JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                            WHERE r.idreservation = :idreservation 
                            AND (r.idreservation_state = :idreservation_state OR r.idreservation_state != 4) 
                            LIMIT 1'); // Estado 4-> Reservada

    if ($stmt->execute($params) && $stmt->rowCount() <= 0){

        $stmt = $pdo->prepare(' UPDATE reservation
                                SET idreservation_state = :idreservation_state
                                WHERE idreservation = :idreservation');
        if ($stmt->execute($params)){

            $params = array(':idreservation' => $idreservation);
            $stmt = $pdo->prepare(' SELECT 
                              rs.description description,
                              r.reservation_date reservation_date,
                              r.price price,
                              sf.name soccer_field,
                              s.time time,
                              u.email email
                        FROM reservation r
                        INNER JOIN reservation_state rs ON rs.idreservation_state = r.idreservation_state
                        INNER JOIN soccer_field sf ON sf.idsoccer_field = r.idsoccer_field
                        INNER JOIN schedule s ON s.idschedule = r.idschedule
                        INNER JOIN user u ON u.iduser = r.iduser
                        WHERE r.idreservation = :idreservation 
                        LIMIT 1');

            if ($stmt->execute($params) && $row = $stmt->fetch()) {

                $reservation_date = $row['reservation_date'];
                $time = $row['time'] . ':00';
                $soccer_field = $row['soccer_field'];
                $price = '$' . $row['price'];
                $email = $row['email'];
                $status = strtoupper($row['description']);

                //=======================================================================
                //  Inicia envio de correo electronico a usuario
                //=======================================================================
                $emailParams = array($reservation_date, $time, $soccer_field, $price, $status);
                $emailResult = sendEmail('update_reservation',$email,$emailParams);
                //=======================================================================
                //  Finaliza envio de correo electronico a usuario
                //=======================================================================
                http_response_code(200);
            } else {
                returnError('Error al consultar informacion de reserva');
            }
        } else {
            returnError('Error al actualizar el registro.');
        }
    } else {
        $row = $stmt->fetch();
        returnError('La reservacion seleccionada no se modifico debido a que ya se encuentra en estado ' . $row['description']);
    }
} else if ($type == 'getAvailableSoccerFields'){

    $reservation_date = (new DateTime($_GET['reservation_date']))->format('Y-m-d');
    $time = $_GET['time'];
    $dayofweek = date('w',strtotime($_GET['reservation_date']));
    $dayofweek = ($dayofweek == 0) ? 6 : $dayofweek - 1;

    //=======================================================================================================
    // Obteniendo schedule
    //=======================================================================================================
    $params = array(':time' => $time, ':dayofweek' => $dayofweek);
    $stmt = $pdo->prepare('SELECT idschedule
                          FROM schedule s
                          WHERE s.time = :time
                          AND s.dayofweek = :dayofweek
                          LIMIT 1');

    if ($stmt->execute($params)){

        if ($row = $stmt->fetch()){

            //=======================================================================================================
            // Obteniendo canchas
            //=======================================================================================================
            $params = array(':reservation_date' => $reservation_date, ':idschedule' =>  $row['idschedule']);
            $stmt = $pdo->prepare('
                                    SELECT sf.*
                                    FROM soccer_field sf
                                    WHERE NOT EXISTS(
                                                        SELECT 1
                                                        FROM reservation r 
                                                        WHERE r.reservation_date = :reservation_date
                                                         AND r.idsoccer_field = sf.idsoccer_field
                                                         AND r.idschedule = :idschedule
                                                         AND r.idreservation_state = 4
                                                    )');
            if ($stmt->execute($params)){
                if ($stmt->rowCount() > 0){
                    $innerData = array();
                    while($row = $stmt->fetch()){
                        array_push($innerData, array(
                            'idsoccer_field' => $row['idsoccer_field'],
                            'name' => $row['name']));
                    }
                    $data = new customDataSourceResult($innerData);
                } else {
                    returnError('No se encontraron canchas disponibles para el dia y hora seleccionados.');
                }
            } else {
                returnError('Error al intentar consultar las canchas disponibles.');
            }
        } else {
            returnError('No existe un horario configurado para el dia y hora seleccionados.');
        }
    } else {
        returnError('Error al intentar consultar si existe un horario para el dia y hora seleccionados.');
    }
} else if ($type == 'make_reservation'){

    $reservation_date = (new DateTime($_GET['reservation_date']))->format('Y-m-d');
    $time = $_GET['time'];
    $dayofweek = date('w',strtotime($_GET['reservation_date']));
    $dayofweek = ($dayofweek == 0) ? 6 : $dayofweek - 1;

    //=======================================================================================================
    // Obteniendo schedule
    //=======================================================================================================
    $params = array(':time' => $time, ':dayofweek' => $dayofweek);
    $stmt = $pdo->prepare('SELECT idschedule, price
                          FROM schedule
                          WHERE time = :time
                          AND dayofweek = :dayofweek
                          LIMIT 1');
    if ($stmt->execute($params)){
        if ($row = $stmt->fetch()){
            //=======================================================================================================
            // Validando que la cancha esté disponible para el día , hora seleccionados
            //=======================================================================================================
            $price = $row['price'];
            $params = array(
                ':reservation_date' => $reservation_date,
                ':idschedule' =>  $row['idschedule'],
                ':idsoccer_field' => $_GET['soccer_field']);

            $stmt = $pdo->prepare(' SELECT 1
                                    FROM reservation
                                    WHERE reservation_date = :reservation_date
                                    AND idsoccer_field = :idsoccer_field
                                    AND idreservation_state = 4
                                    AND idschedule = :idschedule');
            if ($stmt->execute($params)){

                //=======================================================================================================
                // Si la cancha está disponible, continuar
                //=======================================================================================================
                if ($stmt->rowCount() <= 0){

                    $params = array(
                        ':iduser' => $_SESSION['iduser'],
                        ':reservation_date' => $reservation_date,
                        ':idschedule' =>  $row['idschedule'],
                        ':idsoccer_field' => $_GET['soccer_field'],
                        ':price' => $price);

                    $stmt = $pdo->prepare('INSERT INTO reservation (
                                                iduser, 
                                                reservation_date,
                                                idsoccer_field,
                                                idschedule,
                                                create_date,
                                                idreservation_state,
                                                price)
                                            VALUES (
                                                :iduser,
                                                :reservation_date,
                                                :idsoccer_field,
                                                :idschedule,
                                                now(),
                                                4,
                                                :price)');

                    if ($stmt->execute($params)){

                        $params = array(':idreservation' => $pdo->lastInsertId());
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

                                //=======================================================================
                                //  Inicia envio de correo electronico a usuario
                                //=======================================================================
                                $emailParams = array($reservation_date, $time, $soccer_field, $price);
                                $emailResult = sendEmail('create_reservation',$email,$emailParams);
                                //=======================================================================
                                //  Finaliza envio de correo electronico a usuario
                                //=======================================================================

                                http_response_code(200);
                        } else {
                            returnError('Error al intentar consultar informacion de reserva');
                        }
                    } else {
                        returnError('Error al insertar registro de nueva reservación.');
                    }
                } else {
                    returnError('La cancha seleccionada no está disponible para el horario seleccionado.');
                }
            } else {
                returnError('Error al intentar consultar si la cancha seleccionada está disponible');
            }
        } else {
            returnError('No existe un horario configurado para el dia y hora seleccionados.');
        }
    } else {
        returnError('Error al intentar consultar si existe un horario para el dia y hora seleccionados.');
    }
}

if ($type != 'users' && $type != 'soccer_fields' && $type != 'change_status'
    && $type != 'getAvailableSoccerFields' && $type != 'make_reservation') {

    //========================================================================================================
    //      LLENANDO USUARIOS
    //========================================================================================================
    $users = null;
    $stmt = $pdo->prepare('
                SELECT
                    iduser iduser,
                    CONCAT_WS(\' \',firstname,lastname) username
                FROM user');
    if ($stmt->execute()){
        $innerData = array();
        while($row = $stmt->fetch()){
            array_push($innerData,array('iduser' => $row['iduser'],'username' => $row['username']));
        }
        $users = new customDataSourceResult($innerData);
    }
    $result = &$data->data;

    for ($index = 0, $count = count($result); $index < $count; $index++) {
        $iduser = $result[$index]['iduser'];

        foreach ($users->data as $user) {
            if ($user['iduser'] == $iduser) {
                $result[$index]['username'] = $user;
                break;
            }
        }
    }
    //========================================================================================================
    //      LLENANDO CANCHAS
    //========================================================================================================
    $soccer_fields = $ds->read('soccer_field', array('idsoccer_field', 'name'));
    $result = &$data->data;

    for ($index = 0, $count = count($result); $index < $count; $index++) {
        $idsoccer_field = $result[$index]['idsoccer_field'];

        foreach ($soccer_fields['data'] as $soccer_field) {
            if ($soccer_field['idsoccer_field'] == $idsoccer_field) {
                $result[$index]['soccer_field'] = $soccer_field;
                break;
            }
        }
    }
}

function returnError($message) {
    header('HTTP/1.1 500 ' . $message);
}

// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
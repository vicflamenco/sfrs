<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 10/10/2016
 * Time: 2:19 PM
 */


require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';

$request = json_decode(file_get_contents('php://input'));
$ds = getDatasource();
$pdo = getDbConnection();
$type = $_GET['type'];
$data = null;

if ($type == 'create') {

    // Obteniendo registro a actualizar
    $model = $request->models[0];
    //==================================================================================================
    // Consultando si existe un horario configurado para el día y hora seleccionados
    //==================================================================================================

    if ($model->dayofweek == null){
        returnError('Seleccione un dia de la semana');
    } else {
        $params = array(':dayofweek' => $model->dayofweek, ':time' => $model->time);
        $stmt = $pdo->prepare(' SELECT idschedule, price
                                FROM schedule
                                WHERE dayofweek = :dayofweek
                                AND time = :time
                                LIMIT 1');

        //==================================================================================================
        // Si no se encuentra el Schedule, continuar
        //==================================================================================================
        if ($stmt->execute($params) && !$stmt->fetch()){
            $data = $ds->create('schedule', array('idschedule', 'dayofweek', 'time', 'price'), $request->models, 'idschedule');
        } else {
            returnError('No se guardo el registro. Ya existe un horario configurado para el dia y hora seleccionados.');
        }
    }
}
else if ($type == 'read') {

    // The 'read' method accepts table name, array of columns to select and request parameters as array
    $data = $ds->read('schedule', array('idschedule', 'dayofweek', 'time', 'price'),$request);
}
else  if ($type == 'update') {

    // Obteniendo registro a actualizar
    $model = $request->models[0];
    //==================================================================================================
    // Consultando si existe un horario configurado para el día y hora seleccionados
    //==================================================================================================
    $params = array(':dayofweek' => $model->dayofweek, ':time' => $model->time, 'idschedule' => $model->idschedule);
    $stmt = $pdo->prepare(' SELECT idschedule, price
                                FROM schedule
                                WHERE dayofweek = :dayofweek
                                AND time = :time
                                AND idschedule != :idschedule
                                LIMIT 1');

    //==================================================================================================
    // Si no se encuentra el Schedule, continuar
    //==================================================================================================
    if ($stmt->execute($params) && !$stmt->fetch()){
        $data = $ds->update('schedule', array('idschedule', 'dayofweek', 'time', 'price'), $request->models, 'idschedule');
    } else {
        returnError('No se guardo el registro. Ya existe un horario configurado para el dia y hora seleccionados.');
    }
}
else if ($type == 'destroy') {
    // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
    $data = $ds->destroy('schedule', $request->models, 'idschedule');
}

function returnError($message) {
    header('HTTP/1.1 500 ' . $message);
}

// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
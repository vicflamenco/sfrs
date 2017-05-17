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

$type = $_GET['type'];
$data = null;

if ($type == 'create') {
    // The 'create' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
    $data = $ds->create('soccer_field', array('idsoccer_field', 'name'), $request->models, 'idsoccer_field');
}
else if ($type == 'read') {
    // The 'read' method accepts table name, array of columns to select and request parameters as array
    $data = $ds->read('soccer_field', array('idsoccer_field', 'name'),$request);
}
else  if ($type == 'update') {
    // The 'update' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
    $data = $ds->update('soccer_field', array('idsoccer_field', 'name'), $request->models, 'idsoccer_field');
}
else if ($type == 'destroy') {
    // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
    $data = $ds->destroy('soccer_field', $request->models, 'idsoccer_field');
}


// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
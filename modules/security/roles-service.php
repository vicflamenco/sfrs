<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 10/10/2016
 * Time: 9:53 AM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';

$request = json_decode(file_get_contents('php://input'));
$ds = getDatasource();

if (isset($_GET['type'])){
    $type = $_GET['type'];
    $data = null;

    if ($type == 'create') {
        // The 'create' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
        $data = $ds->create('role', array('idrole', 'rolename'), $request->models, 'idrole');
    }
    else if ($type == 'read') {
        // The 'read' method accepts table name, array of columns to select and request parameters as array
        $data = $ds->read('role', array('idrole', 'rolename'),$request);
    }
    else  if ($type == 'update') {
        // The 'update' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
        $data = $ds->update('role', array('idrole', 'rolename'), $request->models, 'idrole');
    }
    else if ($type == 'destroy') {
        // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
        $data = $ds->destroy('role', $request->models, 'idrole');
    }
} else if (array_key_exists('details', $_GET)) {
    $data = $ds->read('role_module', array('idrole', 'idmodule', 'query', 'create', 'update', 'delete'), $request);
}

// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
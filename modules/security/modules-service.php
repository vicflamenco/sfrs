<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/10/2016
 * Time: 12:23 AM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DAL/dbconnection.php';

$request = json_decode(file_get_contents('php://input'));
$ds = getDatasource();
$pdo = getDbConnection();
$type = $_GET['type'];
$data = null;

if ($type == 'create') {
    // The 'create' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
    $data = $ds->create('module', array('idmodule', 'name', 'view_name', 'is_security_module', 'url'), $request->models, 'idmodule');
}
else if ($type == 'read') {

    $stmt = $pdo->prepare('SELECT * FROM module');
    $innerData = array();
    if ($stmt->execute()){
        while($row = $stmt->fetch()){
            array_push($innerData, array(
               'idmodule' => $row['idmodule'],
                'name' => $row['name'],
                'view_name' => $row['view_name'],
                'is_security_module' => $row['is_security_module'] == 1,
                'url' => $row['url']
            ));
        }
        $data = new customDataSourceResult($innerData);
    }
}
else  if ($type == 'update') {
    // The 'update' method of DataSourceResult accepts table name, array of column names, array of models and the name of the primary key column
    $data = $ds->update('module', array('idmodule', 'name', 'view_name', 'is_security_module', 'url'), $request->models, 'idmodule');
}
else if ($type == 'destroy') {
    // The 'destroy' method of DataSourceResult accepts table name, array of models and the name of the primary key column
    $data = $ds->destroy('module', $request->models, 'idmodule');
}

// Set response content type
header('Content-Type: application/json');

echo json_encode($data);
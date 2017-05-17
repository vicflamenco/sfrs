<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 12:49 AM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/DataSourceResult.php';

function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "sfrs";

    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    //=======================================================================================
    // Configurando MYSQL para que acepte caracteres que no pueden ser representados
    // con códigos ASCII como 'ñ' y tildes.
    //=======================================================================================
    $stmt = $pdo->prepare('SET NAMES \'utf8\'');
    $stmt->execute();

    return $pdo;
}

function getDatasource() {
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "sfrs";

    return new DataSourceResult("mysql:host=$servername;dbname=$dbname", $username, $password);
}

class customDataSourceResult {
    var $total;
    var $data = array();

    function customDataSourceResult($d){
        $this->total = sizeof($d);
        $this->data = $d;
    }
}

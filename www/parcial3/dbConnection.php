<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 14/11/2016
 * Time: 22:29
 */

function getDbConnection(){
    return new mysqli("mysql4.gear.host", "vicflamenco", "victor123!", "parcial3");
}



function getProducts(){

    $sql = "SELECT * FROM producto;";
    $query = mysqli_query(getDbConnection(), $sql);

    $result = array();
    while ($row = mysqli_fetch_assoc($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getProductById($idproducto){

    $sql = "SELECT * FROM producto WHERE idproducto = " . $idproducto . ";";
    $query = mysqli_query(getDbConnection(), $sql);
    $result = array();
    if ($query){
        while ($row = mysqli_fetch_assoc($query)) {
            array_push($result, $row);
        }
    }
    return $result;
}
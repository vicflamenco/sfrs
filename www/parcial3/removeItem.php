<?php
    session_start();
    function removeItem($idproducto, $array) {
        $result = array();
        foreach ($array as $key => &$val) {
            if ($val['idproducto'] != $idproducto) {
                array_push($result, $val);
            }
        }
        $_SESSION['products'] = $result;
    }
    if (!isset($_SESSION['products'])){
        $_SESSION['products'] = array();
    }
    removeItem($_GET['id'],$_SESSION['products']);
    header('Location: cart.php');

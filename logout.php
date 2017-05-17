<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 10/10/2016
 * Time: 12:25 PM
 */

session_start();
session_destroy();

header("Location: login.php");
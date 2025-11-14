<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once file common
require_once('./Commons/env.php');
require_once('./Commons/function.php');

//route

$act = $_GET['act'] ?? '/';

match ($act) {
    //trang chủ
   
    default => notFound(),
}


    ?>
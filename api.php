<?php
header('Content-Type: application/json');
require_once 'Controller.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'];

$controller = new Controller($method, $path);

$controller->process_uri();
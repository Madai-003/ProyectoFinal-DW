<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'banda';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
  http_response_code(500);
  die('Error de conexión: '.$mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
<?php
session_start();
require __DIR__.'/db.php';

if(empty($_POST['usuario']) || empty($_POST['password'])){
  http_response_code(400); die('Faltan credenciales');
}

$usuario = trim($_POST['usuario']);
$pass = $_POST['password'];

$stmt = $mysqli->prepare("SELECT id, usuario, password FROM usuarios WHERE usuario=? OR correo=? LIMIT 1");
$stmt->bind_param('ss', $usuario, $usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if(!$user || !password_verify($pass, $user['password'])){
  http_response_code(401); die('Usuario o contraseña incorrectos');
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['usuario'] = $user['usuario'];

header('Location: '.$_SERVER['HTTP_REFERER'] ?? 'index.php');
exit;
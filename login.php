<?php
session_start();
require __DIR__.'/db.php';

// Siempre responder como TEXTO PLANO para evitar inyecciones del hosting
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (empty($_POST['usuario']) || empty($_POST['password'])) {
  http_response_code(400);
  echo 'Faltan credenciales';
  exit;
}

$usuario = trim($_POST['usuario']);
$pass    = $_POST['password'];

$stmt = $mysqli->prepare("SELECT id, usuario, correo, password FROM usuarios WHERE usuario=? OR correo=? LIMIT 1");
$stmt->bind_param('ss', $usuario, $usuario);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($pass, $user['password'])) {
  http_response_code(401);
  echo 'Usuario o contraseña incorrectos';
  exit;
}

// Login OK
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['usuario'] = $user['usuario'];
$_SESSION['correo']  = $user['correo'];

// Devolver un OK simple (texto)
echo 'ok';
exit;

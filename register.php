<?php
session_start();
require _DIR_.'/db.php';

// Validación rápida
$required = ['nombre','apellido','fecha_nacimiento','correo','telefono','usuario','password'];
foreach($required as $f){ if(empty($_POST[$f])){ http_response_code(400); die('Faltan campos'); } }

// Limpieza básica
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
$telefono = trim($_POST['telefono']);
$usuario = trim($_POST['usuario']);
$pass = $_POST['password'];

if(!$correo){ http_response_code(400); die('Correo inválido'); }
if(strlen($usuario) < 4){ http_response_code(400); die('Usuario demasiado corto'); }
if(strlen($pass) < 6){ http_response_code(400); die('Contraseña mínima de 6 caracteres'); }

// ¿Correo o usuario ya existen?
$check = $mysqli->prepare("SELECT id FROM usuarios WHERE correo=? OR usuario=? LIMIT 1");
$check->bind_param('ss', $correo, $usuario);
$check->execute();
$check->store_result();
if($check->num_rows > 0){ http_response_code(409); die('Correo o usuario ya en uso'); }
$check->close();

// Insert
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("INSERT INTO usuarios(nombre,apellido,fecha_nacimiento,correo,telefono,usuario,password) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param('sssssss', $nombre,$apellido,$fecha_nacimiento,$correo,$telefono,$usuario,$hash);
if(!$stmt->execute()){
  http_response_code(500);
  die('Error al registrar');
}
$stmt->close();

// Autologin
$_SESSION['user_id'] = $mysqli->insert_id;
$_SESSION['usuario'] = $usuario;

header('Location: '.$_SERVER['HTTP_REFERER'] ?? 'index.php');
exit;
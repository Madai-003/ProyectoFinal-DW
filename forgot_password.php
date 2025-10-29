<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/config.php';
require_once __DIR__.'/db.php';
require_once __DIR__.'/mailer_sendgrid.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'msg'=>'Método no permitido']);
  exit;
}

$identity = trim($_POST['identity'] ?? '');
if ($identity === '') {
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'Debes escribir tu correo o usuario.']);
  exit;
}

$stmt = $mysqli->prepare("SELECT id, usuario, correo FROM usuarios WHERE correo=? OR usuario=? LIMIT 1");
$stmt->bind_param('ss', $identity, $identity);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

$genericOkMsg = 'Si el correo/usuario existe, te enviamos un enlace de recuperación (revisa tu correo).';

if (!$user) {
  echo json_encode(['ok'=>true,'msg'=>$genericOkMsg]);
  exit;
}

$token = bin2hex(random_bytes(32));
$stmt  = $mysqli->prepare("
  UPDATE usuarios
     SET reset_token=?,
         reset_expires = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR)
   WHERE id=?
");
$stmt->bind_param('si', $token, $user['id']);
$okSave = $stmt->execute();
$stmt->close();

if (!$okSave) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'No pudimos generar el enlace. Intenta de nuevo.']);
  exit;
}

$link = rtrim(SITE_URL, '/').'/reset_password.php?token='.$token;

$subject = 'Recupera tu contraseña - Silver Road';
$html = '
  <p>Hola <b>'.htmlspecialchars($user['usuario']).'</b>,</p>
  <p>Solicitaste recuperar tu contraseña. Haz clic en el botón:</p>
  <p><a href="'.$link.'" style="background:#e53935;color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;">Restablecer contraseña</a></p>
  <p>O usa este enlace:<br><a href="'.$link.'">'.$link.'</a></p>
  <p>Este enlace expira en 1 hora.</p>
';

$send = sg_send_mail($user['correo'], $subject, $html);

if (!$send['ok']) {
  @file_put_contents(__DIR__.'/log/forgot_error.log',
    '['.date('c')."] status={$send['status']} errno={$send['errno']} err={$send['error']}\n",
    FILE_APPEND
  );
  echo json_encode(['ok'=>true,'msg'=>$genericOkMsg]);
  exit;
}

echo json_encode(['ok'=>true,'msg'=>$genericOkMsg]);

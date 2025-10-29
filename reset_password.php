<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/config.php';
require_once __DIR__.'/db.php';

$token = trim($_GET['token'] ?? '');
$invalid = false;
$userId  = null;

if ($token !== '') {
  $stmt = $mysqli->prepare("
    SELECT id
      FROM usuarios
     WHERE reset_token=? AND reset_expires > UTC_TIMESTAMP()
     LIMIT 1
  ");
  $stmt->bind_param('s', $token);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $stmt->close();
  if ($row) {
    $userId = (int)$row['id'];
  } else {
    $invalid = true;
  }
} else {
  $invalid = true;
}

if ($_SERVER['REQUEST_METHOD']==='POST' && !$invalid && $userId) {
  $pass1 = $_POST['pass1'] ?? '';
  $pass2 = $_POST['pass2'] ?? '';
  if (strlen($pass1) < 6 || $pass1 !== $pass2) {
    $err = 'La contraseña debe tener al menos 6 caracteres y coincidir.';
  } else {
    $hash = password_hash($pass1, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("
      UPDATE usuarios
         SET password=?, reset_token=NULL, reset_expires=NULL
       WHERE id=?
    ");
    $stmt->bind_param('si', $hash, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    if ($ok) {
      $done = true;
    } else {
      $err = 'No pudimos actualizar tu contraseña. Intenta de nuevo.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Restablecer contraseña</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
<style>
body{background:#0f0f10;color:#e9e9e9;font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;}
.center{max-width:460px;margin:48px auto;padding:24px;border-radius:16px;background:#161718;border:1px solid #2b2d31;}
h1{font-size:1.4rem;margin:0 0 12px;}
.alert{padding:12px 14px;border-radius:10px;margin:10px 0;}
.alert-ok{background:#143d2a;color:#b7f0c3;border:1px solid #1f5d3f;}
.alert-err{background:#3d1414;color:#f0b7b7;border:1px solid #5d1f1f;}
input{width:100%;padding:10px;border-radius:10px;border:1px solid #2b2d31;background:#1d1f22;color:#fff;margin:6px 0 12px;}
button{background:#e53935;border:0;color:#fff;padding:10px 16px;border-radius:12px;cursor:pointer;}
button:hover{filter:brightness(1.05);}
</style>
</head>
<body>
<div class="center">
<?php if ($invalid): ?>
  <div class="alert alert-err">El enlace no es válido o ha vencido.</div>
<?php elseif (!empty($done)): ?>
  <div class="alert alert-ok">¡Tu contraseña fue restablecida! Ya puedes iniciar sesión.</div>
  <p><a href="index.php" style="color:#fff">Volver al inicio</a></p>
<?php else: ?>
  <h1>Restablecer contraseña</h1>
  <?php if (!empty($err)): ?><div class="alert alert-err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <form method="post">
    <input type="password" name="pass1" placeholder="Nueva contraseña" required>
    <input type="password" name="pass2" placeholder="Repite la nueva contraseña" required>
    <button type="submit">Guardar contraseña</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>

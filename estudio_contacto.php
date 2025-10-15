<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

if (!isLogged()) { 
  header('Location: login.php'); 
  exit; 
}

$user = currentUser();  
if (!$user || $user['role'] !== 'user') {
  header('Location: admin_mensajes.php');
  exit;
}
?>

<?php
require_once __DIR__.'/header.php';
require_once __DIR__.'/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ok = false; $err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email   = trim($_POST['email']   ?? '');
  $asunto  = trim($_POST['asunto']  ?? '');
  $mensaje = trim($_POST['mensaje'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Correo inválido';
  elseif ($asunto === '' || $mensaje === '')      $err = 'Completa asunto y mensaje';
  else {
    $stmt = $mysqli->prepare("INSERT INTO mensajes_contacto (email, asunto, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $email, $asunto, $mensaje);
    $ok = $stmt->execute();
    if (!$ok) $err = 'No se pudo guardar el mensaje';
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Estudio & Contáctanos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css"/>
<style>
  :root{
    --bg:#0f0f10; --panel:#161718; --panel2:#1d1f22; --text:#e9e9e9; --muted:#b9b9b9;
    --border:#2b2d31; --accent:#e53935; --accent2:#ff6b6b;
  }
  body{ background:var(--bg); color:var(--text); }
  .section{ padding:40px 0; }
  .lead-sm{ color:var(--muted); }
  .panel{ background:var(--panel); border:1px solid var(--border); border-radius:12px; }
  .form-control, .form-select{ background:var(--panel2); color:var(--text); border:1px solid var(--border); }
  .form-control::placeholder{ color:#9ca3af; }
  .btn-primary{ background:var(--accent); border-color:var(--accent); }
  .btn-primary:hover{ background:var(--accent2); border-color:var(--accent2); }
  .map-wrap{ border-radius:12px; overflow:hidden; border:1px solid var(--border); }
  .item{ margin-bottom:.35rem; }
  a, a:hover{ color:#fff; }
</style>
</head>
<body>

<main class="container section" id="estudio">
  <?php if($ok): ?>
    <div class="alert alert-success">¡Gracias! Tu mensaje fue enviado correctamente.</div>
  <?php elseif($err): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <h1 class="h-title mb-4">Nuestro Estudio</h1>

  <div class="row g-4 align-items-start">
    <!-- Texto -->
    <div class="col-lg-6">
      <div class="panel p-4">
        <div class="item"><b>Ubicación:</b> Primera calle B, Zona 8, Quetzaltenango, Colonia Luisa Fernanda.</div>
        <div class="item"><b>Horarios:</b> L–V 10:00–18:00, S 10:00–14:00.</div>
        <div class="item"><b>Servicios:</b> grabación multipista, mezcla, masterización, ensayo.</div>
        <hr>
        <p class="lead-sm mb-0">
          Equipado para bandas en vivo, cabina tratada acústicamente y backline disponible.
          Reserva tu sesión y llevemos tu sonido al siguiente nivel.
        </p>
      </div>
    </div>

    <!-- Mapa -->
    <div class="col-lg-6">
      <div class="map-wrap">
        <div class="ratio ratio-16x9">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d964.0746351651275!2d-91.54742557157975!3d14.864575699099625!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x858e98476ecb296f%3A0x41ac49e47d719e4c!2sZona%2008%2C%20Quetzaltenango!5e0!3m2!1ses!2sgt!4v1756509165022!5m2!1ses!2sgt" 
            width="600" height="450" style="border:0;" 
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</main>

<section class="container section" id="contacto">
  <h2 class="h-title text-center mb-4">Contáctanos</h2>
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <form method="post" class="panel p-4">
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" placeholder="tucorreo@ejemplo.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? ($_SESSION['email'] ?? '')) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Asunto</label>
          <input type="text" name="asunto" class="form-control" placeholder="Reserva, cotización, etc.">
        </div>
        <div class="mb-3">
          <label class="form-label">Mensaje</label>
          <textarea name="mensaje" rows="6" class="form-control" placeholder="Cuéntanos tu proyecto..."></textarea>
        </div>
        <button class="btn btn-primary">Enviar</button>
      </form>
    </div>
  </div>
</section>

</body>
</html>
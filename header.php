<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$user = null;
if (isLogged()) {
  $stmt = $mysqli->prepare("SELECT id, usuario, role FROM usuarios WHERE id=?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
}
?>
<header class="header">
  <div class="Logo">
    <a href="index.php"><img src="img/logo.png" alt="logo"></a>
  </div>

  <button class="nav-toggle" id="navToggle" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
    <span></span><span></span><span></span>
  </button>

  <nav class="main-nav" id="mainNav" aria-label="Navegación principal">
    <a href="index.php">INICIO</a>
    <a href="redes.php">REDES SOCIALES</a>
    <a href="galeria.php">GALERÍA</a>

    <?php if (isLogged()): ?>
      <a href="estudio_contacto.php">CONTÁCTANOS</a>
      <?php if ($user && $user['role'] === 'admin'): ?>
        <a href="admin_panel.php">CONCIERTOS</a>
      <?php else: ?>
        <a href="conciertos.php">CONCIERTOS</a>
      <?php endif; ?>
    <?php endif; ?>

    <div class="nav-auth-mobile">
      <?php if (isLogged()): ?>
        <span>Hola, <strong><?= htmlspecialchars($user['usuario'] ?? $_SESSION['usuario'] ?? '') ?></strong></span>
        <a class="btn-red" href="logout.php">SALIR</a>
      <?php else: ?>
        <button class="btn-red" data-auth="login">INICIAR SESIÓN</button>
        <button class="btn-red" data-auth="register">REGISTRARSE</button>
      <?php endif; ?>
    </div>
  </nav>

  <div class="auth-area">
    <?php if (isLogged()): ?>
      <span>Hola, <strong><?= htmlspecialchars($user['usuario'] ?? $_SESSION['usuario'] ?? '') ?></strong></span>
      <a class="btn-red" href="logout.php">SALIR</a>
    <?php else: ?>
      <button class="btn-red" data-auth="login">INICIAR SESIÓN</button>
      <button class="btn-red" data-auth="register">REGISTRARSE</button>
    <?php endif; ?>
  </div>

  <div class="nav-backdrop" id="navBackdrop" hidden></div>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css?v=7">

</header>
<?php require_once __DIR__.'/flash.php'; render_flash(); ?>

<?php include __DIR__.'/auth.php';?>
<script>
  <?php if (!empty($_SESSION['open_login'])): unset($_SESSION['open_login']); ?>
    document.addEventListener('DOMContentLoaded', ()=>{
      const btn = document.querySelector('[data-auth="login"]');
      if (btn) btn.click();
    });
  <?php endif; ?>
</script>


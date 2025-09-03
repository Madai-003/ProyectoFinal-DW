<?php
require_once __DIR__.'/session.php';
?>
<header class="header">
  <div class="Logo">
    <a href="index.php"><img src="img/logo.png" alt="logo"></a>
  </div>

  <nav class="main-nav">
    <!-- SIEMPRE visibles -->
    <a href="index.php">INICIO</a>
    <a href="redes.php">REDES SOCIALES</a>
    <a href="galeria.php">GALERÍA</a>

    <?php if (isLogged()): ?>
      <!-- SOLO con sesión iniciada -->
      <a href="estudio.php">ESTUDIO DE GRABACIÓN</a>
      <a href="contacto.php">CONTÁCTANOS</a>
      <a href="conciertos.php">CONCIERTOS</a>
    <?php endif; ?>
  </nav>

  <div class="auth-area">
    <?php if (isLogged()): ?>
      <span>Hola, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></span>
      <a class="btn-login" href="logout.php">SALIR</a>
    <?php else: ?>
      <button class="btn-login" data-auth="login">INICIAR SESIÓN</button>
      <button class="btn-register" data-auth="register">REGISTRARSE</button>
    <?php endif; ?>
  </div>
</header>

<?php include __DIR__.'/auth.php';?>
<?php
require_once _DIR_.'/session.php';
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
  <?php
    // Obtener el rol del usuario logueado
    require_once _DIR_.'/db.php';
    $stmt = $mysqli->prepare("SELECT role FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
  ?>
    <!-- Visibles solo logueados -->
    <a href="estudio_contacto.php">CONTÁCTANOS</a>

    <?php if ($user && $user['role'] === 'admin'): ?>
      <a href="admin_panel.php">CONCIERTOS</a>
    <?php else: ?>
      <a href="conciertos.php">CONCIERTOS</a>
    <?php endif; ?>

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

<?php include _DIR_.'/auth.php';?>
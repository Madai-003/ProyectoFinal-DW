<?php if(session_status()===PHP_SESSION_NONE){ session_start(); } ?>
<!-- Modal LOGIN -->
<div id="modal-login" class="modal-auth" style="display:none">
  <div class="modal-box">
    <h3>Iniciar sesión</h3>
    <form method="post" action="login.php">
      <label>Usuario o correo</label>
      <input type="text" name="usuario" required>
      <label>Contraseña</label>
      <input type="password" name="password" required>
      <div class="actions">
        <button type="submit">Entrar</button>
        <button type="button" data-close>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal REGISTER -->
<div id="modal-register" class="modal-auth" style="display:none">
  <div class="modal-box">
    <h3>Registrarse</h3>
    <form method="post" action="register.php">
      <label>Nombre</label>
      <input type="text" name="nombre" required>
      <label>Apellido</label>
      <input type="text" name="apellido" required>
      <label>Fecha de nacimiento</label>
      <input type="date" name="fecha_nacimiento" required>
      <label>Correo electrónico</label>
      <input type="email" name="correo" required>
      <label>Teléfono</label>
      <input type="text" name="telefono" required>
      <label>Nombre de usuario</label>
      <input type="text" name="usuario" required>
      <label>Contraseña</label>
      <input type="password" name="password" required>
      <div class="actions">
        <button type="submit">Crear cuenta</button>
        <button type="button" data-close>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
// abrir/cerrar
document.addEventListener('click', (e)=>{
  if(e.target.matches('[data-auth="login"]')) document.getElementById('modal-login').style.display='flex';
  if(e.target.matches('[data-auth="register"]')) document.getElementById('modal-register').style.display='flex';
  if(e.target.matches('[data-close]') || e.target.closest('.modal-auth')?.isSameNode(e.target)){
    e.target.closest('.modal-auth').style.display='none';
  }
});
</script>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!-- Modal LOGIN -->
<div id="modal-login" class="modal-auth" style="display:none">
  <div class="modal-box">
    <h3>Iniciar sesión</h3>

    <form id="loginForm" method="post" action="login.php" novalidate>
      <label>Usuario o correo</label>
      <input type="text" name="usuario" required>

      <label>Contraseña</label>
      <input type="password" name="password" required>

      <!-- Mensaje de error inline (debajo del password) -->
      <div id="loginError" style="
           color:#ff6b6b;background:rgba(255,0,0,.10);
           padding:8px 10px;border-radius:8px;margin-top:8px;font-size:.95rem;
           display:none;max-height:64px;overflow:hidden;"></div>

      <p style="margin-top:8px">
        <a href="#" id="linkForgot" style="color:#bbb">¿Olvidaste tu contraseña?</a>
      </p>

      <div class="actions">
        <button type="submit" class="btn-red">Entrar</button>
        <button type="button" data-close>Cancelar</button>
      </div>

      <!-- Recuperación de contraseña -->
      <div id="forgotBox" style="display:none;margin-top:12px">
        <label style="display:block;margin-bottom:6px;color:#ddd">Correo o usuario</label>
        <input id="fpIdentity" type="text" placeholder="tu_correo@dominio.com o usuario"
               style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#1a1a1a;color:#fff">
        <button id="fpSend" class="btn-red" style="margin-top:10px" type="button">Enviar enlace</button>
        <div id="fpMsg" style="margin-top:8px;color:#ccc"></div>
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
        <button type="submit" class="btn-red">Crear cuenta</button>
        <button type="button" data-close>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
//  abrir / cerrar modales 
document.addEventListener('click', (e) => {
  const isOpenLogin  = e.target.matches('[data-auth="login"]');
  const isOpenReg    = e.target.matches('[data-auth="register"]');
  const isCloseBtn   = e.target.matches('[data-close]');
  const isBackdrop   = e.target.classList.contains('modal-auth');

  if (isOpenLogin)  document.getElementById('modal-login').style.display = 'flex';
  if (isOpenReg)    document.getElementById('modal-register').style.display = 'flex';
  if (isCloseBtn && e.target.closest('.modal-auth')) e.target.closest('.modal-auth').style.display = 'none';
  if (isBackdrop)   e.target.style.display = 'none';
});

document.addEventListener('keydown', (e)=>{
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-auth').forEach(m => m.style.display='none');
  }
});

(function(){
  const form  = document.getElementById('loginForm');
  const box   = document.getElementById('loginError');

  if (!form) return;

  document.addEventListener('click', (e)=>{
    if (e.target.matches('[data-auth="login"]')) {
      box.style.display = 'none';
      box.textContent   = '';
      form.reset();
    }
  });

  form.addEventListener('submit', async (ev)=>{
    ev.preventDefault();
    box.style.display = 'none';
    box.textContent   = '';

    const fd = new FormData(form);

    try{
      const res  = await fetch(form.action, {
        method:'POST',
        body: fd,
        credentials: 'same-origin'
      });

      const text = (await res.text()).trim();

      if (text === 'ok') {
        location.reload();
        return;
      }

      const looksLikeHtml = /<\s*!?doctype|<\s*html[\s>]/i.test(text);
      box.textContent = looksLikeHtml
        ? 'No se pudo iniciar sesión. Vuelve a intentarlo.'
        : (text || 'No se pudo iniciar sesión.');
      box.style.display = 'block';

    }catch(err){
      box.textContent = 'Error de conexión. Inténtalo de nuevo.';
      box.style.display = 'block';
    }
  });
})();


(function(){
  const linkForgot = document.getElementById('linkForgot');
  const forgotBox  = document.getElementById('forgotBox');
  const btn        = document.getElementById('fpSend');
  const inp        = document.getElementById('fpIdentity');
  const msg        = document.getElementById('fpMsg');

  if (!linkForgot) return;

  linkForgot.addEventListener('click', (e)=>{
    e.preventDefault();
    forgotBox.style.display =
      (forgotBox.style.display === 'none' || !forgotBox.style.display) ? 'block' : 'none';
  });

  btn && btn.addEventListener('click', async ()=>{
    const identity = (inp.value || '').trim();
    if (!identity) { msg.textContent = 'Escribe tu correo o usuario.'; return; }

    btn.disabled = true;
    msg.style.color = '#ccc';
    msg.textContent = 'Enviando...';

    try{
      const res  = await fetch('forgot_password.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ identity })
      });

      let data;
      const text = await res.text();
      try { data = JSON.parse(text); } catch { data = { ok:false, msg:(text||'Sin respuesta del servidor') }; }

      msg.style.color = data.ok ? '#b7f0c3' : '#f0b7b7';
      msg.textContent = data.msg || (data.ok ? 'Listo.' : 'Ocurrió un problema.');

    }catch(e){
      msg.style.color = '#f0b7b7';
      msg.textContent = 'Error de conexión. Inténtalo de nuevo.';
    }finally{
      btn.disabled = false;
    }
  });
})();
</script>


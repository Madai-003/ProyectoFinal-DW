<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Silver Road | Redes & Streaming</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__.'/header.php'; ?>

<main class="container py-4">
  <h1>Síguenos y escucha nuestra música</h1>

  <!-- Streaming -->
  <h2 class="section-title">Plataformas de streaming</h2>
  <p class="subtext">Escúchanos donde prefieras.</p>
  <div class="grid">
    <div class="tile spotify">
      <i class="bi bi-spotify"></i>
      <h5>Spotify</h5>
      <small>@silverroad</small>
      <a href="https://open.spotify.com/artist/3qm84nBOXUEQ2vnTfUTTFC?si=pUjxde4TQYySCsIwbrmuHQ" target="_blank" rel="noopener">Abrir perfil</a>
        <iframe data-testid="embed-iframe" style="border-radius:12px" src="https://open.spotify.com/embed/artist/3qm84nBOXUEQ2vnTfUTTFC?utm_source=generator&theme=0" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
    </div>
    <div class="tile apple">
      <i class="bi bi-apple"></i>
        <h5>Apple Music</h5>
        <small>@silverroad</small>
        <a href="https://music.apple.com/mx/album/back-in-black/574050396" target="_blank" rel="noopener">Ir al perfil</a>
        <iframe allow="autoplay *; encrypted-media *; fullscreen *; clipboard-write" frameborder="0" height="152" style="width:100%;max-width:660px;overflow:hidden;border-radius:10px;" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation" src="https://embed.music.apple.com/mx/album/back-in-black/574050396"></iframe>
    </div>
    <div class="tile ytmusic">
      <i class="bi bi-music-note-beamed"></i>
      <h5>YouTube Music</h5>
      <small>@silverroad</small>
      <a href="https://music.youtube.com/channel/UCJJZxoEUuoz3GDHz90tIocQ?si=NvHZqcAcbR9atOpG" target="_blank" rel="noopener">Abrir perfil</a>
       <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://music.youtube.com/playlist?list=OLAK5uy_kpcRMYtIQuxA385XQpKSOfDOC_22uhNjA">
            <img src="img/img15.jpg" class="card-img-top"  >
        </a>
      </div>
    </div>
    <div class="tile deezer">
      <i class="bi bi-music-player"></i>
      <h5>Deezer</h5>
      <small>@silverroad</small>
      <a href="https://link.deezer.com/s/30TLZJWyAmRyfX7byWOvg" target="_blank" rel="noopener">Abrir perfil</a>
      <iframe title="deezer-widget" src="https://widget.deezer.com/widget/dark/artist/637/top_tracks?tracklist=false" width="100%" height="152" frameborder="0" allowtransparency="true" allow="encrypted-media; clipboard-write"></iframe>
    </div>
    <div class="tile napster">
      <i class="bi bi-soundwave"></i>
      <h5>Napster</h5>
      <small>@silverroad</small>
      <a href="https://www.napster.com/gt/" target="_blank" rel="noopener">Abrir perfil</a>
      <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://www.napster.com/es/">
            <img src="img/img14.jpg" class="card-img-top"  >
        </a>
      </div>
    </div>
  </div>
  <!-- Redes sociales -->
  <h2 class="section-title">Redes sociales</h2>
  <p class="subtext">No te pierdas novedades, fotos y videos.</p>
  <div class="grid">
    <div class="tile youtube">
      <i class="bi bi-youtube"></i>
      <h5>YouTube</h5>
      <small>Canal oficial</small>
      <a href="https://youtube.com/@scorpions?si=fIi_dI9aMF8aTqRM" target="_blank" rel="noopener">Ir al canal</a>
      <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://youtu.be/XHeByMUmvj0?si=SF21IvghBJ8_-04x">
  <img src="img/img10.jpg" class="card-img-top"  >
  </a>
      </div>
    </div>
    <div class="tile instagram">
      <i class="bi bi-instagram"></i>
      <h5>Instagram</h5>
      <small>@Perfil Oficial</small>
      <a href="https://www.instagram.com/acdc?igsh=YTV6cXM1aWJrZmhr" target="_blank" rel="noopener">Abrir perfil</a>
      <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://www.instagram.com/reel/DN7SvwnDKD9/?igsh=NzJ1bGp2N3Q3bWZ3">
  <img src="img/img11.jpg" class="card-img-top"  >
  </a>
      </div>
    </div>
    <div class="tile facebook">
      <i class="bi bi-facebook"></i>
      <h5>Facebook</h5>
      <small>/Perfil Oficial</small>
      <a href="https://www.facebook.com/share/18ozvJ2Xo2/" target="_blank" rel="noopener">Abrir página</a>
      <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://www.facebook.com/share/v/17QKJLC6Fu/">
  <img src="img/img12.jpeg" class="card-img-top"  >
  </a>
      </div>
    </div>
    <div class="tile tiktok">
      <i class="bi bi-tiktok"></i>
      <h5>TikTok</h5>
      <small>@Tiktok Oficial</small>
      <a href="https://www.tiktok.com/@therollingstones?_t=ZM-8zIIWc7hlnJ&_r=1" target="_blank" rel="noopener">Abrir perfil</a>
      <div class="ratio ratio-16x9" style="margin-top:10px">
        <a href="https://vm.tiktok.com/ZMAjN6Gfx/">
  <img src="img/img13.jpg" class="card-img-top"  >
  </a>
      </div>
    </div>
  </div>
</main>
<footer>
  <h6>Derechos reservados por Madai</h6>
  <h6>© 2025 Silver Road | Proyecto Final de Desarrollo Web</h6>

  <a href="https://github.com/Madai-003/ProyectoFinal-DW">
    <img src="img/github.png" width="30" height="30">
    <span>GITHUB</span>
  </a>
</footer>
<script defer src="js/nav.js?v=1"></script>
</body>
</html>
<?php
require_once _DIR_.'/session.php';
if (!isLogged()) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Road</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css"/>
</head>

<body>
    <?php include __DIR__.'/header.php'; ?>
<main class="container py-4">
  <h1 class="mb-3">Próximos conciertos</h1>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="gig">
        <img src="img/img16.jpg" alt="Guatemala City">
        <div class="body">
          <span class="tag">NUEVO</span>
          <h5 class="mt-2 mb-1">Guatemala City — Teatro Lux</h5>
          <p class="mb-1">Sáb, 20 Sep 2025 · 8:00 PM</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="gig">
        <img src="img/img17.jpg" alt="Antigua Guatemala">
        <div class="body">
          <span class="tag">NUEVO</span>
          <h5 class="mt-2 mb-1">Antigua — Ruinas de San José</h5>
          <p class="mb-1">Vie, 10 Oct 2025 · 7:30 PM</p>
         
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="gig">
        <img src="img/img18.jpg" alt="Quetzaltenango">
        <div class="body">
          <h5 class="mt-2 mb-1">Quetzaltenango — Teatro Municipal</h5>
          <p class="mb-1">Sáb, 08 Nov 2025 · 8:00 PM</p>
          
        </div>
      </div>
    </div>
  </div>

  <h2 class="mt-5 mb-3">Conciertos pasados</h2>
  <div class="timeline">
    <div class="item">
      <strong>Ciudad de México — Pepsi Center</strong> <span class="text-muted">· 15 Jun 2025</span>
    </div>
    <div class="item">
      <strong>San Salvador — Anfiteatro CIFCO</strong> <span class="text-muted">· 24 May 2025</span>
    </div>
    <div class="item">
      <strong>Guatemala — Plaza de la Constitución</strong> <span class="text-muted">· 02 Mar 2025</span>
    </div>
  </div>
</main>

<footer>
    <P>Derechos reservados por Madai</P>
    <a href="https://github.com/Madai-003/ProyectoFinal-DW">GITHUG</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js" integrity="sha384-lpyLfhYuitXl2zRZ5Bn2fqnhNAKOAaM/0Kr9laMspuaMiZfGmfwRNFh8HlMy49eQ" crossorigin="anonymous"></script>
</html>
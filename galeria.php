<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Silver Road</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"/>
    
</head>

<body>
    <?php include __DIR__.'/header.php'; ?>

<div id="carouselExample" class="carousel slide">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/img4.jpg" class="d-block w-100"  >
    </div>
    <div class="carousel-item">
      <img src="img/img5.png" class="d-block w-100"  >
    </div>
    <div class="carousel-item">
      <img src="img/img6.png" class="d-block w-100"  >
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<div class="gallery">
<div class="card" style="width: 18rem;">
  <a href="https://open.spotify.com/intl-es/album/0CxPbTRARqKUYighiEY9Sz?si=Nb8Ice_ITGWZNt3vsY_sVg">
      <img src="img/img7.jpg" class="card-img-top"  >
  </a>
  <div class="card-body">
    <p class="card-text">16 CANCIONES</p>
  </div>
</div>

<div class="card" style="width: 18rem;">
  <a href="https://open.spotify.com/album/01Z1nufhjxJVXVDuMRmNEM?si=gECyaMV8TkCpb8iJumJFVg">
    <img src="img/img8.jpg" class="card-img-top"  >
  </a>
  <div class="card-body">
    <p class="card-text">24 CANCIONES</p>
  </div>
</div>
<div class="card" style="width: 18rem;">
  <a href="https://open.spotify.com/intl-es/album/7DUvURQ0wfA1kgG8j99frR?si=bq1JaZkrRA6d-4p_E1B-Hw">
  <img src="img/img9.jpg" class="card-img-top"  >
  </a>
  <div class="card-body">
    <p class="card-text">10 CANCIONES</p>
  </div>
</div>
</div>


<footer>
  <h6>Derechos reservados por Madai</h6>
  <h6>© 2025 Silver Road | Proyecto Final de Desarrollo Web</h6>

  <a href="https://github.com/Madai-003/ProyectoFinal-DW">
    <img src="img/github.png" width="30" height="30">
    <span>GITHUB</span>
  </a>
</footer>
<script defer src="js/nav.js?v=1"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js" integrity="sha384-lpyLfhYuitXl2zRZ5Bn2fqnhNAKOAaM/0Kr9laMspuaMiZfGmfwRNFh8HlMy49eQ" crossorigin="anonymous"></script>
</html>
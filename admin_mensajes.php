<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

if (!isLogged()) { header('Location: login.php'); exit; }
$admin = currentUser();
if (!$admin || $admin['role'] !== 'admin') { http_response_code(403); die('Acceso restringido'); }

$sql = "SELECT 
          id, 
          email, 
          asunto, 
          mensaje, 
          creado_en AS fecha
        FROM mensajes_contacto
        ORDER BY fecha DESC, id DESC";
$res = $mysqli->query($sql);
$mensajes = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mensajes de contacto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css"/>
  <style>
    :root{ --bg:#0f0f10; --panel:#161718; --panel2:#1d1f22; --text:#e9e9e9; --muted:#b9b9b9; --border:#2b2d31; --accent:#e53935; }
    body{ background:var(--bg); color:var(--text); }
    .panel{ background:var(--panel); border:1px solid var(--border); border-radius:12px; }
    .panel2{ background:var(--panel2); border:1px solid var(--border); border-radius:12px; }
    .table{ color:var(--text); }
    .table thead th{ color:#fff; border-bottom:1px solid var(--border); }
    .table tbody td{ color:var(--muted); border-color:var(--border); }
    .badge-soft{ background:rgba(229,57,53,.15); color:#ff9b99; border:1px solid rgba(229,57,53,.35); }
    pre{ white-space:pre-wrap; word-wrap:break-word; margin:0; color:var(--text); }
  </style>
</head>
<body>
<?php include _DIR_.'/header.php'; ?>
<main class="container py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Mensajes de contacto</h1>
    <span class="badge badge-soft rounded-pill px-3 py-2">Admin: <?= htmlspecialchars($admin['usuario']) ?></span>
  </div>

  <div class="panel p-3 mb-3">
    <div class="row g-3 align-items-center">
      <div class="col">
        <span class="text-muted">Total mensajes:</span>
        <span class="fs-5"><?= count($mensajes) ?></span>
      </div>
    </div>
  </div>

  <div class="panel p-3">
    <?php if (!$mensajes): ?>
      <p class="text-muted mb-0">No hay mensajes aún.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Correo</th>
              <th>Asunto</th>
              <th>Mensaje</th>
              <th>Fecha</th>

            </tr>
          </thead>
          <tbody>
          <?php foreach ($mensajes as $m): ?>
            <tr>
              <td><?= (int)$m['id'] ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td><?= htmlspecialchars($m['asunto']) ?></td>
              <td><pre><?= htmlspecialchars($m['mensaje']) ?></pre></td>
              <td><?= isset($m['fecha']) ? date('Y-m-d H:i:s', strtotime($m['fecha'])) : '—' ?></td>

            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
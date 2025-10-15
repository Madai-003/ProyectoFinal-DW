<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

if (!isLogged()) { header('Location: login.php'); exit; }
$admin = currentUser();
if (!$admin || $admin['role'] !== 'admin') { http_response_code(403); die('Acceso restringido'); }

$CONCIERTOS = [
  1 => 'Guatemala City — Teatro Lux',
  2 => 'Antigua — Ruinas de San José',
  3 => 'Quetzaltenango — Teatro Municipal',
];

/* ==== Concierto seleccionado (GET) ==== */
$concierto_id = isset($_GET['concierto_id']) ? (int)$_GET['concierto_id'] : 1;
/* Si el id no existe en BD, intenta usar el primero disponible */
$stmt = $mysqli->prepare("SELECT DISTINCT concierto_id FROM asientos ORDER BY concierto_id");
$stmt->execute(); $rs = $stmt->get_result();
$idsDisponibles = array_map(fn($r)=> (int)$r['concierto_id'], $rs->fetch_all(MYSQLI_ASSOC));
$stmt->close();
if (!$idsDisponibles) { $idsDisponibles = [1]; }
if (!in_array($concierto_id, $idsDisponibles, true)) { $concierto_id = $idsDisponibles[0]; }

$tot = $vend = $disp = 0;
$sql = "SELECT 
           COUNT(*) AS t,
           SUM(estado='VENDIDO') AS v,
           SUM(estado='DISPONIBLE') AS d
         FROM asientos
         WHERE concierto_id=?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $concierto_id);
$stmt->execute();
$stmt->bind_result($tot, $vend, $disp);
$stmt->fetch();
$stmt->close();

$sqlOrdenes = "
  SELECT o.id,
         COALESCE(u.usuario, '-')   AS usuario,
         o.email,
         COUNT(b.id)                AS boletos,
         o.subtotal, o.cargos, o.total,
         o.creado_en
  FROM ordenes o
  LEFT JOIN usuarios u ON u.id = o.usuario_id
  LEFT JOIN boletos b   ON b.orden_id = o.id
  WHERE o.concierto_id = ?
  GROUP BY o.id
  ORDER BY o.creado_en DESC
";
$ordenes = [];
$stmt = $mysqli->prepare($sqlOrdenes);
$stmt->bind_param('i', $concierto_id);
$stmt->execute();
$ordenes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$sqlVendidos = "
  SELECT a.seccion, a.fila, a.col, a.precio,
         b.orden_id, o.email, o.creado_en
  FROM boletos b
  JOIN asientos a ON a.id = b.asiento_id
  JOIN ordenes  o ON o.id = b.orden_id
  WHERE a.concierto_id = ?
  ORDER BY a.seccion, a.fila, a.col
";
$vendidos = [];
$stmt = $mysqli->prepare($sqlVendidos);
$stmt->bind_param('i', $concierto_id);
$stmt->execute();
$vendidos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function q($n){ return 'Q'.number_format((float)$n, 2); }

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel de Administración</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css"/>
  <style>
    :root{
      --bg:#0f0f10; --panel:#161718; --panel2:#1d1f22; --text:#e9e9e9; --muted:#b9b9b9; --border:#2b2d31; --accent:#e53935;
    }
    body{ background:var(--bg); color:var(--text); }
    .panel{ background:var(--panel); border:1px solid var(--border); border-radius:12px; }
    .panel2{ background:var(--panel2); border:1px solid var(--border); border-radius:12px; }
    .form-select, .form-control{ background:var(--panel2); color:var(--text); border:1px solid var(--border); }
    .table{ color:var(--text); }
    .table thead th{ color:#fff; border-bottom:1px solid var(--border); }
    .table tbody td{ color:var(--muted); border-color:var(--border); }
    .badge-soft{ background:rgba(229,57,53,.15); color:#ff9b99; border:1px solid rgba(229,57,53,.35); }
  </style>
</head>
<body>
<?php include __DIR__.'/header.php'; ?>
<main class="container py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Panel de Administración</h1>
    <span class="badge badge-soft rounded-pill px-3 py-2">Admin: <?= htmlspecialchars($admin['usuario']) ?></span>
  </div>

  <!-- Selector de concierto -->
  <form class="panel p-3 mb-4" method="get" action="admin_panel.php">
    <div class="row g-3 align-items-end">
      <div class="col-sm-8 col-md-6 col-lg-4">
        <label class="form-label">Concierto</label>
        <select name="concierto_id" class="form-select" onchange="this.form.submit()">
          <?php foreach ($idsDisponibles as $cid): ?>
            <option value="<?= $cid ?>" <?= $cid===$concierto_id?'selected':'' ?>>
              <?= htmlspecialchars($CONCIERTOS[$cid] ?? ('Concierto #'.$cid)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-light">Ver</button>
      </div>
      <div class="col-12 col-lg-auto ms-lg-auto">
        <div class="d-flex gap-3 flex-wrap">
          <div class="panel2 p-3 text-center">
            <div class="small text-muted">Asientos totales</div>
            <div class="display-6"><?= (int)$tot ?></div>
          </div>
          <div class="panel2 p-3 text-center">
            <div class="small text-muted">Vendidos</div>
            <div class="display-6"><?= (int)$vend ?></div>
          </div>
          <div class="panel2 p-3 text-center">
            <div class="small text-muted">Disponibles</div>
            <div class="display-6"><?= (int)$disp ?></div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="ordenes-tab" data-bs-toggle="tab" data-bs-target="#ordenes"
              type="button" role="tab">Órdenes</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="vendidos-tab" data-bs-toggle="tab" data-bs-target="#vendidos"
              type="button" role="tab">Asientos vendidos</button>
    </li>
  </ul>

  <div class="tab-content panel p-3" id="tabsContent">
    <!-- Órdenes -->
    <div class="tab-pane fade show active" id="ordenes" role="tabpanel" aria-labelledby="ordenes-tab">
      <?php if (!$ordenes): ?>
        <p class="text-muted mb-0">Aún no hay órdenes para este concierto.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th># Orden</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Boletos</th>
                <th>Subtotal</th>
                <th>Cargos</th>
                <th>Total</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ordenes as $o): ?>
              <tr>
                <td><?= (int)$o['id'] ?></td>
                <td><?= htmlspecialchars($o['usuario']) ?></td>
                <td><?= htmlspecialchars($o['email']) ?></td>
                <td><?= (int)$o['boletos'] ?></td>
                <td><?= q($o['subtotal']) ?></td>
                <td><?= q($o['cargos']) ?></td>
                <td><strong><?= q($o['total']) ?></strong></td>
                <td><?= htmlspecialchars($o['creado_en']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Asientos vendidos -->
    <div class="tab-pane fade" id="vendidos" role="tabpanel" aria-labelledby="vendidos-tab">
      <?php if (!$vendidos): ?>
        <p class="text-muted mb-0">No hay asientos vendidos en este concierto.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>Sección</th>
                <th>Fila</th>
                <th>Col</th>
                <th>Precio</th>
                <th># Orden</th>
                <th>Email comprador</th>
                <th>Fecha orden</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vendidos as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['seccion']) ?></td>
                <td><?= (int)$v['fila'] ?></td>
                <td><?= (int)$v['col'] ?></td>
                <td><?= q($v['precio']) ?></td>
                <td><?= (int)$v['orden_id'] ?></td>
                <td><?= htmlspecialchars($v['email']) ?></td>
                <td><?= htmlspecialchars($v['creado_en']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
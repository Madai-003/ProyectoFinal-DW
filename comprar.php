<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

if (!isLogged()) {
  header('Location: login.php');
  exit;
}

$user = currentUser();
if (!$user) {
  header('Location: login.php');
  exit;
}

if ($user['role'] !== 'user') {
  header('Location: admin_panel.php');
  exit;
}

$concierto_id = isset($_GET['concierto_id']) ? (int)$_GET['concierto_id'] : 0;
if ($concierto_id <= 0) { http_response_code(400); echo "Concierto inválido"; exit; }

// Traer asientos del concierto
$stmt = $mysqli->prepare("
  SELECT id, seccion, fila, col, precio, estado
  FROM asientos
  WHERE concierto_id = ?
  ORDER BY seccion, fila, col
");
$stmt->bind_param('i', $concierto_id);
$stmt->execute();
$res = $stmt->get_result();
$asientos = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Agrupar por sección
$porSeccion = [];
$disponibles = 0;
foreach ($asientos as $a) {
  $porSeccion[$a['seccion']][] = $a;
  if (strtoupper($a['estado']) === 'DISPONIBLE') $disponibles++;
}

$fees = ['percent' => 0.10, 'fixed_per_ticket' => 0.00];

$usuario = currentUser();
$emailUsuario = $usuario['correo'] ?? $usuario['email'] ?? '';
$colsPorSeccion = [];
$qCols = $mysqli->prepare("
  SELECT seccion, MAX(col) AS cols
  FROM asientos
  WHERE concierto_id = ?
  GROUP BY seccion
");
$qCols->bind_param('i', $concierto_id);
$qCols->execute();
$rCols = $qCols->get_result();
while ($row = $rCols->fetch_assoc()) {
  $colsPorSeccion[$row['seccion']] = (int)$row['cols'];
}
$qCols->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Comprar boletos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css"/>
  <style>
  :root{
    --bg: #0f0f10;          /* fondo principal */
    --panel: #161718;       /* tarjetas / paneles */
    --panel-2: #1d1f22;     /* panel secundario */
    --text: #e9e9e9;        /* texto principal */
    --muted: #b9b9b9;       /* texto secundario */
    --border: #2b2d31;
    --accent: #e53935;      /* rojo marca */
    --accent-2:#ff6b6b;     /* rojo claro */
    --seat-free:#bdbdbd;    /* gris asiento disponible */
    --seat-sold:#e53935;    /* rojo ocupado */
    --seat-select:#fff;     /* borde selección */
  }

  .theme-rock{
    background: var(--bg);
    color: var(--text);
  }
  .theme-rock h1,.theme-rock h2,.theme-rock h3,.theme-rock h4,.theme-rock h5{
    color: var(--text);
    letter-spacing:.5px;
  }
  .theme-rock .container{
    max-width: 1120px;
  }

  /* Tarjetas y paneles */
  .card, .totales .card, .list-group-item{
    background: var(--panel);
    border: 1px solid var(--border);
    color: var(--text);
  }
  .list-group-item{
    background: var(--panel-2);
  }
  hr{ border-top:1px solid var(--border); }

  /* Controles */
  .form-label{ color: var(--text); font-weight:600; opacity:.9; }
  .form-text{ color: var(--muted); }
  .form-select{
    background: var(--panel-2);
    color: var(--text);
    border:1px solid var(--border);
  }

  /* Botones */
  .btn-primary, .btn-success{
    background: var(--accent);
    border-color: var(--accent);
  }
  .btn-primary:hover, .btn-success:hover{
    background: var(--accent-2);
    border-color: var(--accent-2);
  }
  .btn-outline-secondary{
    color: var(--text);
    border-color: var(--border);
  }
  .btn-outline-secondary:hover{
    background: var(--panel-2);
  }
  .btn-link{ color: var(--accent-2); text-decoration:none; }
  .btn-link:hover{ color:#fff; text-decoration:underline; }

  /* Escenario */
  .stage{
    height: 64px;
    background: linear-gradient(180deg, #9b2b2b, #6f1f1f);
    border: 1px solid #5b1a1a;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(229,57,53,.25) inset, 0 6px 18px rgba(0,0,0,.35);
    margin-bottom: 28px;
  }
  /* ===== Centrar mapa de asientos ===== */
.seccion {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 30px;
}

.seccion-title {
  width: 100%;
  text-align: center;         /* Centra el texto del nombre de la sección */
  color: var(--muted);
  font-weight: 700;
  margin-bottom: 10px;
}

.grid{
  display:grid;
  /* Quita/borra esta: grid-template-columns: repeat(15, 28px); */
  grid-gap:10px 12px;
  padding:14px 10px 22px;
  background: var(--panel);
  border:1px dashed var(--border);
  border-radius:12px;
  margin-bottom:18px;
  justify-content:center; /* ya lo tienes para centrar */
}

  .seat{
    width:28px; height:28px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; user-select:none; cursor:pointer;
    background: var(--seat-free);
    border:1px solid #8a8a8a;
    transition: transform .08s ease, box-shadow .1s ease;
  }
  .seat:hover{ transform: scale(1.06); box-shadow:0 0 0 3px rgba(255,255,255,.08); }
  .seat.sold{
    background: var(--seat-sold);
    border-color:#a21616;
    cursor:not-allowed;
    opacity:.85;
    box-shadow:none;
  }
  .seat.selected{
    outline: 3px solid var(--seat-select);
    box-shadow: 0 0 0 4px rgba(255,255,255,.10);
  }

  /* Leyenda */
  .legend{ color: var(--muted); }
  .legend .dot{ width:14px; height:14px; display:inline-block; border-radius:50%; margin-right:6px; border:1px solid #777; vertical-align:middle; }
  .dot.disp{ background: var(--seat-free); border-color:#8a8a8a; }
  .dot.sold{ background: var(--seat-sold); border-color:#a21616; }
  .dot.sel { background: var(--seat-free); outline:2px solid #fff; }

  /* Totales */
  .totales b{ font-size:1.2rem; }
  .totales .card-body{ background: var(--panel-2); border-radius: 8px; }
  .totales .d-flex span{ color: var(--muted); }

  /* Espaciados responsivos */
  @media (max-width: 768px){
    .grid{ grid-template-columns: repeat(12, 26px); grid-gap: 8px; }
  }
</style>

</head>
<body>
<?php include __DIR__.'/header.php'; ?>
<main class="container py-4">

  <h1 class="mb-3">Compra de boletos</h1>
  <div class="mb-3">
    <label for="cantidad" class="form-label">Selecciona la cantidad de boletos</label>
    <select id="cantidad" class="form-select" style="max-width:220px">
      <?php for ($i=1; $i<=max(1, $disponibles); $i++): ?>
        <option value="<?= $i ?>"><?= $i ?></option>
      <?php endfor; ?>
    </select>
    <div class="form-text"><?= $disponibles ?> asientos disponibles en total.</div>
  </div>

  <hr>

  <h5>Selecciona tus asientos (solo disponibles)</h5>
  <?php foreach ($porSeccion as $seccion => $lista): ?>
    <div class="seccion">
      <div class="seccion-title"><?= htmlspecialchars($seccion) ?></div>
      <?php $cols = $colsPorSeccion[$seccion] ?? 12; ?>
      <div class="grid" data-seccion="<?= htmlspecialchars($seccion) ?>"
          style="grid-template-columns: repeat(<?= $cols ?>, 28px);">
        <?php foreach ($lista as $a): 
          $sold = strtoupper($a['estado']) !== 'DISPONIBLE';
        ?>
          <div class="seat <?= $sold ? 'sold' : '' ?>"
               data-id="<?= (int)$a['id'] ?>"
               data-precio="<?= htmlspecialchars($a['precio']) ?>"
               title="Fila <?= (int)$a['fila'] ?>, Col <?= (int)$a['col'] ?> - Q<?= number_format($a['precio'],2) ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="mt-3 legend">
    <span class="dot disp"></span> Disponible
    &nbsp;&nbsp;<span class="dot sold"></span> Ocupado
    &nbsp;&nbsp;<span class="dot sel"></span> Seleccionado
  </div>

  <hr>

  <h5>Resumen de compra</h5>
  <div class="row">
    <div class="col-md-7">
      <ul id="listaSeleccion" class="list-group mb-3"></ul>
    </div>
    <div class="col-md-5">
      <div class="card totales">
        <div class="card-body">
          <div class="d-flex justify-content-between"><span>Subtotal</span><b id="subtotal">Q0.00</b></div>
          <div class="d-flex justify-content-between"><span>Cargos (servicio)</span><b id="cargos">Q0.00</b></div>
          <hr>
          <div class="d-flex justify-content-between"><span>Total</span><b id="total">Q0.00</b></div>
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button id="btnCancelar" class="btn btn-outline-secondary">Cancelar</button>
        <button id="btnContinuar" class="btn btn-success" disabled>Continuar con la compra</button>
      </div>
      <small class="text-muted d-block mt-2">Los boletos se enviarán a: <b><?= htmlspecialchars($emailUsuario) ?></b></small>
    </div>
  </div>

</main>

<script>
let maxSeleccion = parseInt(document.getElementById('cantidad').value, 10);
let seleccion = new Map();

const fees = <?= json_encode($fees) ?>;
const conciertoId = <?= (int)$concierto_id ?>;

function dinero(n){ return 'Q' + (Number(n||0)).toFixed(2); }

function recalc(){
  const lista = document.getElementById('listaSeleccion');
  lista.innerHTML = '';
  let subtotal = 0, count = 0;
  seleccion.forEach(v => {
    subtotal += Number(v.precio); count++;
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between';
    li.textContent = 'Asiento #' + v.id + ' — ' + dinero(v.precio);
    const btn = document.createElement('button');
    btn.className = 'btn btn-sm btn-link'; btn.textContent = 'quitar';
    btn.onclick = () => toggleSeat(v.el);
    li.appendChild(btn); lista.appendChild(li);
  });
  const cargos = subtotal * fees.percent + count * fees.fixed_per_ticket;
  const total = subtotal + cargos;
  document.getElementById('subtotal').textContent = dinero(subtotal);
  document.getElementById('cargos').textContent   = dinero(cargos);
  document.getElementById('total').textContent    = dinero(total);
  document.getElementById('btnContinuar').disabled = (count !== maxSeleccion || count === 0);
}

function toggleSeat(el){
  if (el.classList.contains('sold')) return;
  const id = el.dataset.id, precio = el.dataset.precio;
  if (seleccion.has(id)) { el.classList.remove('selected'); seleccion.delete(id); }
  else {
    if (seleccion.size >= maxSeleccion) return;
    el.classList.add('selected'); seleccion.set(id, {id, precio, el});
  }
  recalc();
}

document.querySelectorAll('.seat').forEach(el => el.addEventListener('click', () => toggleSeat(el)));
document.getElementById('cantidad').addEventListener('change', e => {
  maxSeleccion = parseInt(e.target.value, 10);
  while (seleccion.size > maxSeleccion) {
    const lastKey = Array.from(seleccion.keys()).pop();
    const v = seleccion.get(lastKey); v.el.classList.remove('selected'); seleccion.delete(lastKey);
  }
  recalc();
});
document.getElementById('btnCancelar').addEventListener('click', () => { window.location.href = 'conciertos.php'; });
document.getElementById('btnContinuar').addEventListener('click', async () => {
  if (seleccion.size !== maxSeleccion) return;
  const asiento_ids = Array.from(seleccion.keys());
  const formData = new FormData();
  formData.append('concierto_id', String(conciertoId));
  formData.append('asientos', JSON.stringify(asiento_ids));
  try {
    const res = await fetch('confirmar_compra.php', { method:'POST', body: formData });
    const data = await res.json();
    if (!res.ok || !data.ok) { alert(data.msg || 'No se pudo completar la compra.'); if (data.reload) location.reload(); return; }
    alert('¡Gracias por tu compra! Los boletos fueron enviados a tu correo.');
    window.location.href = 'conciertos.php';
  } catch (e) { console.error(e); alert('Error de red.'); }
});
recalc();
</script>
</body>
</html>
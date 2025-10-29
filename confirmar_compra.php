<?php
require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

header('Content-Type: application/json');

if (!isLogged()) { http_response_code(401); echo json_encode(['ok'=>false,'msg'=>'Inicia sesión para comprar.']); exit; }

$usuario = currentUser();
$usuario_id = (int)($usuario['id'] ?? 0);
$email = $usuario['correo'] ?? $usuario['email'] ?? null;

$concierto_id = (int)($_POST['concierto_id'] ?? 0);
$asientos = json_decode($_POST['asientos'] ?? '[]', true);
if ($concierto_id <= 0 || !is_array($asientos) || count($asientos) === 0) {
  http_response_code(400); echo json_encode(['ok'=>false, 'msg'=>'Datos inválidos.']); exit;
}

$percent = 0.10;
$fixed_per_ticket = 0.00;

function bindParams(mysqli_stmt $stmt, string $types, array $values) {
  $refs = [];
  $refs[] = $types;
  foreach ($values as $k => $v) { $refs[] = &$values[$k]; }
  return call_user_func_array([$stmt, 'bind_param'], $refs);
}

try {
  $mysqli->begin_transaction();

  $placeholders = implode(',', array_fill(0, count($asientos), '?'));
  $sql = "SELECT id, precio, estado FROM asientos
          WHERE concierto_id = ? AND id IN ($placeholders) FOR UPDATE";

  $stmt = $mysqli->prepare($sql);
  $types = 'i' . str_repeat('i', count($asientos));
  $params = array_merge([$concierto_id], array_map('intval', $asientos));
  bindParams($stmt, $types, $params);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  if (count($rows) !== count($asientos)) {
    throw new Exception('Algunos asientos no existen.');
  }

  $subtotal = 0.0;
  foreach ($rows as $r) {
    if (strtoupper($r['estado']) !== 'DISPONIBLE') {
      throw new Exception('Un asiento ya no está disponible. Intenta de nuevo.');
    }
    $subtotal += (float)$r['precio'];
  }
  $cargos = $subtotal * $percent + count($rows) * $fixed_per_ticket;
  $total  = $subtotal + $cargos;

  $stmt = $mysqli->prepare("INSERT INTO ordenes (concierto_id, usuario_id, email, subtotal, cargos, total, creado_en) VALUES (?, ?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param('iisddd', $concierto_id, $usuario_id, $email, $subtotal, $cargos, $total);
  $stmt->execute();
  $orden_id = $stmt->insert_id;
  $stmt->close();

  $insBoleto = $mysqli->prepare("INSERT INTO boletos (orden_id, asiento_id, precio) VALUES (?, ?, ?)");
  $updSeat   = $mysqli->prepare("UPDATE asientos SET estado='VENDIDO' WHERE id = ?");

  foreach ($rows as $r) {
    $aid = (int)$r['id']; $precio = (float)$r['precio'];
    $insBoleto->bind_param('iid', $orden_id, $aid, $precio);
    $insBoleto->execute();
    $updSeat->bind_param('i', $aid);
    $updSeat->execute();
  }
  $insBoleto->close(); $updSeat->close();

  $mysqli->commit();

  echo json_encode(['ok'=>true, 'orden_id'=>$orden_id, 'total'=>$total]);
} catch (Throwable $e) {
  if ($mysqli->errno) { /* noop */ }
  if ($mysqli->begin_transaction) { /* avoid notice */ }
  $mysqli->rollback();
  http_response_code(409);
  echo json_encode(['ok'=>false, 'msg'=>$e->getMessage(), 'reload'=>true]);
}

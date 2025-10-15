<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLogged(): bool {
  return !empty($_SESSION['user_id']);
}

function currentUser(): ?array {
  if (!isLogged()) return null;
  require _DIR_.'/db.php';
  $uid = (int)$_SESSION['user_id'];
  $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE id = ?");
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc() ?: null;
  $stmt->close();
  return $user;
}
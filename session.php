<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLogged(): bool {
  return !empty($_SESSION['user_id']);
}
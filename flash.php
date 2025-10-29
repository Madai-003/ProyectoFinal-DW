<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function set_flash(string $type, string $text): void {
  $_SESSION['flash'][] = ['type'=>$type, 'text'=>$text];
}

function render_flash(): void {
  if (empty($_SESSION['flash'])) return;
  $msgs = $_SESSION['flash'];
  unset($_SESSION['flash']);
  echo '<div class="sr-flash-wrap">';
  foreach ($msgs as $m) {
    $t = htmlspecialchars($m['text'], ENT_QUOTES, 'UTF-8');
    $type = $m['type'];
    echo '<div class="sr-flash sr-flash--'.$type.'">
            <span>'.$t.'</span>
            <button class="sr-flash__close" aria-label="Cerrar">&times;</button>
          </div>';
  }
  echo '</div>';
  echo "<script>
    document.querySelectorAll('.sr-flash__close').forEach(b=>{
      b.addEventListener('click',()=>b.parentElement.remove());
    });
    // Autocierre a los 5s
    setTimeout(()=>document.querySelectorAll('.sr-flash').forEach(n=>n.remove()), 5000);
  </script>";
}

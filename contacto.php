<?php
// Mensajes de estado
$success = "";
$error   = "";

// conexión centralizada:
require_once __DIR__ . '/db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email   = isset($_POST["email"])   ? trim($_POST["email"])   : "";
    $asunto  = isset($_POST["asunto"])  ? trim($_POST["asunto"])  : "";
    $mensaje = isset($_POST["mensaje"]) ? trim($_POST["mensaje"]) : "";

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresa un email válido.";
    } elseif ($asunto === "") {
        $error = "Ingresa un asunto.";
    } elseif ($mensaje === "") {
        $error = "Escribe tu mensaje.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO mensajes_contacto (email, asunto, mensaje) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $email, $asunto, $mensaje);
            if ($stmt->execute()) {
                $success = "¡Gracias! Tu mensaje fue enviado correctamente.";
                // Limpia campos
                $email = $asunto = $mensaje = "";
            } else {
                $error = "Hubo un problema al guardar el mensaje. Inténtalo de nuevo.";
            }
            $stmt->close();
        } else {
            $error = "No se pudo preparar la consulta.";
        }
    }
}
?>
<?php
require_once __DIR__.'/session.php';
if (!isLogged()) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Silver Road - Contáctanos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include __DIR__.'/header.php'; ?>

<main class="container py-4" style="max-width:720px">
  <h1 class="mb-3">Contáctanos</h1>

  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form action="contacto.php" method="POST" class="row g-3 needs-validation" novalidate>
    <div class="col-12">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" required
             value="<?php echo isset($email) ? htmlspecialchars($email) : '';?>">
      <div class="invalid-feedback">Ingresa un email válido.</div>
    </div>
    <div class="col-12">
      <label class="form-label">Asunto</label>
      <input type="text" name="asunto" class="form-control" required
             value="<?php echo isset($asunto) ? htmlspecialchars($asunto) : '';?>">
      <div class="invalid-feedback">Ingresa un asunto.</div>
    </div>
    <div class="col-12">
      <label class="form-label">Mensaje</label>
      <textarea name="mensaje" rows="5" class="form-control" required><?php
        echo isset($mensaje) ? htmlspecialchars($mensaje) : '';
      ?></textarea>
      <div class="invalid-feedback">Escribe tu mensaje.</div>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Enviar</button>
    </div>
  </form>

  <script>
    (() => {
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
</main>

<footer>
    <P>Derechos reservados por Madai</P>
    <a href="https://github.com/Madai-003/ProyectoFinal-DW">GITHUG</a>
</footer>
</body>
</html>
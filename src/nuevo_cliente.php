<?php
// nuevo_cliente.php — Alta de reserva — versión con PK id
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexion = mysqli_connect("mysql-alexgargo78.alwaysdata.net", "432730_", "Lequio.78", "alexgargo78_reserva-restaurante");
$conexion->set_charset("utf8mb4");

//$conexion = mysqli_connect("db", "root", "test", "reserva-restaurante");

$mensaje = ""; $tipoMsg = "warning";

$nombre     = $_POST["nombre"]     ?? "";
$telefono   = $_POST["telefono"]   ?? "";
$email      = $_POST["email"]      ?? "";
$fecha      = $_POST["fecha"]      ?? "";
$hora       = $_POST["hora"]       ?? "";
$comensales = $_POST["comensales"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($nombre === "" || $telefono === "" || $email === "" || $fecha === "" || $hora === "" || $comensales === "") {
    $mensaje = "❌ Faltan campos obligatorios."; $tipoMsg = "danger";
  } else {
    try {
      $stmt = $conexion->prepare("INSERT INTO reserva (nombre, telefono, email, fecha, hora, comensales) VALUES (?,?,?,?,?,?)");
      $stmt->bind_param("sssssi", $nombre, $telefono, $email, $fecha, $hora, $comensales);
      $stmt->execute();
      header("Location: index.php?msg=add_ok"); exit;
    } catch (mysqli_sql_exception $e) {
      if ($e->getCode() === 1062) {
        $mensaje = "⚠️ Ya existe una reserva con ese email en esa fecha y hora."; $tipoMsg = "warning";
      } else {
        $mensaje = "❌ Error: " . htmlspecialchars($e->getMessage()); $tipoMsg = "danger";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva reserva</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container p-4" id="principal">
  <div class="card p-3 glass-card">
    <h1 class="mb-3 text-center">Nueva reserva</h1>

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= htmlspecialchars($tipoMsg) ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form action="" method="post" class="mt-3">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <input class="form-control" type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input class="form-control" type="tel" name="telefono" value="<?= htmlspecialchars($telefono) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Fecha</label>
          <input class="form-control" type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Hora</label>
          <input class="form-control" type="time" name="hora" value="<?= htmlspecialchars($hora) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Comensales</label>
          <input class="form-control" type="number" min="1" name="comensales" value="<?= htmlspecialchars($comensales) ?>" required>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Guardar</button>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>

<?php
// index.php — Reservas Restaurante (Listado + edición inline + alta)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexion = mysqli_connect("mysql-alexgargo78.alwaysdata.net", "432730_", "Lequio.78", "alexgargo78_reserva-restaurante");
$conexion->set_charset("utf8mb4");
//$conexion = mysqli_connect("db", "root", "test", "reserva-restaurante");

/* ----------------------------
   Ordenación por columnas (GET/POST)
----------------------------- */
$permitidas = ['nombre','telefono','email','fecha','hora','comensales'];
$ordenParam = $_GET['orden'] ?? $_POST['orden'] ?? 'fecha';
$dirParam   = $_GET['dir']   ?? $_POST['dir']   ?? 'asc';

$orden = in_array($ordenParam, $permitidas, true) ? $ordenParam : 'fecha';
$dir   = strtolower($dirParam) === 'desc' ? 'desc' : 'asc'; // default asc

// para conservar los parámetros de orden en redirecciones
$queryOrden = "orden={$orden}&dir={$dir}";

/* ----------------------------
   Mensajes por querystring
----------------------------- */
$mensaje = ""; $tipoMsg = "info";
if (!empty($_GET["msg"])) {
  switch ($_GET["msg"]) {
    case "add_ok":  $mensaje="✅ Reserva añadida correctamente."; $tipoMsg="success"; break;
    case "dup":     $mensaje="⚠️ Ya existe una reserva con ese nombre."; $tipoMsg="warning"; break;
    case "err":     $mensaje="❌ " . htmlspecialchars($_GET["text"] ?? "Se produjo un error."); $tipoMsg="danger"; break;
    case "del_ok":  $mensaje="🗑️ Reserva eliminada."; $tipoMsg="secondary"; break;
    case "upd_ok":  $mensaje="✅ Reserva actualizada."; $tipoMsg="success"; break;
  }
}

// Acciones de eliminar / modificar / actualizar
$accion      = $_POST["accion"]      ?? "";
$nombre      = $_POST["nombre"]      ?? "";
$telefono    = $_POST["telefono"]    ?? "";
$email       = $_POST["email"]       ?? "";
$fecha       = $_POST["fecha"]       ?? "";
$hora        = $_POST["hora"]        ?? "";
$comensales  = $_POST["comensales"]  ?? "";
$nombreAnt   = $_POST["nombreAnt"]   ?? "";

try {
  if ($accion === "eliminar") {
    $stmt = $conexion->prepare("DELETE FROM reserva WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    header("Location: index.php?msg=del_ok&$queryOrden");
    exit;
  }

  if ($accion === "actualizar") {
    if ($nombre === "" || $telefono === "" || $email === "" || $fecha === "" || $hora === "" || $comensales === "") {
      header("Location: index.php?msg=err&text=Faltan+campos+obligatorios&$queryOrden");
      exit;
    }
    // Si cambia el 'nombre' (PK), comprobar duplicado
    if ($nombre !== $nombreAnt) {
      $check = $conexion->prepare("SELECT 1 FROM reserva WHERE nombre = ?");
      $check->bind_param("s", $nombre);
      $check->execute(); $check->store_result();
      if ($check->num_rows > 0) {
        header("Location: index.php?msg=err&text=Nombre+duplicado+en+actualizacion&$queryOrden");
        exit;
      }
      $upd = $conexion->prepare("UPDATE reserva SET nombre=?, telefono=?, email=?, fecha=?, hora=?, comensales=? WHERE nombre=?");
      $upd->bind_param("sssssis", $nombre, $telefono, $email, $fecha, $hora, $comensales, $nombreAnt);
      $upd->execute();
      header("Location: index.php?msg=upd_ok&$queryOrden");
      exit;
    } else {
      $upd = $conexion->prepare("UPDATE reserva SET telefono=?, email=?, fecha=?, hora=?, comensales=? WHERE nombre=?");
      $upd->bind_param("ssssis", $telefono, $email, $fecha, $hora, $comensales, $nombreAnt);
      $upd->execute();
      header("Location: index.php?msg=upd_ok&$queryOrden");
      exit;
    }
  }
} catch (Throwable $e) {
  header("Location: index.php?msg=err&text=" . urlencode($e->getMessage()) . "&$queryOrden");
  exit;
}

/* ----------------------------
   Consulta con ORDER BY seguro
----------------------------- */
$sql = "SELECT nombre, telefono, email, fecha, hora, comensales FROM reserva ORDER BY $orden $dir";
$result = $conexion->query($sql);

/* ----------------------------
   Helper para cabeceras sortables
----------------------------- */
function th_sort(string $label, string $col, string $ordenActual, string $dirActual): string {
  $isActive = ($col === $ordenActual);
  // Alterna dirección si se vuelve a pulsar la misma columna
  $newDir = ($isActive && $dirActual === 'asc') ? 'desc' : 'asc';
  $arrow = '';
  if ($isActive) $arrow = $dirActual === 'asc' ? ' ▲' : ' ▼';
  $href = "index.php?orden={$col}&dir={$newDir}";
  return "<a href=\"$href\" class=\"text-white text-decoration-none\">$label$arrow</a>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservas Restaurante</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container p-4" id="principal">
    <div class="card p-3 glass-card">
      <h1 class="mb-3 text-center">Reservas</h1>

      <div class="text-center mb-3">
        <a href="nuevo_cliente.php" class="btn btn-success">
          <i class="bi bi-plus"></i> Nueva reserva
        </a>
      </div>

      <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($tipoMsg) ?>"><?= $mensaje ?></div>
      <?php endif; ?>

      <table class="table table-striped align-middle glass-table">
        <thead class="table-dark">
          <tr>
            <th><?= th_sort('Nombre',     'nombre',     $orden, $dir) ?></th>
            <th><?= th_sort('Teléfono',   'telefono',   $orden, $dir) ?></th>
            <th><?= th_sort('Email',      'email',      $orden, $dir) ?></th>
            <th><?= th_sort('Fecha',      'fecha',      $orden, $dir) ?></th>
            <th><?= th_sort('Hora',       'hora',       $orden, $dir) ?></th>
            <th><?= th_sort('Comensales', 'comensales', $orden, $dir) ?></th>
            <th style="width:1%"></th>
            <th style="width:1%"></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php if ($accion === "modificar" && $nombre === $row['nombre']): ?>
              <?php $formId = 'f'.md5($row['nombre']); ?>
              <form id="<?= $formId ?>" action="index.php?<?= $queryOrden ?>" method="post"></form>
              <tr>
                <td>
                  <input class="form-control" type="text" name="nombre"
                         value="<?= htmlspecialchars($row['nombre']) ?>" form="<?= $formId ?>">
                  <input type="hidden" name="accion" value="actualizar" form="<?= $formId ?>">
                  <input type="hidden" name="nombreAnt" value="<?= htmlspecialchars($row['nombre']) ?>" form="<?= $formId ?>">
                  <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>" form="<?= $formId ?>">
                  <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>" form="<?= $formId ?>">
                </td>
                <td><input class="form-control" type="text"   name="telefono"   value="<?= htmlspecialchars($row['telefono']) ?>" form="<?= $formId ?>"></td>
                <td><input class="form-control" type="email"  name="email"      value="<?= htmlspecialchars($row['email']) ?>"    form="<?= $formId ?>"></td>
                <td><input class="form-control" type="date"   name="fecha"      value="<?= htmlspecialchars($row['fecha']) ?>"    form="<?= $formId ?>"></td>
                <td><input class="form-control" type="time"   name="hora"       value="<?= htmlspecialchars($row['hora']) ?>"     form="<?= $formId ?>"></td>
                <td><input class="form-control" type="number" name="comensales" value="<?= htmlspecialchars($row['comensales']) ?>" min="1" form="<?= $formId ?>"></td>
                <td>
                  <button class="btn btn-success btn-sm w-100" type="submit" form="<?= $formId ?>">
                    <i class="bi bi-check-lg"></i> Guardar
                  </button>
                </td>
                <td>
                  <a href="index.php?<?= $queryOrden ?>" class="btn btn-secondary btn-sm w-100">
                    <i class="bi bi-x-lg"></i> Cancelar
                  </a>
                </td>
              </tr>
            <?php else: ?>
              <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['hora']) ?></td>
                <td><?= htmlspecialchars($row['comensales']) ?></td>
                <td>
                  <form action="index.php?<?= $queryOrden ?>" method="post" class="m-0">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
                    <button class="btn btn-danger btn-sm w-100" type="submit">
                      <i class="bi bi-trash"></i> Borrar
                    </button>
                  </form>
                </td>
                <td>
                  <form action="index.php?<?= $queryOrden ?>" method="post" class="m-0">
                    <input type="hidden" name="accion" value="modificar">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
                    <button class="btn btn-primary btn-sm w-100" type="submit">
                      <i class="bi bi-pencil"></i> Modificar
                    </button>
                  </form>
                </td>
              </tr>
            <?php endif; ?>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

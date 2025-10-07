enActual, string $dirActual): string {
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
            <?php if ($
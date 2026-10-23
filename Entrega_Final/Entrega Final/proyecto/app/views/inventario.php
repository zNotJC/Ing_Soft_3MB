<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JOJOLOS - Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/global.css">
</head>
<body data-es-administrador="<?= $esAdministrador ? 'true' : 'false' ?>">
<header class="container-fluid py-3"><nav class="navbar navbar-expand-lg"><a class="navbar-brand" href="dashboard.php">JOJOLOS</a><div class="navbar-nav ms-auto"><a class="nav-link" href="dashboard.php">Inicio</a><a class="nav-link" href="tickets.php">Tickets</a><a class="nav-link" href="solicitudes.php">Solicitudes</a><a class="nav-link" href="prestamos.php">Préstamos</a><a class="nav-link" href="historial.php">Historial</a><a class="nav-link" href="/proyecto/app/controllers/cerrarSesion.php">Cerrar sesión</a></div></nav></header>
<main class="container py-4">
<h1>Inventario de equipos</h1>
<p><?= $esAdministrador ? 'Gestiona las altas, modificaciones y bajas de equipos.' : 'Consulta los equipos registrados.' ?></p>
<?php if ($esAdministrador): ?>
<form id="formEquipo" class="card card-body mb-4">
<input type="hidden" id="idEquipo" name="idEquipo">
<div class="row g-3">
<div class="col-md-4"><label for="nombre" class="form-label">Nombre</label><input class="form-control" id="nombre" name="nombre" maxlength="100" required></div>
<div class="col-md-4"><label for="numeroSerie" class="form-label">Número de serie</label><input class="form-control" id="numeroSerie" name="numeroSerie" maxlength="100" required></div>
<div class="col-md-4"><label for="idLugar" class="form-label">Lugar</label><select class="form-select" id="idLugar" name="idLugar" required><option value="">Cargando lugares...</option></select></div>
</div>
<div class="mt-3 d-flex gap-2"><button class="btn btn-primary" type="submit" id="guardarEquipo">Registrar equipo</button><button class="btn btn-secondary" type="button" id="cancelarEdicion" hidden>Cancelar edición</button></div>
</form>
<?php endif; ?>
<div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>ID</th><th>Nombre</th><th>Número de serie</th><th>Lugar</th><?php if ($esAdministrador): ?><th>Acciones</th><?php endif; ?></tr></thead><tbody id="tablaEquipos"><tr><td colspan="5">Cargando inventario...</td></tr></tbody></table></div>
<p id="mensajeInventario" role="status" aria-live="polite"></p>
</main>
<script src="assets/js/inventario.js"></script>
</body>
</html>

<?php
$rolActual = (string) ($_SESSION['rol'] ?? '');
$paginaActual = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$enlaces = [
    ['texto' => 'Inicio', 'ruta' => 'dashboard.php', 'roles' => ['Administrador', 'Docente', 'Tecnico']],
    ['texto' => 'Gestionar usuarios', 'ruta' => 'usuarios.php', 'roles' => ['Administrador']],
    ['texto' => 'Inventario y laboratorios', 'ruta' => 'inventario.php', 'roles' => ['Administrador', 'Tecnico', 'Docente']],
    ['texto' => 'Tickets', 'ruta' => 'tickets.php', 'roles' => ['Administrador', 'Docente', 'Tecnico']],
    ['texto' => 'Solicitudes', 'ruta' => 'solicitudes.php', 'roles' => ['Administrador', 'Docente', 'Tecnico']],
    ['texto' => 'Préstamos', 'ruta' => 'prestamos.php', 'roles' => ['Administrador', 'Docente', 'Tecnico']],
    ['texto' => 'Historial', 'ruta' => 'historial.php', 'roles' => ['Administrador', 'Docente', 'Tecnico']],
];
?>
<header class="site-header">
    <nav class="navbar navbar-expand-xl" aria-label="Navegación principal">
        <a class="navbar-brand" href="dashboard.php">JOJOLOS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-xl-center">
                <?php foreach ($enlaces as $enlace): ?>
                    <?php if (in_array($rolActual, $enlace['roles'], true)): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $paginaActual === $enlace['ruta'] ? ' active' : '' ?>" href="<?= htmlspecialchars($enlace['ruta'], ENT_QUOTES, 'UTF-8') ?>"<?= $paginaActual === $enlace['ruta'] ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($enlace['texto'], ENT_QUOTES, 'UTF-8') ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <li class="nav-item nav-logout-item"><a class="btnCerrarSesion" href="cerrarSesion.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </nav>
</header>

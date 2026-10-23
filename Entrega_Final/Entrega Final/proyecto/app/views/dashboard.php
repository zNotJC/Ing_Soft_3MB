<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>JOJOLOS - Panel Principal</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/global.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/dashboard.css"
    >

</head>

<body>

<header>

    <nav class="navbar navbar-expand-lg">

        <a
            class="navbar-brand"
            href="dashboard.php"
        >
            JOJOLOS
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Menú"
        >

            <span class="navbar-toggler-icon"></span>

        </button>

        <div
            class="collapse navbar-collapse"
            id="menuPrincipal"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="dashboard.php"
                    >
                        Inicio
                    </a>

                </li>

                <?php if (!empty($_SESSION["administrador"])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php">Gestionar usuarios</a>
                    <a class="nav-link" href="inventario.php">Inventario</a>
                    <a class="nav-link" href="catalogos.php">Catálogos</a>
                </li>
                <?php elseif (!empty($_SESSION["tecnico"])): ?>
                <li class="nav-item"><a class="nav-link" href="inventario.php">Inventario</a></li>
                <?php endif; ?>

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="tickets.php"
                    >
                        Tickets
                    </a>

                </li>

                <?php if ($esDocente || $esTecnico): ?>
                <li class="nav-item"><a class="nav-link" href="solicitudes.php">Solicitudes</a></li>
                <?php endif; ?>

                <?php if ($esAdministrador || $esDocente): ?>
                <li class="nav-item"><a class="nav-link" href="prestamos.php">Préstamos</a></li>
                <?php endif; ?>

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="historial.php"
                    >
                        Historial
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="btnCerrarSesion"
                        href="/proyecto/app/controllers/cerrarSesion.php"
                    >
                        Cerrar Sesión
                    </a>

                </li>

            </ul>

        </div>

    </nav>

</header>

<main>

    <section class="bienvenida">

        <h1>
            Panel Principal
        </h1>

        <p>
            Usuario:
            <?= htmlspecialchars($usuario) ?>
        </p>

        <p>
            JOJOLOS es una plataforma diseñada para centralizar
            la gestión de incidencias, solicitudes y préstamos
            de equipos dentro de los laboratorios informáticos.
            El sistema permite registrar información, realizar
            seguimientos y consultar estadísticas para mejorar
            la organización de los recursos tecnológicos.
        </p>

    </section>

    <section class="accesos-rapidos">

        <a
            href="tickets.php"
            class="acceso"
        >
            Gestionar Tickets
        </a>

        <a
            href="solicitudes.php"
            class="acceso"
        >
            Gestionar Solicitudes
        </a>

        <a
            href="prestamos.php"
            class="acceso"
        >
            Gestionar Préstamos
        </a>

        <a
            href="historial.php"
            class="acceso"
        >
            Ver Historial
        </a>

    </section>

    <?php if ($esAdministrador || $esTecnico): ?>
    <section class="resumen">

        <article>

            <h2>
                Tickets Pendientes
            </h2>

            <p>
                <span id="ticketsPendientes">0</span>
            </p>

        </article>

        <article>

            <h2>
                Tickets Resueltos
            </h2>

            <p>
                <span id="ticketsResueltos">0</span>
            </p>

        </article>

        <article>

            <h2>
                Solicitudes Activas
            </h2>

            <p>
                <span id="solicitudesActivas">0</span>
            </p>

        </article>

        <article>

            <h2>
                Préstamos Activos
            </h2>

            <p>
                <span id="prestamosActivos">0</span>
            </p>

        </article>

    </section>
    <section>

        <h2>
            Últimos Accesos al Sistema
        </h2>

        <table class="table">

            <thead>

                <tr>

                    <th>
                        Usuario
                    </th>

                    <th>
                        Rol
                    </th>

                    <th>
                        Fecha y Hora
                    </th>

                </tr>

            </thead>
            <tbody id="tablaAccesos">

                <tr>
                    <td colspan="3">Cargando...</td>
                </tr>

            </tbody>

        </table>

    </section>
    <?php endif; ?>

</main>

<footer>

    <p>
        JOJOLOS - Sistema de Gestión de Laboratorios
    </p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($esAdministrador || $esTecnico): ?>
<script src="assets/js/dashboard.js"></script>
<?php endif; ?>

</body>

</html>
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

<?php require __DIR__ . "/partials/navbar.php"; ?>

<main>

    <section class="bienvenida">

        <h1>
            <?= $esDocente ? "¡Bienvenido/a a JOJOLOS!" : "Panel Principal" ?>
        </h1>

        <div class="identidad-usuario" aria-label="Datos de sesión">
            <p><strong>Usuario:</strong> <?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?></p>
            <p><strong>Rol:</strong> <?= htmlspecialchars((string) (($_SESSION["rol"] ?? "") === "Tecnico" ? "Técnico" : ($_SESSION["rol"] ?? "Sin rol")), ENT_QUOTES, "UTF-8") ?></p>
        </div>

        <p class="descripcion-plataforma">
            JOJOLOS es una plataforma diseñada para centralizar
            la gestión de incidencias, solicitudes y préstamos
            de equipos dentro de los laboratorios informáticos.
            El sistema permite registrar información, realizar
            seguimientos y consultar estadísticas para mejorar
            la organización de los recursos tecnológicos.
        </p>

    </section>

    <section class="accesos-rapidos">

        <a href="tickets.php" class="acceso"><?= $esTecnico || $esDocente ? 'Gestionar Tickets' : 'Consultar Tickets' ?></a>

        <?php if ($esDocente || $esTecnico || $esAdministrador): ?>
        <a href="solicitudes.php" class="acceso"><?= $esTecnico ? 'Gestionar Solicitudes' : ($esAdministrador ? 'Consultar Solicitudes' : 'Mis Solicitudes') ?></a>
        <?php endif; ?>

        <?php if ($esAdministrador): ?>
        <a href="prestamos.php" class="acceso">Gestionar Préstamos</a>
        <?php endif; ?>

        <a href="historial.php" class="acceso">Ver Historial</a>

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

        <article>
            <h2>Equipos Registrados</h2>
            <p><span id="equiposRegistrados">0</span></p>
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
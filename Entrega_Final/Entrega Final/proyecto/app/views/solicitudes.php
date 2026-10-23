<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>JOJOLOS - Gestión de Solicitudes</title>

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
        href="assets/css/solicitudes.css"
    >

</head>

<body data-es-docente="<?= $esDocente ? "true" : "false" ?>" data-es-administrador="<?= $esAdministrador ? "true" : "false" ?>" data-es-tecnico="<?= $esTecnico ? "true" : "false" ?>">

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
                        class="nav-link"
                        href="dashboard.php"
                    >
                        Inicio
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="tickets.php"
                    >
                        Tickets
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="solicitudes.php"
                    >
                        Solicitudes
                    </a>

                </li>

                <?php if ($esDocente || $esAdministrador): ?>
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

                    <button
                        id="cerrarSesionBtn"
                        type="button"
                    >
                        Cerrar Sesión
                    </button>

                </li>

            </ul>

        </div>

    </nav>

</header>

<main>

    <section>

        <h1>
            Gestión de Solicitudes
        </h1>

        <p>
            Consulta y administra las solicitudes registradas.
        </p>

        <?php if ($esDocente): ?>

            <button
                id="btnNuevaSolicitud"
                type="button"
            >
                Nueva Solicitud
            </button>

        <?php endif; ?>

    </section>

    <?php if ($esDocente): ?>

        <div
            id="modalSolicitud"
            class="modal-ticket"
            hidden
        >

            <div class="modal-contenido">

                <button
                    type="button"
                    id="cerrarModal"
                    class="cerrar-modal"
                >
                    ×
                </button>

                <h2>
                    Registrar Solicitud
                </h2>

                <form
                    action="procesarAltaSolicitud.php"
                    method="post"
                    id="solicitudForm"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="crear"
                    >

                    <div>

                        <label for="tipoSolicitud">
                            Tipo de Solicitud
                        </label>

                        <select
                            id="tipoSolicitud"
                            name="tipoSolicitud"
                            required
                        >

                            <option
                                value=""
                                disabled
                                selected
                            >
                                Seleccione una opción
                            </option>

                            <option value="Equipamiento">
                                Equipamiento
                            </option>

                            <option value="Reparación">
                                Reparación
                            </option>

                            <option value="Acceso">
                                Acceso
                            </option>

                            <option value="Licencia">
                                Licencia
                            </option>

                            <option value="Mobiliario">
                                Mobiliario
                            </option>

                            <option value="Otro">
                                Otro
                            </option>

                        </select>

                    </div>

                    <div>

                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            maxlength="500"
                            required
                        ></textarea>

                    </div>

                    <div>

                        <label for="cantidad">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="cantidad"
                            name="cantidad"
                            min="1"
                            value="1"
                            required
                        >

                    </div>

                    <div>

                        <label for="equipo">
                            Equipo o Recurso
                        </label>

                        <input
                            type="text"
                            id="equipo"
                            name="equipo"
                            maxlength="100"
                            placeholder="Ej.: Laptop, Proyector, Adobe..."
                        >

                    </div>

                    <div>

                        <label for="fechaNecesaria">
                            Fecha Necesaria
                        </label>

                        <input
                            type="date"
                            id="fechaNecesaria"
                            name="fechaNecesaria"
                        >

                    </div>

                    <button type="submit">
                        Guardar Solicitud
                    </button>

                </form>

            </div>

        </div>

    <?php endif; ?>

    <?php if ($esDocente): ?>

        <div
            id="modalModificarSolicitud"
            class="modal-ticket"
            hidden
        >

            <div class="modal-contenido">

                <button
                    type="button"
                    id="cerrarModalModificar"
                    class="cerrar-modal"
                >
                    ×
                </button>

                <h2>
                    Modificar Solicitud
                </h2>

                <form
                    action="procesarAltaSolicitud.php"
                    method="post"
                    id="formModificarSolicitud"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="modificar"
                    >

                    <input
                        type="hidden"
                        name="idSolicitud"
                        id="modificarIdSolicitud"
                    >

                    <div>

                        <label for="modificarTipoSolicitud">
                            Tipo de Solicitud
                        </label>

                        <select
                            id="modificarTipoSolicitud"
                            name="tipoSolicitud"
                            required
                        >

                            <option value="Equipamiento">
                                Equipamiento
                            </option>

                            <option value="Reparación">
                                Reparación
                            </option>

                            <option value="Acceso">
                                Acceso
                            </option>

                            <option value="Licencia">
                                Licencia
                            </option>

                            <option value="Mobiliario">
                                Mobiliario
                            </option>

                            <option value="Otro">
                                Otro
                            </option>

                        </select>

                    </div>

                    <div>

                        <label for="modificarDescripcion">
                            Descripción
                        </label>

                        <textarea
                            id="modificarDescripcion"
                            name="descripcion"
                            rows="4"
                            maxlength="500"
                            required
                        ></textarea>

                    </div>

                    <div>

                        <label for="modificarCantidad">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="modificarCantidad"
                            name="cantidad"
                            min="1"
                            required
                        >

                    </div>

                    <div>

                        <label for="modificarEquipo">
                            Equipo o Recurso
                        </label>

                        <input
                            type="text"
                            id="modificarEquipo"
                            name="equipo"
                            maxlength="100"
                        >

                    </div>

                    <div>

                        <label for="modificarFechaNecesaria">
                            Fecha Necesaria
                        </label>

                        <input
                            type="date"
                            id="modificarFechaNecesaria"
                            name="fechaNecesaria"
                        >

                    </div>

                    <button type="submit">
                        Guardar Cambios
                    </button>

                </form>

            </div>

        </div>

    <?php endif; ?>

    <section class="filtros">

        <select id="filtroEstado">

            <option value="">
                Todos los estados
            </option>

            <option value="Pendiente">
                Pendiente
            </option>

            <option value="Aprobada">
                Aprobada
            </option>

            <option value="Rechazada">
                Rechazada
            </option>

            <option value="Cancelada">
                Cancelada
            </option>

        </select>

        <input
            type="text"
            id="filtroUsuario"
            placeholder="Usuario"
        >

        <input
            type="text"
            id="filtroTipo"
            placeholder="Tipo de solicitud"
        >

        <input
            type="text"
            id="filtroEquipo"
            placeholder="Equipo o recurso"
        >

        <input
            type="date"
            id="filtroFecha"
        >

    </section>

    <section>

        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Descripción
                    </th>

                    <th>
                        Cantidad
                    </th>

                    <th>
                        Equipo/Recurso
                    </th>

                    <th>
                        Usuario
                    </th>

                    <th>
                        Fecha Necesaria
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Respuesta
                    </th>

                    <th>
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody id="tablaSolicitudes">

                <tr id="cargandoSolicitudes">
                    <td colspan="11">
                        Cargando solicitudes...
                    </td>
                </tr>

            </tbody>

        </table>

    </section>

    <?php if ($esTecnico): ?>

        <div
            id="modalRespuesta"
            class="modal-ticket"
            hidden
        >

            <div class="modal-contenido">

                <button
                    type="button"
                    id="cerrarModalRespuesta"
                    class="cerrar-modal"
                >
                    ×
                </button>

                <h2 id="tituloRespuesta">
                    Responder Solicitud
                </h2>

                <form
                    action="procesarAltaSolicitud.php"
                    method="post"
                    id="formRespuesta"
                >

                    <input
                        type="hidden"
                        name="accion"
                        id="accionRespuesta"
                    >

                    <input
                        type="hidden"
                        name="idSolicitud"
                        id="idSolicitudRespuesta"
                    >

                    <div>

                        <label for="respuesta">
                            Respuesta
                        </label>

                        <textarea
                            id="respuesta"
                            name="respuesta"
                            rows="5"
                            maxlength="500"
                        ></textarea>

                    </div>

                    <button type="submit">
                        Guardar Respuesta
                    </button>

                </form>

            </div>

        </div>

    <?php endif; ?>

</main>

<footer>

    <p>
        JOJOLOS - Sistema de Gestión de Laboratorios
    </p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/solicitudes.js"></script>

</body>

</html>
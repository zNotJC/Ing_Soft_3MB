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

<?php require __DIR__ . "/partials/navbar.php"; ?>

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

                            <option value="Laboratorio">Laboratorio</option>
                            <option value="Reparación">Reparación</option>
                        </select>
                        <div class="mt-3" id="grupoLaboratorioSolicitud" hidden>
                            <label for="laboratorioSolicitud">Laboratorio</label>
                            <select id="laboratorioSolicitud" class="form-select"><option value="">Seleccione un laboratorio</option></select>
                        </div>
                        <div class="mt-3" id="grupoReparacionSolicitud" hidden>
                            <label for="tipoEquipoSolicitud">Tipo de equipo</label>
                            <select id="tipoEquipoSolicitud" class="form-select"><option value="">Seleccione un tipo</option><option>Ordenadores</option><option>Periféricos</option><option>Televisores</option><option>Aires Acondicionados</option></select>
                            <label for="equipoSolicitud" class="mt-2">Equipo a reparar</label>
                            <select id="equipoSolicitud" class="form-select"><option value="">Seleccione un equipo</option></select>
                        </div>
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

                    <input type="hidden" id="equipo" name="equipo" value="">

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

                        <select id="modificarTipoSolicitud" name="tipoSolicitud" required>
                            <option value="Laboratorio">Laboratorio</option>
                            <option value="Reparación">Reparación</option>
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

                    <div class="mt-3" id="grupoLaboratorioModificarSolicitud" hidden>
                        <label for="laboratorioModificarSolicitud">Laboratorio</label>
                        <select id="laboratorioModificarSolicitud" class="form-select"><option value="">Seleccione un laboratorio</option></select>
                    </div>
                    <div class="mt-3" id="grupoReparacionModificarSolicitud" hidden>
                        <label for="tipoEquipoModificarSolicitud">Tipo de equipo</label>
                        <select id="tipoEquipoModificarSolicitud" class="form-select"><option value="">Seleccione un tipo</option><option>Ordenadores</option><option>Periféricos</option><option>Televisores</option><option>Aires Acondicionados</option></select>
                        <label for="equipoModificarSolicitud" class="mt-2">Equipo</label>
                        <select id="equipoModificarSolicitud" class="form-select"><option value="">Seleccione un equipo</option></select>
                    </div>
                    <input type="hidden" id="modificarEquipo" name="equipo" value="">

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

        <select id="filtroTipo" aria-label="Filtrar por tipo de solicitud">
            <option value="">Todos los tipos</option>
            <option value="Laboratorio">Laboratorio</option>
            <option value="Reparación">Reparación</option>
        </select>

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

<footer class="site-footer"><img class="footer-logo" src="assets/img/jojolos-footer.svg" alt="JOJOLOS"><p>JOJOLOS - Sistema de Gestión de Recursos y Soporte de Informática</p></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/solicitudes.js"></script>

</body>

</html>
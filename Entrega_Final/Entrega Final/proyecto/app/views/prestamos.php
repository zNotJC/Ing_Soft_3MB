<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>JOJOLOS - Gestión de Préstamos</title>

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
        href="assets/css/prestamos.css"
    >

</head>

<body data-es-docente="<?= $esDocente ? "true" : "false" ?>" data-es-administrador="<?= $esAdministrador ? "true" : "false" ?>" data-es-tecnico="<?= $esTecnico ? "true" : "false" ?>">

<?php require __DIR__ . "/partials/navbar.php"; ?>

<main>

    <section>

        <h1>
            Gestión de Préstamos
        </h1>

        <p>
            <?php if ($esDocente): ?>Solicita un préstamo seleccionando el tipo de dispositivo y uno de los equipos disponibles.<?php elseif ($esTecnico): ?>Gestiona los préstamos, sus estados y las devoluciones.<?php else: ?>Consulta los préstamos registrados.<?php endif; ?>
        </p>

        <?php if ($esDocente): ?>

            <button
                id="btnNuevoPrestamo"
                type="button"
            >
                Nuevo Préstamo
            </button>

        <?php endif; ?>

    </section>

    <?php if ($esDocente): ?>

        <div
            id="modalPrestamo"
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
                    Registrar Préstamo
                </h2>

                <form
                    action="procesarAltaPrestamo.php"
                    method="post"
                    id="formPrestamo"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="crear"
                    >

                    <div>

                        <label for="tipoEquipoPrestamo">Tipo de dispositivo</label>
                        <select id="tipoEquipoPrestamo" class="form-select" required>
                            <option value="">Seleccione un tipo</option><option>Ordenadores</option><option>Periféricos</option><option>Televisores</option><option>Aires Acondicionados</option>
                        </select>
                        <label for="idEquipo">Equipo</label>
                        <select
                            id="idEquipo"
                            name="idEquipo"
                            required
                        >
                            <option value="" disabled selected>
                                Seleccione un equipo
                            </option>
                        </select>

                    </div>

                    <div>

                        <label for="fechaPrestamo">
                            Fecha de Préstamo
                        </label>

                        <input
                            type="date"
                            id="fechaPrestamo"
                            name="fechaPrestamo"
                            required
                        >

                    </div>

                    <div>

                        <label for="fechaDevolucion">
                            Fecha de Devolución
                        </label>

                        <input
                            type="date"
                            id="fechaDevolucion"
                            name="fechaDevolucion"
                            required
                        >

                    </div>

                    <button type="submit">
                        Guardar Préstamo
                    </button>

                </form>

            </div>

        </div>

    <?php endif; ?>

    <?php if ($esTecnico): ?>

        <div
            id="modalModificarPrestamo"
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
                    Modificar Préstamo
                </h2>

                <form
                    action="procesarAltaPrestamo.php"
                    method="post"
                    id="formModificarPrestamo"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="modificar"
                    >

                    <input
                        type="hidden"
                        name="idPrestamo"
                        id="modificarIdPrestamo"
                    >

                    <div>

                        <label for="tipoEquipoModificar" class="form-label">Tipo de dispositivo</label>
                        <select id="tipoEquipoModificar" class="form-select" required>
                            <option value="">Seleccione un tipo</option><option>Ordenadores</option><option>Periféricos</option><option>Televisores</option><option>Aires Acondicionados</option>
                        </select>
                        <label for="modificarIdEquipo">Equipo</label>
                        <select
                            name="idEquipo"
                            id="modificarIdEquipo"
                            required
                        >
                            <option value="" disabled>
                                Seleccione un equipo
                            </option>
                        </select>

                    </div>

                    <div>

                        <label for="modificarFechaPrestamo">
                            Fecha de Préstamo
                        </label>

                        <input
                            type="date"
                            name="fechaPrestamo"
                            id="modificarFechaPrestamo"
                            required
                        >

                    </div>

                    <div>

                        <label for="modificarFechaDevolucion">
                            Fecha de Devolución
                        </label>

                        <input
                            type="date"
                            name="fechaDevolucion"
                            id="modificarFechaDevolucion"
                            required
                        >

                    </div>

                    <div>
                        <label for="modificarPrioridad">Prioridad</label>
                        <select class="form-select" name="prioridad" id="modificarPrioridad" required>
                            <option value="Baja">Baja</option>
                            <option value="Media">Media</option>
                            <option value="Alta">Alta</option>
                        </select>
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

        </select>

        <input
            type="text"
            id="filtroNombre"
            placeholder="Nombre"
        >

        <input
            type="text"
            id="filtroEquipo"
            placeholder="Equipo"
        >

        <input
            type="text"
            id="filtroCedula"
            placeholder="Cédula"
        >

        <input
            type="date"
            id="filtroFechaPrestamo"
        >

        <input
            type="date"
            id="filtroFechaDevolucion"
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
                        Equipo
                    </th>

                    <th>
                        Tipo de equipo
                    </th>

                    <th>
                        Nombre
                    </th>

                    <th>
                        Cédula
                    </th>

                    <th>
                        Fecha Préstamo
                    </th>

                    <th>
                        Fecha Devolución
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Prioridad
                    </th>

                    <th>
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody id="tablaPrestamos">

                <tr>
                    <td colspan="10">Cargando...</td>
                </tr>

            </tbody>

        </table>

    </section>

</main>

<footer>

    <p>
        JOJOLOS - Sistema de Gestión de Laboratorios
    </p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/prestamos.js"></script>

</body>

</html>
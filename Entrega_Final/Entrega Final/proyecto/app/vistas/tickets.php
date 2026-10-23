<?php











?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>JOJOLOS - Gestión de Tickets</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/tickets.css">

</head>

<body data-es-docente="<?= $esDocente ? "true" : "false" ?>" data-es-administrador="<?= $esAdministrador ? "true" : "false" ?>" data-es-tecnico="<?= !empty($_SESSION["tecnico"]) ? "true" : "false" ?>">

    <?php require __DIR__ . "/partials/navbar.php"; ?>


    <main>

        <section>

            <h1>Gestión de Tickets</h1>

            <p>
                Registro y seguimiento de incidencias detectadas
                en los laboratorios informáticos.
            </p>

            <?php if ($esDocente): ?>

                <button
                    type="button"
                    id="btnNuevoTicket"
                >
                    Nuevo Ticket
                </button>

            <?php endif; ?>

        </section>


        <?php if ($esDocente): ?>

            <div
                id="modalTicket"
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

                    <h2>Registrar Ticket</h2>

                    <form
                        id="ticketForm"
                        method="POST"
                        action="procesarAltaTicket.php"
                    >

                        <input
                            type="hidden"
                            name="accion"
                            value="crear"
                        >


                        <label for="titulo">
                            Título de la incidencia
                        </label>

                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            required
                            minlength="3"
                            maxlength="150"
                        >


                        <label for="idLaboratorio">
                            Laboratorio / Taller
                        </label>

                        <select
                            id="idLaboratorio"
                            name="idLaboratorio"
                            required
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <option value="">Cargando laboratorios...</option>

                        </select>


                        <label for="idEquipo">
                            Equipo afectado
                        </label>
                        <select id="idEquipo" name="idEquipo" required>
                            <option value="">Seleccione un equipo</option>
                        </select>

                        <label for="descripcion">
                            Descripción de la incidencia
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="5"
                            required
                        ></textarea>


                        <button type="submit">
                            Guardar Ticket
                        </button>

                    </form>

                </div>

            </div>

        <?php endif; ?>


        <section class="filtros">

            <select id="filtroEstado">
                <option value="">Todos los estados</option>
                <option value="En espera">En espera</option>
                <option value="Pendiente">Pendiente</option>
                <option value="En Proceso">En Proceso</option>
                <option value="Vencido">Vencido</option>
                <option value="Resuelto">Resuelto</option>
                <option value="Cancelado">Cancelado</option>
                <option value="Denegado">Denegado</option>
            </select>


            <select id="filtroLaboratorio"><option value="">Todos los laboratorios</option></select>


            <input
                type="text"
                id="filtroEquipo"
                placeholder="Título de la incidencia"
            >

        </section>


        <section>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Laboratorio/Taller</th>
                        <th>Título</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>


                <tbody id="tablaTickets">

                    <tr id="cargandoTickets">
                        <td colspan="7">
                            Cargando tickets...
                        </td>
                    </tr>

                </tbody>

            </table>

        </section>
        <dialog id="dialogRevisionTicket">
            <h2>Revisar ticket</h2>
            <p><strong>Título:</strong> <span id="revisionTitulo"></span></p>
            <p><strong>Descripción:</strong> <span id="revisionDescripcion"></span></p>
            <p><strong>Laboratorio:</strong> <span id="revisionLaboratorio"></span></p>
            <label for="revisionPrioridad">Prioridad</label>
            <select id="revisionPrioridad" required>
                <option value="">Seleccione</option>
                <option value="Baja">Baja</option><option value="Media">Media</option><option value="Alta">Alta</option>
            </select>
            <label for="revisionFechaLimite">Fecha límite</label>
            <input type="date" id="revisionFechaLimite" required>
            <button type="button" id="btnIniciarTicket">Iniciar</button>
            <button type="button" id="btnEsperarTicket">Esperar</button>
            <button type="button" id="cerrarRevisionTicket">Cerrar</button>
        </dialog>

        <dialog id="dialogTicket">

            <h2>Detalle del Ticket</h2>

            <p>
                <strong>ID:</strong>
                <span id="detalleId"></span>
            </p>

            <p>
                <strong>Título:</strong>
                <span id="detalleTitulo"></span>
            </p>

            <p>
                <strong>Descripción:</strong>
                <span id="detalleDescripcion"></span>
            </p>

            <p>
                <strong>Laboratorio/Taller:</strong>
                <span id="detalleLaboratorio"></span>
            </p>

            <p>
                <strong>Solicitante:</strong>
                <span id="detalleSolicitante"></span>
            </p>

            <p>
                <strong>Prioridad:</strong>
                <span id="detallePrioridad"></span>
            </p>

            <p>
                <strong>Estado:</strong>
                <span id="detalleEstado"></span>
            </p>

            <p>
                <strong>Fecha de registro:</strong>
                <span id="detalleFechaRegistro"></span>
            </p>

            <p>
                <strong>Fecha límite:</strong>
                <span id="detalleFechaLimite"></span>
            </p>

            <button
                type="button"
                id="cerrarDetalleTicket"
            >
                Cerrar
            </button>

        </dialog>

    </main>


    <footer>

        <p>
            JOJOLOS - Sistema de Gestión de Laboratorios
        </p>

    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/tickets.js"></script>

</body>

</html>
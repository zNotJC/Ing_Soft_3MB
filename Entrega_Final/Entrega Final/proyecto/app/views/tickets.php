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

    <header>

        <nav class="navbar navbar-expand-lg">

            <a class="navbar-brand" href="dashboard.php">
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
                            class="nav-link active"
                            href="tickets.php"
                        >
                            Tickets
                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="solicitudes.php"
                        >
                            Solicitudes
                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="prestamos.php"
                        >
                            Préstamos
                        </a>

                    </li>

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
                            href="/proyecto/app/controllers/cerrarSesion.php"
                            class="btnCerrarSesion"
                        >
                            Cerrar Sesión
                        </a>

                    </li>

                </ul>

            </div>

        </nav>

    </header>


    <main>

        <section>

            <h1>Gestión de Tickets</h1>

            <p>
                Registro y seguimiento de incidencias detectadas
                en los laboratorios informáticos.
            </p>

            <?php if ($esDocente || !empty($_SESSION["tecnico"])): ?>

                <button
                    type="button"
                    id="btnNuevoTicket"
                >
                    Nuevo Ticket
                </button>

            <?php endif; ?>

        </section>


        <?php if ($esDocente || !empty($_SESSION["tecnico"])): ?>

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


                        <label for="idLugar">
                            Laboratorio / Taller
                        </label>

                        <select
                            id="idLugar"
                            name="idLugar"
                            required
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <?php foreach ($lugares as $lugar): ?>

                                <option
                                    value="<?= htmlspecialchars($lugar["idLugar"]) ?>"
                                >
                                    <?= htmlspecialchars($lugar["nombre"]) ?>
                                </option>

                            <?php endforeach; ?>

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


                        <label for="prioridad">
                            Prioridad
                        </label>

                        <select
                            id="prioridad"
                            name="idPrioridad"
                            required
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <?php foreach ($prioridades as $prioridad): ?>

                                <option
                                    value="<?= htmlspecialchars($prioridad["idPrioridad"]) ?>"
                                >
                                    <?= htmlspecialchars($prioridad["nombre"]) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>


                        <label for="fechaLimite">
                            Fecha límite
                        </label>

                        <input
                            type="date"
                            id="fechaLimite"
                            name="fechaLimite"
                            min="<?= date("Y-m-d") ?>"
                            required
                        >


                        <button type="submit">
                            Guardar Ticket
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

                <option value="En Proceso">
                    En Proceso
                </option>

                <option value="Vencido">
                    Vencido
                </option>

                <option value="Resuelto">
                    Resuelto
                </option>

                <option value="Cancelado">
                    Cancelado
                </option>

            </select>


            <input
                type="text"
                id="filtroLaboratorio"
                placeholder="Laboratorio o taller"
            >


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
                <span id="detalleLugar"></span>
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
const btnNuevaSolicitud =
    document.getElementById("btnNuevaSolicitud");

const modalSolicitud =
    document.getElementById("modalSolicitud");

const cerrarModal =
    document.getElementById("cerrarModal");

const formulario =
    document.getElementById("solicitudForm");


if (btnNuevaSolicitud) {

    btnNuevaSolicitud.addEventListener(
        "click",
        function () {

            if (formulario) {
                formulario.reset();
            }

            modalSolicitud.hidden = false;

        }
    );

}


if (cerrarModal && modalSolicitud) {

    cerrarModal.addEventListener(
        "click",
        function () {

            modalSolicitud.hidden = true;

        }
    );

}


const modalModificar =
    document.getElementById(
        "modalModificarSolicitud"
    );

const cerrarModalModificar =
    document.getElementById(
        "cerrarModalModificar"
    );


if (cerrarModalModificar && modalModificar) {

    cerrarModalModificar.addEventListener(
        "click",
        function () {

            modalModificar.hidden = true;

        }
    );

}


function abrirModalModificarSolicitud(
    idSolicitud,
    tipoSolicitud,
    descripcion,
    cantidad,
    equipo,
    fechaNecesaria
) {

    document.getElementById(
        "modificarIdSolicitud"
    ).value = idSolicitud;

    document.getElementById(
        "modificarTipoSolicitud"
    ).value = tipoSolicitud;

    document.getElementById(
        "modificarDescripcion"
    ).value = descripcion;

    document.getElementById(
        "modificarCantidad"
    ).value = cantidad;

    document.getElementById(
        "modificarEquipo"
    ).value = equipo || "";

    document.getElementById(
        "modificarFechaNecesaria"
    ).value = fechaNecesaria || "";

    modalModificar.hidden = false;

}


const modalRespuesta =
    document.getElementById(
        "modalRespuesta"
    );

const cerrarModalRespuesta =
    document.getElementById(
        "cerrarModalRespuesta"
    );


if (cerrarModalRespuesta && modalRespuesta) {

    cerrarModalRespuesta.addEventListener(
        "click",
        function () {

            modalRespuesta.hidden = true;

        }
    );

}


function abrirModalRespuesta(
    idSolicitud,
    accion
) {

    document.getElementById(
        "idSolicitudRespuesta"
    ).value = idSolicitud;

    document.getElementById(
        "accionRespuesta"
    ).value = accion;

    const titulo =
        document.getElementById(
            "tituloRespuesta"
        );

    const respuesta =
        document.getElementById(
            "respuesta"
        );

    if (accion === "aprobar") {

        titulo.textContent =
            "Aprobar Solicitud";

        respuesta.placeholder =
            "Observación para el solicitante (opcional).";

        respuesta.required = false;

    } else {

        titulo.textContent =
            "Rechazar Solicitud";

        respuesta.placeholder =
            "Indique el motivo del rechazo.";

        respuesta.required = true;

    }

    respuesta.value = "";

    modalRespuesta.hidden = false;

}


const formularioSolicitud =
    document.getElementById(
        "solicitudForm"
    );


if (formularioSolicitud) {
    formularioSolicitud.addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const fechaNecesaria = document.getElementById("fechaNecesaria").value;
        if (fechaNecesaria && fechaNecesaria < obtenerFechaActual()) {
            window.alert("La fecha necesaria no puede ser anterior a la fecha actual.");
            return;
        }
        try {
            const datos = Object.fromEntries(new FormData(formularioSolicitud).entries());
            await enviarSolicitud("POST", datos);
            formularioSolicitud.reset();
            modalSolicitud.hidden = true;
        } catch (error) { window.alert(error.message); }
    });
}

if (formularioModificar) {
    formularioModificar.addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const fechaNecesaria = document.getElementById("modificarFechaNecesaria").value;
        if (fechaNecesaria && fechaNecesaria < obtenerFechaActual()) {
            window.alert("La fecha necesaria no puede ser anterior a la fecha actual.");
            return;
        }
        try {
            const datos = Object.fromEntries(new FormData(formularioModificar).entries());
            datos.idSolicitud = Number(datos.idSolicitud);
            await enviarSolicitud("PUT", datos);
            modalModificar.hidden = true;
        } catch (error) { window.alert(error.message); }
    });
}

const formularioRespuesta = document.getElementById("formRespuesta");
if (formularioRespuesta) {
    formularioRespuesta.addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const idSolicitud = Number(document.getElementById("idSolicitudRespuesta").value);
        const accion = document.getElementById("accionRespuesta").value;
        const estado = accion === "aprobar" ? "Aprobada" : "Rechazada";
        const respuesta = document.getElementById("respuesta").value.trim();
        if (estado === "Rechazada" && !respuesta) {
            window.alert("Indica el motivo del rechazo.");
            return;
        }
        try {
            await enviarSolicitud("PUT", { idSolicitud, estado, respuesta });
            modalRespuesta.hidden = true;
        } catch (error) { window.alert(error.message); }
    });
}

async function enviarSolicitud(metodo, datos) {
    const respuesta = await fetch("api/solicitudes.php", {
        method: metodo,
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(datos)
    });
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo completar la operación.");
    await cargarSolicitudes();
}

function obtenerFechaActual() {

    const fecha =
        new Date();

    const año =
        fecha.getFullYear();

    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(2, "0");

    const dia =
        String(
            fecha.getDate()
        ).padStart(2, "0");

    return (
        año +
        "-" +
        mes +
        "-" +
        dia
    );

}


const filtroEstado =
    document.getElementById(
        "filtroEstado"
    );

const filtroUsuario =
    document.getElementById(
        "filtroUsuario"
    );

const filtroTipo =
    document.getElementById(
        "filtroTipo"
    );

const filtroEquipo =
    document.getElementById(
        "filtroEquipo"
    );

const filtroFecha =
    document.getElementById(
        "filtroFecha"
    );

const tabla =
    document.getElementById(
        "tablaSolicitudes"
    );

const solicitudesPorId =
    new Map();


function crearCeldaSolicitud(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}

function crearBotonSolicitud(texto, clase, accion, id, tipoRespuesta = "") {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.textContent = texto;
    boton.className = clase;
    boton.dataset.accionSolicitud = accion;
    boton.dataset.idSolicitud = String(id);
    if (tipoRespuesta) boton.dataset.tipoRespuesta = tipoRespuesta;
    return boton;
}

function construirFilaSolicitud(solicitud) {
    solicitudesPorId.set(String(solicitud.idSolicitud), solicitud);
    const fila = document.createElement("tr");
    [solicitud.idSolicitud, solicitud.fechaSolicitud, solicitud.tipoSolicitud, solicitud.descripcion, solicitud.cantidad, solicitud.equipo, solicitud.nombre, solicitud.fechaNecesaria, solicitud.estado, solicitud.respuesta].forEach(valor => fila.appendChild(crearCeldaSolicitud(valor)));
    const acciones = document.createElement("td");
    acciones.className = "acciones";
    const esDocente = document.body.dataset.esDocente === "true";
    const esTecnico = document.body.dataset.esTecnico === "true";
    if (esDocente && solicitud.estado === "Pendiente") {
        acciones.appendChild(crearBotonSolicitud("Modificar", "btn-modificar", "modificar", solicitud.idSolicitud));
        acciones.appendChild(crearBotonSolicitud("Cancelar", "btn-cancelar", "cancelar", solicitud.idSolicitud));
    }
    if (esTecnico && solicitud.estado === "Pendiente") {
        acciones.appendChild(crearBotonSolicitud("Aprobar", "btn-aprobar", "respuesta", solicitud.idSolicitud, "aprobar"));
        acciones.appendChild(crearBotonSolicitud("Rechazar", "btn-rechazar", "respuesta", solicitud.idSolicitud, "rechazar"));
    }
    fila.appendChild(acciones);
    return fila;
}

function aplicarFiltros() {

    const estado =
        filtroEstado
            ? filtroEstado.value.toLowerCase()
            : "";

    const usuario =
        filtroUsuario
            ? filtroUsuario.value.toLowerCase()
            : "";

    const tipo =
        filtroTipo
            ? filtroTipo.value.toLowerCase()
            : "";

    const equipo =
        filtroEquipo
            ? filtroEquipo.value.toLowerCase()
            : "";

    const fecha =
        filtroFecha
            ? filtroFecha.value
            : "";


    const filas =
        tabla.querySelectorAll("tr");


    for (
        let i = 0;
        i < filas.length;
        i++
    ) {

        const fila =
            filas[i];


        if (
            fila.children.length < 10
        ) {

            continue;

        }


        const textoFecha =
            fila.children[1]
                .textContent
                .trim();

        const textoTipo =
            fila.children[2]
                .textContent
                .toLowerCase();

        const textoEquipo =
            fila.children[5]
                .textContent
                .toLowerCase();

        const textoUsuario =
            fila.children[6]
                .textContent
                .toLowerCase();

        const textoEstado =
            fila.children[8]
                .textContent
                .toLowerCase();


        const coincideEstado =
            estado === "" ||
            textoEstado === estado;

        const coincideUsuario =
            usuario === "" ||
            textoUsuario.includes(usuario);

        const coincideTipo =
            tipo === "" ||
            textoTipo.includes(tipo);

        const coincideEquipo =
            equipo === "" ||
            textoEquipo.includes(equipo);

        const coincideFecha =
            fecha === "" ||
            textoFecha === fecha;


        fila.style.display =
            coincideEstado &&
            coincideUsuario &&
            coincideTipo &&
            coincideEquipo &&
            coincideFecha
                ? ""
                : "none";

    }

}


if (tabla) {

    tabla.addEventListener(
        "click",
        function (evento) {

            const botonModificar =
                evento.target.closest(
                    '[data-accion-solicitud="modificar"]'
                );

            if (botonModificar) {

                const solicitud =
                    solicitudesPorId.get(
                        botonModificar.dataset.idSolicitud
                    );

                if (solicitud) {

                    abrirModalModificarSolicitud(
                        solicitud.idSolicitud,
                        solicitud.tipoSolicitud,
                        solicitud.descripcion,
                        solicitud.cantidad,
                        solicitud.equipo,
                        solicitud.fechaNecesaria
                    );

                }

                return;

            }


            const botonRespuesta = evento.target.closest('[data-accion-solicitud="respuesta"]');
            if (botonRespuesta) {
                abrirModalRespuesta(botonRespuesta.dataset.idSolicitud, botonRespuesta.dataset.tipoRespuesta);
                return;
            }

            const botonCancelar = evento.target.closest('[data-accion-solicitud="cancelar"]');
            if (botonCancelar) {
                if (!window.confirm("¿Deseas cancelar esta solicitud?")) return;
                enviarSolicitud("DELETE", { idSolicitud: Number(botonCancelar.dataset.idSolicitud) }).catch(error => window.alert(error.message));
            }

        }
    );

}


if (filtroEstado) {

    filtroEstado.addEventListener(
        "change",
        aplicarFiltros
    );

}


if (filtroUsuario) {

    filtroUsuario.addEventListener(
        "input",
        aplicarFiltros
    );

}


if (filtroTipo) {

    filtroTipo.addEventListener(
        "input",
        aplicarFiltros
    );

}


if (filtroEquipo) {

    filtroEquipo.addEventListener(
        "input",
        aplicarFiltros
    );

}


if (filtroFecha) {

    filtroFecha.addEventListener(
        "change",
        aplicarFiltros
    );

}


const cerrarSesionBtn =
    document.getElementById(
        "cerrarSesionBtn"
    );


if (cerrarSesionBtn) {

    cerrarSesionBtn.addEventListener(
        "click",
        function () {

            window.location.href =
                "cerrarSesion.php";

        }
    );

}


async function cargarSolicitudes() {
    try {
        const respuesta = await fetch("api/solicitudes.php", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        const solicitudes = resultado.data;
        if (!respuesta.ok || resultado.status !== "success" || !Array.isArray(solicitudes)) throw new Error(resultado.message || "No se pudo cargar la lista de solicitudes.");
        if (solicitudes.length === 0) {
            const fila = document.createElement("tr");
            const celda = crearCeldaSolicitud("No hay solicitudes registradas.");
            celda.colSpan = 11;
            fila.appendChild(celda);
            tabla.replaceChildren(fila);
            return;
        }
        const fragmento = document.createDocumentFragment();
        solicitudes.forEach(solicitud => fragmento.appendChild(construirFilaSolicitud(solicitud)));
        tabla.replaceChildren(fragmento);
        aplicarFiltros();
    } catch (error) {
        console.error(error);
        const fila = document.createElement("tr");
        const celda = crearCeldaSolicitud(error.message || "No se pudieron cargar las solicitudes.");
        celda.colSpan = 11;
        fila.appendChild(celda);
        tabla.replaceChildren(fila);
    }
}

cargarSolicitudes();

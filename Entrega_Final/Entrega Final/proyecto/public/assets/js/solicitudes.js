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

            if (typeof actualizarTipoSolicitud === "function") actualizarTipoSolicitud();

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

    document.getElementById("modificarEquipo").value = equipo || "";
    if (tipoSolicitudModificar) {
        tipoSolicitudModificar.value = tipoSolicitud;
        actualizarTipoModificar();
        if (tipoSolicitud === 'Laboratorio' && selectorLabModificar) selectorLabModificar.value = equipo || '';
        if (tipoSolicitud === 'Reparación' && selectorEquipoModificar) {
            const encontrado = (inventarioSolicitudes.equipos || []).find(x => x.nombre === equipo);
            if (encontrado) { if (tipoEquipoModificar) tipoEquipoModificar.value = encontrado.tipo; actualizarEquiposSolicitud(); selectorEquipoModificar.value = encontrado.nombre; }
        }
    }

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


const formularioModificar = document.getElementById("formModificarSolicitud");

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
            datos.equipo = tipoSolicitudSelector?.value === 'Laboratorio' ? (selectorLaboratorioSolicitud?.value || '') : (selectorEquipoSolicitud?.value || '');
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
            const tipoMod = document.getElementById('modificarTipoSolicitud')?.value;
            datos.equipo = tipoMod === 'Laboratorio' ? (document.getElementById('laboratorioModificarSolicitud')?.value || '') : (document.getElementById('equipoModificarSolicitud')?.value || '');
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
    const respuesta = await fetch("../index.php?api=solicitudes", {
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
    fila.dataset.estadoOriginal = solicitud.estado || "";
    fila.dataset.tipoOriginal = solicitud.tipoSolicitud || "";
    fila.dataset.fechaOriginal = String(solicitud.fechaSolicitud || '').slice(0, 10);
    fila.dataset.usuarioOriginal = solicitud.nombre || "";
    fila.dataset.equipoOriginal = solicitud.equipo || "";
    [solicitud.idSolicitud, solicitud.fechaSolicitud, solicitud.tipoSolicitud, solicitud.descripcion, solicitud.equipo, solicitud.nombre, solicitud.fechaNecesaria, solicitud.estado, solicitud.respuesta].forEach(valor => fila.appendChild(crearCeldaSolicitud(valor)));
    const acciones = document.createElement("td");
    acciones.className = "acciones";
    const esDocente = document.body.dataset.esDocente === "true";
    const esTecnico = document.body.dataset.esTecnico === "true";
    if (esDocente && ["Pendiente", "En espera"].includes(solicitud.estado)) {
        acciones.appendChild(crearBotonSolicitud("Modificar", "btn-modificar", "modificar", solicitud.idSolicitud));
        acciones.appendChild(crearBotonSolicitud("Cancelar", "btn-cancelar", "cancelar", solicitud.idSolicitud));
    }
    if (esTecnico && ["Pendiente", "En espera"].includes(solicitud.estado)) {
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


        const textoFecha = fila.dataset.fechaOriginal || '';
        const textoTipo = (fila.dataset.tipoOriginal || '').toLowerCase();
        const textoEquipo = (fila.dataset.equipoOriginal || '').toLowerCase();
        const textoUsuario = (fila.dataset.usuarioOriginal || '').toLowerCase();
        const textoEstado = (fila.dataset.estadoOriginal || '').toLowerCase();


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
    filtroTipo.addEventListener("change", aplicarFiltros);
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
                "../index.php?accion=logout";

        }
    );

}


async function cargarSolicitudes() {
    if (!tabla) return;
    try {
        const respuesta = await fetch("../index.php?api=solicitudes", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        const solicitudes = resultado.data;
        if (!respuesta.ok || resultado.status !== "success" || !Array.isArray(solicitudes)) throw new Error(resultado.message || `No se pudo cargar la lista de solicitudes (HTTP ${respuesta.status}).`);
        if (solicitudes.length === 0) {
            const fila = document.createElement("tr");
            const celda = crearCeldaSolicitud("No hay solicitudes registradas.");
            celda.colSpan = 10;
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
        celda.colSpan = 10;
        fila.appendChild(celda);
        tabla.replaceChildren(fila);
    }
}

cargarSolicitudes();

const tipoSolicitudSelector = document.getElementById('tipoSolicitud');
const grupoLaboratorio = document.getElementById('grupoLaboratorioSolicitud');
const grupoReparacion = document.getElementById('grupoReparacionSolicitud');
const selectorLaboratorioSolicitud = document.getElementById('laboratorioSolicitud');
const selectorTipoEquipoSolicitud = document.getElementById('tipoEquipoSolicitud');
const selectorEquipoSolicitud = document.getElementById('equipoSolicitud');
const tipoSolicitudModificar = document.getElementById('modificarTipoSolicitud');
const grupoLabModificar = document.getElementById('grupoLaboratorioModificarSolicitud');
const grupoRepModificar = document.getElementById('grupoReparacionModificarSolicitud');
const selectorLabModificar = document.getElementById('laboratorioModificarSolicitud');
const tipoEquipoModificar = document.getElementById('tipoEquipoModificarSolicitud');
const selectorEquipoModificar = document.getElementById('equipoModificarSolicitud');
let inventarioSolicitudes = {laboratorios: [], equipos: []};
async function cargarOpcionesSolicitud() {
    try {
        const r = await fetch('../index.php?api=equipos', {headers:{Accept:'application/json'}, cache:'no-store'});
        const j = await r.json();
        if (!r.ok || j.status !== 'success') throw new Error(j.message || 'No se pudo cargar el inventario.');
        inventarioSolicitudes = j.data;
        if (selectorLaboratorioSolicitud) { selectorLaboratorioSolicitud.replaceChildren(new Option('Seleccione un laboratorio','')); (j.data.laboratoriosDisponibles || []).forEach(l => selectorLaboratorioSolicitud.add(new Option(l.nombre,l.nombre))); }
        if (selectorLabModificar) { selectorLabModificar.replaceChildren(new Option('Seleccione un laboratorio','')); (j.data.laboratoriosDisponibles || []).forEach(l => selectorLabModificar.add(new Option(l.nombre,l.nombre))); }
        actualizarTipoSolicitud();
    } catch (e) { console.error(e); }
}
function actualizarTipoSolicitud() {
    const tipo = tipoSolicitudSelector?.value || '';
    if (grupoLaboratorio) grupoLaboratorio.hidden = tipo !== 'Laboratorio';
    if (grupoReparacion) grupoReparacion.hidden = tipo !== 'Reparación';
    if (selectorLaboratorioSolicitud) selectorLaboratorioSolicitud.required = tipo === 'Laboratorio';
    if (selectorTipoEquipoSolicitud) selectorTipoEquipoSolicitud.required = false;
    if (selectorEquipoSolicitud) selectorEquipoSolicitud.required = tipo === 'Reparación';
    const fecha = document.getElementById('fechaNecesaria');
    if (fecha) { fecha.required = tipo === 'Laboratorio'; if (tipo !== 'Laboratorio') fecha.value = ''; }
    const grupoFecha = document.getElementById('grupoFechaNecesaria');
    if (grupoFecha) grupoFecha.hidden = tipo !== 'Laboratorio';
    const etiquetaFecha = document.getElementById('etiquetaFechaNecesaria');
    if (etiquetaFecha) etiquetaFecha.textContent = tipo === 'Laboratorio' ? 'Día solicitado para el laboratorio' : 'Día solicitado (opcional)';
    actualizarEquiposSolicitud();
}
function actualizarEquiposSolicitud() {
    const poblar=(select,tipo)=>{if(!select)return;select.replaceChildren(new Option('Seleccione un equipo',''));(inventarioSolicitudes.equipos||[]).filter(e=>(!tipo||e.tipo===tipo)&&e.estado==='Disponible').forEach(e=>select.add(new Option(`${e.nombre} (${e.tipo})`,e.nombre)));};
    poblar(selectorEquipoSolicitud,selectorTipoEquipoSolicitud?.value||'');
    poblar(selectorEquipoModificar,tipoEquipoModificar?.value||'');
}
function actualizarTipoModificar(){const tipo=tipoSolicitudModificar?.value||'';if(grupoLabModificar)grupoLabModificar.hidden=tipo!=='Laboratorio';if(grupoRepModificar)grupoRepModificar.hidden=tipo!=='Reparación';if(selectorLabModificar)selectorLabModificar.required=tipo==='Laboratorio';if(tipoEquipoModificar)tipoEquipoModificar.required=false;if(selectorEquipoModificar)selectorEquipoModificar.required=tipo==='Reparación';const fecha=document.getElementById('modificarFechaNecesaria');if(fecha){fecha.required=tipo==='Laboratorio';if(tipo!=='Laboratorio')fecha.value='';}const grupoFecha=document.getElementById('grupoModificarFechaNecesaria');if(grupoFecha)grupoFecha.hidden=tipo!=='Laboratorio';const etiqueta=document.getElementById('etiquetaModificarFechaNecesaria');if(etiqueta)etiqueta.textContent='Día solicitado para el laboratorio';actualizarEquiposSolicitud();}
tipoSolicitudSelector?.addEventListener('change', actualizarTipoSolicitud);
selectorTipoEquipoSolicitud?.addEventListener('change', actualizarEquiposSolicitud);
tipoSolicitudModificar?.addEventListener('change', actualizarTipoModificar);
tipoEquipoModificar?.addEventListener('change', actualizarEquiposSolicitud);
formulario?.addEventListener('submit', function () {
    const destino = tipoSolicitudSelector?.value === 'Laboratorio' ? selectorLaboratorioSolicitud?.value : selectorEquipoSolicitud?.value;
    let campoEquipo = formulario.querySelector('[name="equipo"]');
    if (!campoEquipo) { campoEquipo = document.createElement('input'); campoEquipo.type='hidden'; campoEquipo.name='equipo'; formulario.appendChild(campoEquipo); }
    campoEquipo.value = destino || '';
});
cargarOpcionesSolicitud();

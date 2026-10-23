const tablaEquipos = document.getElementById("tablaEquipos");
const formularioEquipo = document.getElementById("formEquipo");
const selectorLugar = document.getElementById("idLugar");
const mensajeInventario = document.getElementById("mensajeInventario");
const esAdministrador = document.body.dataset.esAdministrador === "true";
let equipos = new Map();

function celda(texto) {
    const elemento = document.createElement("td");
    elemento.textContent = texto ?? "";
    return elemento;
}

function mostrarMensajeTabla(texto) {
    const fila = document.createElement("tr");
    const elemento = celda(texto);
    elemento.colSpan = esAdministrador ? 5 : 4;
    fila.appendChild(elemento);
    tablaEquipos.replaceChildren(fila);
}

function cargarLugares(lugares) {
    if (!selectorLugar) return;
    const inicial = document.createElement("option");
    inicial.value = "";
    inicial.textContent = "Seleccione un lugar";
    selectorLugar.replaceChildren(inicial);
    lugares.forEach(lugar => {
        const opcion = document.createElement("option");
        opcion.value = lugar.idLugar;
        opcion.textContent = lugar.nombre;
        selectorLugar.appendChild(opcion);
    });
}

function renderizarEquipos(lista) {
    equipos = new Map(lista.map(equipo => [String(equipo.idEquipo), equipo]));
    if (!lista.length) {
        mostrarMensajeTabla("No hay equipos registrados.");
        return;
    }
    const fragmento = document.createDocumentFragment();
    lista.forEach(equipo => {
        const fila = document.createElement("tr");
        [equipo.idEquipo, equipo.nombre, equipo.numeroSerie, equipo.lugar].forEach(valor => fila.appendChild(celda(valor)));
        if (esAdministrador) {
            const acciones = document.createElement("td");
            const editar = document.createElement("button");
            editar.type = "button";
            editar.className = "btn btn-sm btn-primary me-2";
            editar.textContent = "Modificar";
            editar.dataset.accionEquipo = "editar";
            editar.dataset.idEquipo = equipo.idEquipo;
            const eliminar = document.createElement("button");
            eliminar.type = "button";
            eliminar.className = "btn btn-sm btn-danger";
            eliminar.textContent = "Eliminar";
            eliminar.dataset.accionEquipo = "eliminar";
            eliminar.dataset.idEquipo = equipo.idEquipo;
            acciones.append(editar, eliminar);
            fila.appendChild(acciones);
        }
        fragmento.appendChild(fila);
    });
    tablaEquipos.replaceChildren(fragmento);
}

async function solicitarApi(metodo, datos = null) {
    const opciones = { method: metodo, headers: { Accept: "application/json" } };
    if (datos !== null) {
        opciones.headers["Content-Type"] = "application/json";
        opciones.body = JSON.stringify(datos);
    }
    const respuesta = await fetch("api/equipos.php", opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "La operación falló.");
    return resultado.data;
}

async function cargarInventario() {
    try {
        const datos = await solicitarApi("GET");
        cargarLugares(datos.lugares);
        renderizarEquipos(datos.equipos);
    } catch (error) {
        mostrarMensajeTabla(error.message);
    }
}

formularioEquipo?.addEventListener("submit", async evento => {
    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(formularioEquipo).entries());
    const id = datos.idEquipo;
    datos.idLugar = Number(datos.idLugar);
    delete datos.idEquipo;
    try {
        await solicitarApi(id ? "PUT" : "POST", id ? { ...datos, idEquipo: Number(id) } : datos);
        mensajeInventario.textContent = id ? "Equipo actualizado." : "Equipo registrado.";
        formularioEquipo.reset();
        document.getElementById("idEquipo").value = "";
        document.getElementById("guardarEquipo").textContent = "Registrar equipo";
        document.getElementById("cancelarEdicion").hidden = true;
        await cargarInventario();
    } catch (error) { mensajeInventario.textContent = error.message; }
});

tablaEquipos.addEventListener("click", async evento => {
    const boton = evento.target.closest("button[data-accion-equipo]");
    if (!boton || !esAdministrador) return;
    const equipo = equipos.get(String(boton.dataset.idEquipo));
    if (!equipo) return;
    if (boton.dataset.accionEquipo === "editar") {
        document.getElementById("idEquipo").value = equipo.idEquipo;
        document.getElementById("nombre").value = equipo.nombre;
        document.getElementById("numeroSerie").value = equipo.numeroSerie;
        selectorLugar.value = equipo.idLugar;
        document.getElementById("guardarEquipo").textContent = "Guardar cambios";
        document.getElementById("cancelarEdicion").hidden = false;
        return;
    }
    if (!window.confirm(`¿Eliminar el equipo ${equipo.nombre}?`)) return;
    try {
        await solicitarApi("DELETE", { idEquipo: Number(equipo.idEquipo) });
        mensajeInventario.textContent = "Equipo eliminado.";
        await cargarInventario();
    } catch (error) { mensajeInventario.textContent = error.message; }
});

document.getElementById("cancelarEdicion")?.addEventListener("click", () => {
    formularioEquipo.reset();
    document.getElementById("idEquipo").value = "";
    document.getElementById("guardarEquipo").textContent = "Registrar equipo";
    document.getElementById("cancelarEdicion").hidden = true;
});

cargarInventario();

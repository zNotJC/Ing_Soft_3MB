const modal = document.getElementById("modalPrestamo");
const btnNuevoPrestamo = document.getElementById("btnNuevoPrestamo");
const cerrarModal = document.getElementById("cerrarModal");
const formulario = document.getElementById("formPrestamo");
const modalModificar = document.getElementById("modalModificarPrestamo");
const cerrarModalModificar = document.getElementById("cerrarModalModificar");
const formularioModificar = modalModificar ? modalModificar.querySelector("form") : null;
const filtroEstado = document.getElementById("filtroEstado");
const filtroNombre = document.getElementById("filtroNombre");
const filtroEquipo = document.getElementById("filtroEquipo");
const filtroCedula = document.getElementById("filtroCedula");
const filtroFechaPrestamo = document.getElementById("filtroFechaPrestamo");
const filtroFechaDevolucion = document.getElementById("filtroFechaDevolucion");
const tabla = document.getElementById("tablaPrestamos");
const esAdministrador = document.body.dataset.esAdministrador === "true";
const prestamosPorId = new Map();

function crearCelda(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}

function crearBoton(texto, clase, accion, id) {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.textContent = texto;
    boton.className = clase;
    boton.dataset.accionPrestamo = accion;
    boton.dataset.idPrestamo = String(id);
    return boton;
}

function mostrarMensaje(mensaje) {
    const fila = document.createElement("tr");
    const celda = crearCelda(mensaje);
    celda.colSpan = 9;
    fila.appendChild(celda);
    tabla.replaceChildren(fila);
}

function renderizarEquipos(equipos) {
    [document.getElementById("idEquipo"), document.getElementById("modificarIdEquipo")].forEach(select => {
        if (!select) return;
        const valorActual = select.value;
        const opcionInicial = document.createElement("option");
        opcionInicial.value = "";
        opcionInicial.textContent = "Seleccione un equipo";
        opcionInicial.disabled = true;
        select.replaceChildren(opcionInicial);
        equipos.forEach(equipo => {
            const opcion = document.createElement("option");
            opcion.value = equipo.idEquipo;
            opcion.textContent = `${equipo.nombre} - ${equipo.numeroSerie}`;
            select.appendChild(opcion);
        });
        if (valorActual) select.value = valorActual;
    });
}

function renderizarEstados(estados) {
    if (!filtroEstado) return;
    const inicial = document.createElement("option");
    inicial.value = "";
    inicial.textContent = "Todos los estados";
    filtroEstado.replaceChildren(inicial);
    estados.forEach(estado => {
        const opcion = document.createElement("option");
        opcion.value = estado.nombre;
        opcion.textContent = estado.nombre;
        filtroEstado.appendChild(opcion);
    });
}

function construirFilaPrestamo(prestamo) {
    prestamosPorId.set(String(prestamo.idPrestamo), prestamo);
    const fila = document.createElement("tr");
    [prestamo.idPrestamo, prestamo.equipo, prestamo.numeroSerie, prestamo.nombre, prestamo.cedula, prestamo.fechaPrestamo, prestamo.fechaDevolucion, prestamo.estado].forEach(valor => fila.appendChild(crearCelda(valor)));
    const acciones = document.createElement("td");
    acciones.className = "acciones";
    if (esAdministrador) {
        if (prestamo.estado === "Pendiente") acciones.appendChild(crearBoton("Aceptar", "btn-aceptar", "aceptar", prestamo.idPrestamo));
        if (["Pendiente", "Activo"].includes(prestamo.estado)) {
            acciones.appendChild(crearBoton("Modificar", "btn-modificar", "modificar", prestamo.idPrestamo));
            acciones.appendChild(crearBoton("Cancelar", "btn-cancelar", "cancelar", prestamo.idPrestamo));
        }
        if (prestamo.estado === "Activo") acciones.appendChild(crearBoton("Registrar devolución", "btn-estado", "devolver", prestamo.idPrestamo));
    }
    fila.appendChild(acciones);
    return fila;
}

function renderizarPrestamos(prestamos) {
    prestamosPorId.clear();
    if (!prestamos.length) {
        mostrarMensaje("No hay préstamos registrados.");
        return;
    }
    const fragmento = document.createDocumentFragment();
    prestamos.forEach(prestamo => fragmento.appendChild(construirFilaPrestamo(prestamo)));
    tabla.replaceChildren(fragmento);
}

function aplicarFiltros() {
    const estado = (filtroEstado?.value ?? "").toLowerCase();
    const nombre = (filtroNombre?.value ?? "").toLowerCase();
    const equipo = (filtroEquipo?.value ?? "").toLowerCase();
    const cedula = (filtroCedula?.value ?? "").toLowerCase();
    const fechaPrestamo = filtroFechaPrestamo?.value ?? "";
    const fechaDevolucion = filtroFechaDevolucion?.value ?? "";
    tabla.querySelectorAll("tr").forEach(fila => {
        if (fila.children.length < 9) return;
        fila.hidden = !((!estado || fila.children[7].textContent.toLowerCase() === estado) &&
            (!nombre || fila.children[3].textContent.toLowerCase().includes(nombre)) &&
            (!equipo || fila.children[1].textContent.toLowerCase().includes(equipo)) &&
            (!cedula || fila.children[4].textContent.toLowerCase().includes(cedula)) &&
            (!fechaPrestamo || fila.children[5].textContent.trim() === fechaPrestamo) &&
            (!fechaDevolucion || fila.children[6].textContent.trim() === fechaDevolucion));
    });
}

function validarFechas(fechaPrestamoId, fechaDevolucionId, evento) {
    const fechaPrestamo = document.getElementById(fechaPrestamoId).value;
    const fechaDevolucion = document.getElementById(fechaDevolucionId).value;
    if (fechaPrestamo && fechaDevolucion && fechaDevolucion < fechaPrestamo) {
        evento.preventDefault();
        window.alert("La fecha de devolución debe ser posterior o igual a la fecha de préstamo.");
    }
}

async function enviarPrestamo(metodo, datos) {
    const respuesta = await fetch("api/prestamos.php", {
        method: metodo,
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(datos)
    });
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo completar la operación.");
    await cargarPrestamos();
}

btnNuevoPrestamo?.addEventListener("click", () => { formulario.reset(); modal.hidden = false; });
cerrarModal?.addEventListener("click", () => { modal.hidden = true; });
cerrarModalModificar?.addEventListener("click", () => { modalModificar.hidden = true; });
formulario?.addEventListener("submit", async evento => {
    evento.preventDefault();
    if (document.getElementById("fechaPrestamo").value && document.getElementById("fechaDevolucion").value < document.getElementById("fechaPrestamo").value) {
        window.alert("La fecha de devolución debe ser posterior o igual a la fecha de préstamo.");
        return;
    }
    try {
        await enviarPrestamo("POST", Object.fromEntries(new FormData(formulario).entries()));
        formulario.reset();
        modal.hidden = true;
    } catch (error) { window.alert(error.message); }
});
formularioModificar?.addEventListener("submit", async evento => {
    evento.preventDefault();
    if (document.getElementById("modificarFechaPrestamo").value && document.getElementById("modificarFechaDevolucion").value < document.getElementById("modificarFechaPrestamo").value) {
        window.alert("La fecha de devolución debe ser posterior o igual a la fecha de préstamo.");
        return;
    }
    const datos = Object.fromEntries(new FormData(formularioModificar).entries());
    datos.idPrestamo = Number(datos.idPrestamo);
    datos.idEquipo = Number(datos.idEquipo);
    datos.accion = "modificar";
    try { await enviarPrestamo("PUT", datos); modalModificar.hidden = true; }
    catch (error) { window.alert(error.message); }
});

tabla?.addEventListener("click", async evento => {
    const boton = evento.target.closest("button[data-accion-prestamo]");
    if (!boton) return;
    const id = Number(boton.dataset.idPrestamo);
    const accion = boton.dataset.accionPrestamo;
    if (accion === "modificar") {
        const prestamo = prestamosPorId.get(String(id));
        if (!prestamo) return;
        document.getElementById("modificarIdPrestamo").value = prestamo.idPrestamo;
        document.getElementById("modificarIdEquipo").value = prestamo.idEquipo;
        document.getElementById("modificarFechaPrestamo").value = prestamo.fechaPrestamo;
        document.getElementById("modificarFechaDevolucion").value = prestamo.fechaDevolucion;
        modalModificar.hidden = false;
        return;
    }
    if (accion === "cancelar" && !window.confirm("¿Deseas cancelar este préstamo?")) return;
    try { await enviarPrestamo("PUT", { idPrestamo: id, accion }); }
    catch (error) { window.alert(error.message); }
});

[filtroEstado, filtroNombre, filtroEquipo, filtroCedula, filtroFechaPrestamo, filtroFechaDevolucion].forEach(elemento => {
    elemento?.addEventListener(elemento.type === "text" ? "input" : "change", aplicarFiltros);
});

async function cargarPrestamos() {
    try {
        const respuesta = await fetch("api/prestamos.php", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        const datos = resultado.data;
        if (!respuesta.ok || resultado.status !== "success" || !datos || !Array.isArray(datos.prestamos) || !Array.isArray(datos.equipos) || !Array.isArray(datos.estados)) {
            throw new Error(resultado.message || "La API no devolvió la estructura esperada.");
        }
        renderizarEquipos(datos.equipos);
        renderizarEstados(datos.estados);
        renderizarPrestamos(datos.prestamos);
        aplicarFiltros();
    } catch (error) {
        console.error(error);
        mostrarMensaje(error.message || "No se pudieron cargar los préstamos.");
    }
}

cargarPrestamos();

const tablaTickets = document.getElementById("tablaTickets");
const modalTicket = document.getElementById("modalTicket");
const btnNuevoTicket = document.getElementById("btnNuevoTicket");
const cerrarModal = document.getElementById("cerrarModal");
const formularioTicket = document.getElementById("ticketForm");
const filtroEstado = document.getElementById("filtroEstado");
const filtroLaboratorio = document.getElementById("filtroLaboratorio");
const filtroEquipo = document.getElementById("filtroEquipo");
const dialogTicket = document.getElementById("dialogTicket");
const cerrarDetalleTicket = document.getElementById("cerrarDetalleTicket");
const esTecnico = document.body.dataset.esTecnico === "true";
const ticketsPorId = new Map();

function celda(texto) {
    const td = document.createElement("td");
    td.textContent = texto ?? "";
    return td;
}

function boton(texto, clase, atributos = {}) {
    const elemento = document.createElement("button");
    elemento.type = "button";
    elemento.textContent = texto;
    elemento.className = clase;
    Object.entries(atributos).forEach(([clave, valor]) => elemento.dataset[clave] = String(valor));
    return elemento;
}

function mostrarMensajeTabla(mensaje) {
    const fila = document.createElement("tr");
    const celdaMensaje = celda(mensaje);
    celdaMensaje.colSpan = 7;
    fila.appendChild(celdaMensaje);
    tablaTickets.replaceChildren(fila);
}

function construirFilaTicket(ticket) {
    ticketsPorId.set(String(ticket.idTicket), ticket);
    const fila = document.createElement("tr");
    [ticket.idTicket, ticket.fechaRegistro, ticket.lugar, ticket.titulo, ticket.prioridad, ticket.estado].forEach(valor => fila.appendChild(celda(valor)));
    const acciones = document.createElement("td");
    acciones.className = "acciones";
    acciones.appendChild(boton("Ver", "btn-ver", { idTicket: ticket.idTicket }));
    if (esTecnico && !["Resuelto", "Cancelado"].includes(ticket.estado)) {
        if (ticket.estado === "Pendiente" || ticket.estado === "Vencido") {
            acciones.appendChild(boton("Iniciar", "btn-estado", { idTicket: ticket.idTicket, estadoTicket: "En Proceso" }));
        }
        if (ticket.estado === "En Proceso") {
            acciones.appendChild(boton("Resolver", "btn-estado", { idTicket: ticket.idTicket, estadoTicket: "Resuelto" }));
        }
        acciones.appendChild(boton("Cancelar", "btn-cancelar", { idTicket: ticket.idTicket, estadoTicket: "Cancelado" }));
    }
    fila.appendChild(acciones);
    return fila;
}

function aplicarFiltros() {
    const estado = filtroEstado?.value ?? "";
    const lugar = (filtroLaboratorio?.value ?? "").toLowerCase().trim();
    const titulo = (filtroEquipo?.value ?? "").toLowerCase().trim();
    tablaTickets.querySelectorAll("tr").forEach(fila => {
        if (fila.children.length < 7) return;
        fila.hidden = !((!estado || fila.children[5].textContent.trim() === estado) &&
            (!lugar || fila.children[2].textContent.toLowerCase().includes(lugar)) &&
            (!titulo || fila.children[3].textContent.toLowerCase().includes(titulo)));
    });
}

function mostrarDetalleTicket(ticket) {
    const campos = {
        detalleId: ticket.idTicket,
        detalleTitulo: ticket.titulo,
        detalleDescripcion: ticket.descripcion,
        detalleLugar: ticket.lugar,
        detalleSolicitante: ticket.solicitante,
        detallePrioridad: ticket.prioridad,
        detalleEstado: ticket.estado,
        detalleFechaRegistro: ticket.fechaRegistro,
        detalleFechaLimite: ticket.fechaLimite
    };
    Object.entries(campos).forEach(([id, valor]) => {
        const elemento = document.getElementById(id);
        if (elemento) elemento.textContent = valor ?? "";
    });
    dialogTicket?.showModal();
}

async function cargarTickets() {
    try {
        const respuesta = await fetch("api/tickets.php", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== "success" || !Array.isArray(resultado.data?.tickets)) {
            throw new Error(resultado.message || "No se pudieron cargar los tickets.");
        }
        ticketsPorId.clear();
        const fragmento = document.createDocumentFragment();
        if (resultado.data.tickets.length === 0) {
            const fila = document.createElement("tr");
            const mensaje = celda("No hay tickets registrados.");
            mensaje.colSpan = 7;
            fila.appendChild(mensaje);
            fragmento.appendChild(fila);
        } else {
            resultado.data.tickets.forEach(ticket => fragmento.appendChild(construirFilaTicket(ticket)));
        }
        tablaTickets.replaceChildren(fragmento);
        aplicarFiltros();
    } catch (error) {
        console.error(error);
        mostrarMensajeTabla(error.message || "No se pudieron cargar los tickets.");
    }
}

btnNuevoTicket?.addEventListener("click", () => { modalTicket.hidden = false; });
cerrarModal?.addEventListener("click", () => { modalTicket.hidden = true; });
modalTicket?.addEventListener("click", evento => { if (evento.target === modalTicket) modalTicket.hidden = true; });
cerrarDetalleTicket?.addEventListener("click", () => dialogTicket?.close());
filtroEstado?.addEventListener("change", aplicarFiltros);
filtroLaboratorio?.addEventListener("input", aplicarFiltros);
filtroEquipo?.addEventListener("input", aplicarFiltros);

tablaTickets?.addEventListener("click", async evento => {
    const elemento = evento.target.closest("button");
    if (!elemento) return;
    const id = elemento.dataset.idTicket;
    if (elemento.classList.contains("btn-ver")) {
        const ticket = ticketsPorId.get(String(id));
        if (ticket) mostrarDetalleTicket(ticket);
        return;
    }
    if (!elemento.classList.contains("btn-estado") && !elemento.classList.contains("btn-cancelar")) return;
    if (elemento.dataset.estadoTicket === "Cancelado" && !window.confirm("¿Deseas cancelar este ticket?")) return;
    const estado = elemento.dataset.estadoTicket;
    try {
        const respuesta = await fetch("api/tickets.php", {
            method: "PUT",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify({ idTicket: Number(id), accion: "estado", estado })
        });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo actualizar el ticket.");
        await cargarTickets();
    } catch (error) {
        window.alert(error.message);
    }
});

formularioTicket?.addEventListener("submit", async evento => {
    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(formularioTicket).entries());
    try {
        const respuesta = await fetch("api/tickets.php", {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify(datos)
        });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo registrar el ticket.");
        formularioTicket.reset();
        modalTicket.hidden = true;
        await cargarTickets();
    } catch (error) {
        window.alert(error.message);
    }
});

cargarTickets();

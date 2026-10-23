const tabla = document.getElementById("tablaHistorial");
const filtroTipo = document.getElementById("filtroTipo");
const filtroEstado = document.getElementById("filtroEstado");
const filtroUsuario = document.getElementById("filtroUsuario");
const filtroFecha = document.getElementById("filtroFecha");

function crearCelda(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}

function mostrarMensaje(mensaje) {
    const fila = document.createElement("tr");
    const celda = crearCelda(mensaje);
    celda.colSpan = 6;
    fila.appendChild(celda);
    tabla.replaceChildren(fila);
}

function renderizarHistorial(registros) {
    if (!registros.length) {
        mostrarMensaje("No hay registros en el historial.");
        return;
    }
    const fragmento = document.createDocumentFragment();
    registros.forEach(registro => {
        const fila = document.createElement("tr");
        [registro.id, registro.tipo, registro.detalle, registro.usuario, registro.estado, registro.fecha].forEach(valor => fila.appendChild(crearCelda(valor)));
        fragmento.appendChild(fila);
    });
    tabla.replaceChildren(fragmento);
}

function aplicarFiltros() {
    const tipo = filtroTipo.value.toLowerCase();
    const estado = filtroEstado.value.toLowerCase();
    const usuario = filtroUsuario.value.toLowerCase();
    const fecha = filtroFecha.value;
    tabla.querySelectorAll("tr").forEach(fila => {
        if (fila.children.length < 6) return;
        const coincide = (!tipo || fila.children[1].textContent.trim().toLowerCase() === tipo) &&
            (!estado || fila.children[4].textContent.trim().toLowerCase() === estado) &&
            (!usuario || fila.children[3].textContent.trim().toLowerCase().includes(usuario)) &&
            (!fecha || fila.children[5].textContent.trim().startsWith(fecha));
        fila.hidden = !coincide;
    });
}

async function cargarHistorial() {
    try {
        const respuesta = await fetch("api/historial.php", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== "success" || !Array.isArray(resultado.data)) {
            throw new Error(resultado.message || "No se pudo cargar el historial.");
        }
        renderizarHistorial(resultado.data);
        aplicarFiltros();
    } catch (error) {
        console.error(error);
        mostrarMensaje(error.message || "No se pudo cargar el historial.");
    }
}

filtroTipo.addEventListener("change", aplicarFiltros);
filtroEstado.addEventListener("change", aplicarFiltros);
filtroUsuario.addEventListener("input", aplicarFiltros);
filtroFecha.addEventListener("change", aplicarFiltros);
cargarHistorial();

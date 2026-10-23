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
    const anterior = filtroUsuario.value;
    filtroUsuario.replaceChildren(new Option('Todos los usuarios', ''));
    [...new Set(registros.map(r => r.usuario).filter(Boolean))].sort().forEach(usuario => filtroUsuario.add(new Option(usuario, usuario)));
    filtroUsuario.value = anterior;
    if (!registros.length) {
        mostrarMensaje("No hay registros en el historial.");
        return;
    }
    const fragmento = document.createDocumentFragment();
    registros.forEach(registro => {
        const fila = document.createElement("tr");
        fila.dataset.tipoOriginal = registro.tipo || "";
        fila.dataset.estadoOriginal = registro.estado || "";
        fila.dataset.usuarioOriginal = registro.usuario || "";
        fila.dataset.fechaOriginal = String(registro.fecha || '').slice(0, 10);
        [registro.id, registro.tipo, registro.detalle, registro.usuario, registro.estado, String(registro.fecha || '').slice(0, 10)].forEach(valor => fila.appendChild(crearCelda(valor)));
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
        const coincide = (!tipo || fila.dataset.tipoOriginal.toLowerCase() === tipo) &&
            (!estado || fila.dataset.estadoOriginal.toLowerCase() === estado) &&
            (!usuario || fila.dataset.usuarioOriginal.toLowerCase().includes(usuario)) &&
            (!fecha || fila.dataset.fechaOriginal.startsWith(fecha));
        fila.hidden = !coincide;
    });
}

async function cargarHistorial() {
    try {
        const respuesta = await fetch("../index.php?api=historial", { headers: { Accept: "application/json" }, cache: "no-store" });
        const texto = await respuesta.text();
        let resultado;
        try { resultado = JSON.parse(texto); } catch { throw new Error(`Respuesta inválida del servidor (HTTP ${respuesta.status}).`); }
        if (!respuesta.ok || resultado.status !== "success" || !Array.isArray(resultado.data?.historial)) {
            throw new Error(resultado.message || "No se pudo cargar el historial.");
        }
        renderizarHistorial(resultado.data.historial);
        aplicarFiltros();
    } catch (error) {
        console.error(error);
        mostrarMensaje(error.message || "No se pudo cargar el historial.");
    }
}

filtroTipo.addEventListener("change", aplicarFiltros);
filtroEstado.addEventListener("change", aplicarFiltros);
filtroUsuario.addEventListener("change", aplicarFiltros);
filtroFecha.addEventListener("change", aplicarFiltros);
cargarHistorial();

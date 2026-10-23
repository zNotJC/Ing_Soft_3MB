const tipoCatalogo = document.getElementById("tipoCatalogo");
const nombreCatalogo = document.getElementById("nombreCatalogo");
const tablaCatalogos = document.getElementById("tablaCatalogos");
const mensajeCatalogos = document.getElementById("mensajeCatalogos");
const guardarCatalogo = document.getElementById("guardarCatalogo");
const cancelarCatalogo = document.getElementById("cancelarCatalogo");
let idEdicion = null;
let registros = new Map();

function celda(texto) {
    const elemento = document.createElement("td");
    elemento.textContent = texto ?? "";
    return elemento;
}

async function apiCatalogos(metodo, datos = null) {
    const opciones = { method: metodo, headers: { Accept: "application/json" } };
    let url = "api/catalogos.php";
    if (metodo === "GET" && datos) {
        url += `?tipo=${encodeURIComponent(datos.tipo)}`;
    } else if (datos) {
        opciones.headers["Content-Type"] = "application/json";
        opciones.body = JSON.stringify(datos);
    }
    const respuesta = await fetch(url, opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo completar la operación.");
    return resultado.data;
}

function renderizar(lista) {
    registros = new Map(lista.map(registro => [String(registro.id), registro]));
    if (!lista.length) {
        const fila = document.createElement("tr");
        const celdaVacia = celda("No hay registros en este catálogo.");
        celdaVacia.colSpan = 3;
        fila.appendChild(celdaVacia);
        tablaCatalogos.replaceChildren(fila);
        return;
    }
    const fragmento = document.createDocumentFragment();
    lista.forEach(registro => {
        const fila = document.createElement("tr");
        fila.append(celda(registro.id), celda(registro.nombre));
        const acciones = document.createElement("td");
        const editar = document.createElement("button");
        editar.type = "button";
        editar.className = "btn btn-sm btn-primary me-2";
        editar.textContent = "Modificar";
        editar.dataset.accionCatalogo = "editar";
        editar.dataset.id = registro.id;
        const eliminar = document.createElement("button");
        eliminar.type = "button";
        eliminar.className = "btn btn-sm btn-danger";
        eliminar.textContent = "Eliminar";
        eliminar.dataset.accionCatalogo = "eliminar";
        eliminar.dataset.id = registro.id;
        acciones.append(editar, eliminar);
        fila.appendChild(acciones);
        fragmento.appendChild(fila);
    });
    tablaCatalogos.replaceChildren(fragmento);
}

async function cargarCatalogo() {
    try { renderizar(await apiCatalogos("GET", { tipo: tipoCatalogo.value })); }
    catch (error) { mensajeCatalogos.textContent = error.message; }
}

async function guardar() {
    const nombre = nombreCatalogo.value.trim();
    if (!nombre) { mensajeCatalogos.textContent = "El nombre es obligatorio."; return; }
    try {
        await apiCatalogos(idEdicion === null ? "POST" : "PUT", { tipo: tipoCatalogo.value, id: idEdicion, nombre });
        mensajeCatalogos.textContent = idEdicion === null ? "Registro creado." : "Registro actualizado.";
        reiniciar();
        await cargarCatalogo();
    } catch (error) { mensajeCatalogos.textContent = error.message; }
}

function reiniciar() {
    idEdicion = null;
    nombreCatalogo.value = "";
    guardarCatalogo.textContent = "Agregar";
    cancelarCatalogo.hidden = true;
}

guardarCatalogo.addEventListener("click", guardar);
cancelarCatalogo.addEventListener("click", reiniciar);
tipoCatalogo.addEventListener("change", () => { reiniciar(); cargarCatalogo(); });
tablaCatalogos.addEventListener("click", async evento => {
    const boton = evento.target.closest("button[data-accion-catalogo]");
    if (!boton) return;
    const registro = registros.get(String(boton.dataset.id));
    if (!registro) return;
    if (boton.dataset.accionCatalogo === "editar") {
        idEdicion = registro.id;
        nombreCatalogo.value = registro.nombre;
        guardarCatalogo.textContent = "Guardar cambios";
        cancelarCatalogo.hidden = false;
        return;
    }
    if (!window.confirm(`¿Eliminar el registro ${registro.nombre}?`)) return;
    try {
        await apiCatalogos("DELETE", { tipo: tipoCatalogo.value, id: registro.id });
        mensajeCatalogos.textContent = "Registro eliminado.";
        await cargarCatalogo();
    } catch (error) { mensajeCatalogos.textContent = error.message; }
});

cargarCatalogo();

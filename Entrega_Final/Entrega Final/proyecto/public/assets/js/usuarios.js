const formularioUsuario = document.getElementById("formUsuario");
const tablaUsuarios = document.getElementById("tablaUsuarios");
const mensajeUsuarios = document.getElementById("mensajeUsuarios");
let usuarios = new Map();
let modoEdicion = false;

function celda(valor) {
    const elemento = document.createElement("td");
    elemento.textContent = valor ?? "";
    return elemento;
}

function mostrarMensajeTabla(mensaje) {
    const fila = document.createElement("tr");
    const elemento = celda(mensaje);
    elemento.colSpan = 6;
    fila.appendChild(elemento);
    tablaUsuarios.replaceChildren(fila);
}

function renderizarUsuarios(lista) {
    usuarios = new Map(lista.map(usuario => [String(usuario.cedula), usuario]));
    if (!lista.length) {
        mostrarMensajeTabla("No hay usuarios registrados.");
        return;
    }
    const fragmento = document.createDocumentFragment();
    lista.forEach(usuario => {
        const fila = document.createElement("tr");
        [usuario.cedula, usuario.usuario, usuario.correo, usuario.rol, usuario.activo ? "Activo" : "Inactivo"].forEach(valor => fila.appendChild(celda(valor)));
        const acciones = document.createElement("td");
        const editar = document.createElement("button");
        editar.type = "button";
        editar.className = "btn btn-sm btn-primary me-2";
        editar.textContent = "Modificar";
        editar.dataset.accionUsuario = "editar";
        editar.dataset.cedula = usuario.cedula;
        acciones.appendChild(editar);
        if (usuario.activo) {
            const desactivar = document.createElement("button");
            desactivar.type = "button";
            desactivar.className = "btn btn-sm btn-danger";
            desactivar.textContent = "Desactivar";
            desactivar.dataset.accionUsuario = "desactivar";
            desactivar.dataset.cedula = usuario.cedula;
            acciones.appendChild(desactivar);
        }
        fila.appendChild(acciones);
        fragmento.appendChild(fila);
    });
    tablaUsuarios.replaceChildren(fragmento);
}

async function apiUsuarios(metodo, datos = null) {
    const opciones = { method: metodo, headers: { Accept: "application/json" } };
    if (datos !== null) {
        opciones.headers["Content-Type"] = "application/json";
        opciones.body = JSON.stringify(datos);
    }
    const respuesta = await fetch("api/usuarios.php", opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo completar la operación.");
    return resultado.data;
}

async function cargarUsuarios() {
    try {
        renderizarUsuarios(await apiUsuarios("GET"));
    } catch (error) { mostrarMensajeTabla(error.message); }
}

formularioUsuario.addEventListener("submit", async evento => {
    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(formularioUsuario).entries());
    const metodo = modoEdicion ? "PUT" : "POST";
    if (modoEdicion && !datos.contrasena) delete datos.contrasena;
    try {
        await apiUsuarios(metodo, datos);
        mensajeUsuarios.textContent = modoEdicion ? "Usuario actualizado." : "Usuario creado.";
        reiniciarFormulario();
        await cargarUsuarios();
    } catch (error) { mensajeUsuarios.textContent = error.message; }
});

tablaUsuarios.addEventListener("click", async evento => {
    const boton = evento.target.closest("button[data-accion-usuario]");
    if (!boton) return;
    const usuario = usuarios.get(String(boton.dataset.cedula));
    if (!usuario) return;
    if (boton.dataset.accionUsuario === "editar") {
        modoEdicion = true;
        document.getElementById("cedula").value = usuario.cedula;
        document.getElementById("cedula").readOnly = true;
        document.getElementById("usuario").value = usuario.usuario;
        document.getElementById("correo").value = usuario.correo;
        document.getElementById("rol").value = usuario.rol;
        document.getElementById("contrasena").required = false;
        document.getElementById("guardarUsuario").textContent = "Guardar cambios";
        document.getElementById("cancelarEdicionUsuario").hidden = false;
        return;
    }
    if (!window.confirm(`¿Desactivar la cuenta ${usuario.usuario}?`)) return;
    try {
        await apiUsuarios("DELETE", { cedula: usuario.cedula });
        mensajeUsuarios.textContent = "Usuario desactivado.";
        await cargarUsuarios();
    } catch (error) { mensajeUsuarios.textContent = error.message; }
});

function reiniciarFormulario() {
    formularioUsuario.reset();
    modoEdicion = false;
    document.getElementById("cedula").readOnly = false;
    document.getElementById("contrasena").required = true;
    document.getElementById("guardarUsuario").textContent = "Crear usuario";
    document.getElementById("cancelarEdicionUsuario").hidden = true;
}

document.getElementById("cancelarEdicionUsuario").addEventListener("click", reiniciarFormulario);
cargarUsuarios();

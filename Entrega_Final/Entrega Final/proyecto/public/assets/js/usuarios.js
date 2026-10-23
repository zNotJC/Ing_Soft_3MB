const formularioUsuario = document.getElementById("formUsuario");
const tablaUsuarios = document.getElementById("tablaUsuarios");
const mensajeUsuarios = document.getElementById("mensajeUsuarios");
const campoCedula = document.getElementById("cedula");
const campoContrasena = document.getElementById("contrasena");
const ayudaContrasena = document.getElementById("ayudaContrasena");
let usuarios = new Map();
let modoEdicion = false;
let cedulaOriginal = "";

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

function crearBoton(texto, clase, accion, cedula) {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = clase;
    boton.textContent = texto;
    boton.dataset.accionUsuario = accion;
    boton.dataset.cedula = cedula;
    return boton;
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
        [usuario.cedula, usuario.usuario, usuario.correo, usuario.rol === "Tecnico" ? "Técnico" : usuario.rol, usuario.activo ? "Activo" : "Inactivo"].forEach(valor => fila.appendChild(celda(valor)));
        const acciones = document.createElement("td");
        acciones.className = "acciones-tabla";
        acciones.appendChild(crearBoton("Modificar", "btn btn-sm btn-primary", "editar", usuario.cedula));
        if (usuario.activo) {
            acciones.appendChild(crearBoton("Desactivar", "btn btn-sm btn-warning me-2", "desactivar", usuario.cedula));
        } else {
            acciones.appendChild(crearBoton("Activar", "btn btn-sm btn-success me-2", "activar", usuario.cedula));
        }
        acciones.appendChild(crearBoton("Eliminar", "btn btn-sm btn-danger", "eliminar", usuario.cedula));
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
    const respuesta = await fetch("../index.php?api=usuarios", opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== "success") throw new Error(resultado.message || "No se pudo completar la operación.");
    return resultado.data;
}

async function cargarUsuarios() {
    try {
        const datos = await apiUsuarios("GET");
        renderizarUsuarios(Array.isArray(datos) ? datos : []);
    } catch (error) {
        mostrarMensajeTabla(error.message);
    }
}

formularioUsuario.addEventListener("submit", async evento => {
    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(formularioUsuario).entries());
    const metodo = modoEdicion ? "PUT" : "POST";
    if (modoEdicion) {
        datos.cedulaOriginal = cedulaOriginal;
        if (!datos.contrasena) datos.contrasena = '';
    }
    try {
        await apiUsuarios(metodo, datos);
        mensajeUsuarios.textContent = modoEdicion ? "Usuario actualizado correctamente." : "Usuario creado correctamente.";
        reiniciarFormulario();
        await cargarUsuarios();
    } catch (error) {
        mensajeUsuarios.textContent = error.message;
    }
});

tablaUsuarios.addEventListener("click", async evento => {
    const boton = evento.target.closest("button[data-accion-usuario]");
    if (!boton) return;
    const usuario = usuarios.get(String(boton.dataset.cedula));
    if (!usuario) return;
    const accion = boton.dataset.accionUsuario;
    if (accion === "editar") {
        modoEdicion = true;
        cedulaOriginal = String(usuario.cedula);
        campoCedula.value = usuario.cedula;
        campoCedula.readOnly = false;
        document.getElementById("usuario").value = usuario.usuario;
        document.getElementById("correo").value = usuario.correo;
        document.getElementById("rol").value = usuario.rol;
        campoContrasena.value = "";
        campoContrasena.required = false;
        campoContrasena.placeholder = "Vacía para conservar la contraseña actual";
        campoContrasena.autocomplete = "new-password";
        if (ayudaContrasena) ayudaContrasena.textContent = "Por seguridad no se puede recuperar la contraseña actual. Escribe una nueva o deja el campo vacío para conservarla.";
        document.getElementById("guardarUsuario").textContent = "Guardar cambios";
        document.getElementById("cancelarEdicionUsuario").hidden = false;
        campoCedula.focus();
        return;
    }
    if (accion === "desactivar") {
        if (!window.confirm(`¿Desactivar la cuenta ${usuario.usuario}?`)) return;
        try {
            await apiUsuarios("DELETE", { cedula: usuario.cedula, accion: "desactivar" });
            mensajeUsuarios.textContent = "Usuario desactivado.";
            await cargarUsuarios();
        } catch (error) { mensajeUsuarios.textContent = error.message; }
        return;
    }
    if (accion === "eliminar") {
        if (!window.confirm(`¿Eliminar definitivamente la cuenta ${usuario.usuario}? Si tiene tickets, solicitudes o préstamos asociados, la operación será rechazada.`)) return;
        try {
            await apiUsuarios("DELETE", { cedula: usuario.cedula });
            mensajeUsuarios.textContent = "Usuario eliminado.";
            await cargarUsuarios();
        } catch (error) { mensajeUsuarios.textContent = error.message; }
        return;
    }
    if (accion === "activar") {
        if (!window.confirm(`¿Activar la cuenta ${usuario.usuario}?`)) return;
        try {
            await apiUsuarios("PUT", { accion: "activar", cedula: usuario.cedula });
            mensajeUsuarios.textContent = "Usuario activado.";
            await cargarUsuarios();
        } catch (error) { mensajeUsuarios.textContent = error.message; }
    }
});

function reiniciarFormulario() {
    formularioUsuario.reset();
    modoEdicion = false;
    cedulaOriginal = "";
    campoCedula.readOnly = false;
    campoContrasena.required = true;
    campoContrasena.placeholder = "Mínimo 8 caracteres";
    campoContrasena.autocomplete = "new-password";
    if (ayudaContrasena) ayudaContrasena.textContent = "Al modificar, deja el campo vacío para conservar la contraseña actual.";
    document.getElementById("guardarUsuario").textContent = "Crear usuario";
    document.getElementById("cancelarEdicionUsuario").hidden = true;
}

document.getElementById("cancelarEdicionUsuario").addEventListener("click", reiniciarFormulario);
cargarUsuarios();

function crearCelda(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}

function mostrarEstadoTabla(tabla, mensaje, columnas) {
    const fila = document.createElement("tr");
    const celda = crearCelda(mensaje);
    celda.colSpan = columnas;
    fila.appendChild(celda);
    tabla.replaceChildren(fila);
}

async function cargarDashboard() {
    const tablaAccesos = document.getElementById("tablaAccesos");
    try {
        const respuesta = await fetch("api/dashboard.php", { headers: { Accept: "application/json" } });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== "success" || !resultado.data) {
            throw new Error(resultado.message || "No se pudo cargar el dashboard.");
        }
        const datos = resultado.data;
        ["ticketsPendientes", "ticketsResueltos", "solicitudesActivas", "prestamosActivos", "equiposRegistrados"].forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) elemento.textContent = datos[id] ?? 0;
        });
        if (!Array.isArray(datos.ultimosAccesos) || datos.ultimosAccesos.length === 0) {
            mostrarEstadoTabla(tablaAccesos, "No hay accesos registrados.", 3);
            return;
        }
        const fragmento = document.createDocumentFragment();
        datos.ultimosAccesos.forEach(acceso => {
            const fila = document.createElement("tr");
            fila.append(crearCelda(acceso.usuario), crearCelda(acceso.rol), crearCelda(acceso.fechaHora));
            fragmento.appendChild(fila);
        });
        tablaAccesos.replaceChildren(fragmento);
    } catch (error) {
        console.error(error);
        if (tablaAccesos) mostrarEstadoTabla(tablaAccesos, error.message || "No se pudo cargar la información.", 3);
    }
}


cargarDashboard();

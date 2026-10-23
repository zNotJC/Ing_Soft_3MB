const modal = document.getElementById('modalPrestamo');
const btnNuevo = document.getElementById('btnNuevoPrestamo');
const cerrar = document.getElementById('cerrarModal');
const form = document.getElementById('formPrestamo');
const tabla = document.getElementById('tablaPrestamos');
const dialogRevision = document.getElementById('dialogRevisionPrestamo');
const esTecnico = document.body.dataset.esTecnico === 'true';
const porId = new Map();
let equipos = [];
let prestamoEnRevision = null;

function celda(valor) { const td = document.createElement('td'); td.textContent = valor ?? ''; return td; }
function boton(texto, clase, accion, id) { const b = document.createElement('button'); b.type = 'button'; b.textContent = texto; b.className = clase; b.dataset.accionPrestamo = accion; b.dataset.idPrestamo = id; return b; }
function mensaje(texto) { const tr = document.createElement('tr'); const td = celda(texto); td.colSpan = 10; tr.append(td); tabla.replaceChildren(tr); }
function poblarEquipos(select, tipo = '', seleccionado = '') {
    if (!select) return;
    select.replaceChildren(new Option('Seleccione un equipo', ''));
    equipos.filter(e => tipo && e.tipo === tipo && e.estado === 'Disponible').forEach(e => select.add(new Option(e.nombre, e.idEquipo)));
    if (seleccionado) select.value = String(seleccionado);
}
function renderEquipos(datos) {
    equipos = datos;
    poblarEquipos(document.getElementById('idEquipo'), document.getElementById('tipoEquipoPrestamo')?.value || '');
}
function cargarFiltros(prestamos) {
    [['filtroEstado', 'Todos los estados', 'estado'], ['filtroNombre', 'Todos los usuarios', 'nombre'], ['filtroEquipo', 'Todos los equipos', 'equipo'], ['filtroCedula', 'Todas las cédulas', 'cedula']].forEach(([id, etiqueta, campo]) => {
        const select = document.getElementById(id); if (!select) return;
        const anterior = select.value;
        select.replaceChildren(new Option(etiqueta, ''));
        [...new Set(prestamos.map(p => p[campo]).filter(v => v !== null && v !== undefined && v !== ''))].sort().forEach(v => select.add(new Option(v, v)));
        select.value = anterior;
    });
}
function renderizar(prestamos) {
    porId.clear(); cargarFiltros(prestamos); tabla.replaceChildren();
    if (!prestamos.length) { mensaje('No hay préstamos registrados.'); return; }
    prestamos.forEach(p => {
        porId.set(String(p.idPrestamo), p);
        const tr = document.createElement('tr');
        tr.dataset.estadoOriginal = p.estado || '';
        tr.dataset.nombreOriginal = p.nombre || '';
        tr.dataset.equipoOriginal = p.equipo || '';
        tr.dataset.cedulaOriginal = p.cedula || '';
        [p.idPrestamo, p.equipo, p.tipoEquipo, p.nombre, p.cedula, p.fechaPrestamo, p.fechaDevolucion, p.estado, p.prioridad].forEach(v => tr.append(celda(v)));
        const acciones = celda('');
        if (esTecnico && ['En espera', 'Pendiente'].includes(p.estado)) {
            if (!p.prioridad || p.prioridad === '-') acciones.append(boton('Revisar préstamo', 'btn btn-sm btn-primary', 'revisar', p.idPrestamo));
            else acciones.append(boton('Aceptar', 'btn btn-sm btn-success', 'aceptar', p.idPrestamo), boton('Denegar', 'btn btn-sm btn-danger', 'denegar', p.idPrestamo));
        }
        if (esTecnico && ['Aceptado', 'Activo'].includes(p.estado)) acciones.append(boton('Registrar devolución', 'btn btn-sm btn-secondary', 'devolver', p.idPrestamo));
        if (esTecnico && ['En espera', 'Pendiente'].includes(p.estado)) acciones.append(boton('Cancelar', 'btn btn-sm btn-danger', 'cancelar', p.idPrestamo));
        tr.append(acciones); tabla.append(tr);
    });
    filtrarPrestamos();
}
async function api(metodo, datos) {
    const opciones = { method: metodo, headers: { Accept: 'application/json' } };
    if (datos) { opciones.headers['Content-Type'] = 'application/json'; opciones.body = JSON.stringify(datos); }
    const respuesta = await fetch('../index.php?api=prestamos', { ...opciones, cache: 'no-store' });
    let json; try { json = await respuesta.json(); } catch { throw new Error(`Respuesta inválida del servidor (HTTP ${respuesta.status}).`); }
    if (!respuesta.ok || json.status !== 'success') throw new Error(json.message || 'No se pudo completar la operación.');
    return json.data;
}
async function cargar() { try { const datos = await api('GET'); renderEquipos(datos.equipos || []); renderizar(datos.prestamos || []); } catch (e) { console.error(e); mensaje(e.message); } }
function abrirRevisionPrestamo(prestamo) {
    prestamoEnRevision = prestamo;
    document.getElementById('revisionPrestamoEquipo').textContent = `${prestamo.equipo} (${prestamo.tipoEquipo})`;
    document.getElementById('revisionPrestamoSolicitante').textContent = prestamo.nombre;
    document.getElementById('revisionPrestamoPrioridad').value = '';
    dialogRevision?.showModal();
}
function filtrarPrestamos() {
    const estado = document.getElementById('filtroEstado')?.value || '';
    const nombre = document.getElementById('filtroNombre')?.value || '';
    const equipo = document.getElementById('filtroEquipo')?.value || '';
    const cedula = document.getElementById('filtroCedula')?.value || '';
    tabla.querySelectorAll('tr').forEach(tr => {
        if (tr.children.length < 9) return;
        tr.hidden = Boolean((estado && tr.dataset.estadoOriginal !== estado) || (nombre && tr.dataset.nombreOriginal !== nombre) || (equipo && tr.dataset.equipoOriginal !== equipo) || (cedula && tr.dataset.cedulaOriginal !== cedula));
    });
}
btnNuevo?.addEventListener('click', () => { form?.reset(); poblarEquipos(document.getElementById('idEquipo'), document.getElementById('tipoEquipoPrestamo')?.value || ''); if (modal) modal.hidden = false; });
cerrar?.addEventListener('click', () => { if (modal) modal.hidden = true; });
document.getElementById('tipoEquipoPrestamo')?.addEventListener('change', e => poblarEquipos(document.getElementById('idEquipo'), e.target.value));
form?.addEventListener('submit', async e => { e.preventDefault(); const datos = Object.fromEntries(new FormData(form)); if (datos.fechaDevolucion < datos.fechaPrestamo) { alert('La fecha de devolución no puede ser anterior a la fecha de préstamo.'); return; } datos.idEquipo = Number(datos.idEquipo); try { await api('POST', datos); modal.hidden = true; await cargar(); } catch (err) { alert(err.message); } });
tabla?.addEventListener('click', async e => {
    const b = e.target.closest('button[data-accion-prestamo]'); if (!b || !esTecnico) return;
    const idPrestamo = Number(b.dataset.idPrestamo), accion = b.dataset.accionPrestamo;
    try {
        if (accion === 'revisar') {
            const prestamo = porId.get(String(idPrestamo));
            if (prestamo) abrirRevisionPrestamo(prestamo);
            return;
        } else {
            if (accion === 'cancelar' && !confirm('¿Deseas cancelar este préstamo?')) return;
            if (accion === 'denegar' && !confirm('¿Deseas denegar este préstamo?')) return;
            await api('PUT', { idPrestamo, accion });
        }
        await cargar();
    } catch (err) { alert(err.message); }
});
document.getElementById('guardarRevisionPrestamo')?.addEventListener('click', async () => {
    const botonRevision = document.getElementById('guardarRevisionPrestamo');
    const fila = botonRevision?.closest('dialog');
    const prestamo = prestamoEnRevision;
    const prioridad = document.getElementById('revisionPrestamoPrioridad')?.value;
    if (!prestamo || !prioridad) { alert('Selecciona una prioridad.'); return; }
    try { await api('PUT', { idPrestamo: Number(prestamo.idPrestamo), accion: 'revisar', prioridad }); fila?.close(); prestamoEnRevision = null; await cargar(); }
    catch (error) { alert(error.message); }
});
document.getElementById('cerrarRevisionPrestamo')?.addEventListener('click', () => { dialogRevision?.close(); prestamoEnRevision = null; });
['filtroEstado', 'filtroNombre', 'filtroEquipo', 'filtroCedula'].forEach(id => document.getElementById(id)?.addEventListener('change', filtrarPrestamos));
cargar();

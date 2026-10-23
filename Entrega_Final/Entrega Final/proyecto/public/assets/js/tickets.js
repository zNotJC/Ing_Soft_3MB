const tablaTickets=document.getElementById("tablaTickets");
const modalTicket=document.getElementById("modalTicket");
const btnNuevoTicket=document.getElementById("btnNuevoTicket");
const cerrarModal=document.getElementById("cerrarModal");
const formularioTicket=document.getElementById("ticketForm");
const filtroEstado=document.getElementById("filtroEstado");
const filtroLaboratorio=document.getElementById("filtroLaboratorio");
const filtroEquipo=document.getElementById("filtroEquipo");
const dialogTicket=document.getElementById("dialogTicket");
const esTecnico=document.body.dataset.esTecnico==="true";
const esDocente=document.body.dataset.esDocente==="true";
const ticketsPorId=new Map();
let ticketRevision=null;
function celda(v){const e=document.createElement("td");e.textContent=v??"";return e;}
function boton(texto,clase,attrs={}){const b=document.createElement("button");b.type="button";b.textContent=texto;b.className=clase;Object.entries(attrs).forEach(([k,v])=>b.dataset[k]=String(v));return b;}
function mostrarMensajeTabla(texto){const tr=document.createElement("tr");const td=celda(texto);td.colSpan=7;tr.append(td);tablaTickets.replaceChildren(tr);}
function construirFilaTicket(t){
 ticketsPorId.set(String(t.idTicket),t);const tr=document.createElement("tr");
 tr.dataset.estadoOriginal=t.estado||"";tr.dataset.laboratorioOriginal=t.laboratorio||"";tr.dataset.tituloOriginal=t.titulo||"";
 [t.idTicket,t.fechaRegistro,t.laboratorio,t.titulo,t.prioridad||"-",t.estado].forEach(v=>tr.append(celda(v)));
 const acciones=celda("");acciones.className="acciones";acciones.append(boton("Ver","btn-ver",{idTicket:t.idTicket}));
 if(esTecnico){
  if(["En espera","Pendiente"].includes(t.estado)) acciones.append(boton("Revisar ticket","btn-revisar",{idTicket:t.idTicket}));
  if(t.estado==="En Proceso"){acciones.append(boton("Registrar Completado","btn-completar",{idTicket:t.idTicket}),boton("Cancelar","btn-cancelar",{idTicket:t.idTicket}));}
 }
 tr.append(acciones);return tr;
}
function aplicarFiltros(){const estado=filtroEstado?.value||"",lab=filtroLaboratorio?.value||"",titulo=(filtroEquipo?.value||"").toLowerCase().trim();tablaTickets.querySelectorAll("tr").forEach(tr=>{if(tr.children.length<7)return;tr.hidden=!!((estado&&tr.dataset.estadoOriginal!==estado)||(lab&&tr.dataset.laboratorioOriginal!==lab)||(titulo&&!tr.dataset.tituloOriginal.toLowerCase().includes(titulo)));});}
function detalle(t){const m={detalleId:t.idTicket,detalleTitulo:t.titulo,detalleDescripcion:t.descripcion,detalleLaboratorio:t.laboratorio,detalleEquipo:t.equipo||'-',detalleSolicitante:t.solicitante,detallePrioridad:t.prioridad||"-",detalleEstado:t.estado,detalleFechaRegistro:t.fechaRegistro,detalleFechaAtencion:t.fechaAtencion||"-"};Object.entries(m).forEach(([id,v])=>{const e=document.getElementById(id);if(e)e.textContent=v??"";});dialogTicket?.showModal();}
async function api(method,data){const r=await fetch("../index.php?api=tickets",{method,headers:{"Content-Type":"application/json",Accept:"application/json"},...(data?{body:JSON.stringify(data)}:{}),cache:"no-store"});let j;try{j=await r.json();}catch{throw Error(`Respuesta inválida del servidor (HTTP ${r.status}).`);}if(!r.ok||j.status!=="success")throw Error(j.message||"No se pudo completar la operación.");return j.data;}
async function cargarTickets(){try{const d=await api("GET");ticketsPorId.clear();const labs=[...new Map((d.tickets||[]).map(t=>[String(t.idLaboratorio??t.laboratorio),t.laboratorio])).entries()];if(filtroLaboratorio){const old=filtroLaboratorio.value;filtroLaboratorio.replaceChildren(new Option("Todos los laboratorios",""));labs.forEach(([id,n])=>filtroLaboratorio.add(new Option(n,n)));filtroLaboratorio.value=old;}
 const equipoSel=document.getElementById('idEquipo');if(equipoSel){const oldEquipo=equipoSel.value;equipoSel.replaceChildren(new Option('Seleccione un equipo (opcional)',''));(d.equipos||[]).forEach(eq=>equipoSel.add(new Option(`${eq.nombre} (${eq.tipo})`,eq.idEquipo)));equipoSel.value=oldEquipo;}
 const sel=document.getElementById("idLaboratorio");if(sel){sel.replaceChildren(new Option("Seleccione",""));(d.laboratorios||[]).forEach(l=>sel.add(new Option(l.nombre,l.idLaboratorio)));}
 const frag=document.createDocumentFragment();if(!(d.tickets||[]).length){const tr=document.createElement("tr"),td=celda("No hay tickets registrados.");td.colSpan=7;tr.append(td);frag.append(tr);}else d.tickets.forEach(t=>frag.append(construirFilaTicket(t)));tablaTickets.replaceChildren(frag);aplicarFiltros();
 }catch(e){console.error(e);mostrarMensajeTabla(e.message);}}
function abrirRevision(t){ticketRevision=t;document.getElementById("revisionTitulo").textContent=t.titulo;document.getElementById("revisionDescripcion").textContent=t.descripcion;document.getElementById("revisionLaboratorio").textContent=t.laboratorio;document.getElementById("revisionPrioridad").value="";document.getElementById("dialogRevisionTicket").showModal();}
async function gestionarTicket(accion,extra={}){if(!ticketRevision)return;await api("PUT",{idTicket:Number(ticketRevision.idTicket),accion,...extra});document.getElementById("dialogRevisionTicket").close();ticketRevision=null;await cargarTickets();}
btnNuevoTicket?.addEventListener("click",()=>{if(esDocente)modalTicket.hidden=false;});
cerrarModal?.addEventListener("click",()=>modalTicket.hidden=true);
document.getElementById("cerrarRevisionTicket")?.addEventListener("click",()=>{document.getElementById("dialogRevisionTicket").close();ticketRevision=null;});
document.getElementById("cerrarDetalleTicket")?.addEventListener("click",()=>dialogTicket?.close());
document.getElementById("btnAceptarTicket")?.addEventListener("click",async()=>{const prioridad=document.getElementById("revisionPrioridad").value;if(!prioridad){alert("Selecciona una prioridad.");return;}try{await gestionarTicket("aceptar",{prioridad});}catch(e){alert(e.message);}});
document.getElementById("btnDenegarTicket")?.addEventListener("click",async()=>{const prioridad=document.getElementById("revisionPrioridad").value;if(!prioridad){alert("Selecciona una prioridad.");return;}if(!confirm("¿Deseas denegar este ticket?"))return;try{await gestionarTicket("denegar",{prioridad});}catch(e){alert(e.message);}});
filtroEstado?.addEventListener("change",aplicarFiltros);filtroLaboratorio?.addEventListener("change",aplicarFiltros);filtroEquipo?.addEventListener("input",aplicarFiltros);
tablaTickets?.addEventListener("click",async e=>{const b=e.target.closest("button");if(!b)return;const t=ticketsPorId.get(String(b.dataset.idTicket));if(!t)return;if(b.classList.contains("btn-ver")){detalle(t);return;}if(b.classList.contains("btn-revisar")){abrirRevision(t);return;}let accion=null;if(b.classList.contains("btn-completar"))accion="completar";if(b.classList.contains("btn-cancelar"))accion="cancelar";if(!accion)return;if(accion==="cancelar"&&!confirm("¿Deseas cancelar este ticket?"))return;try{await api("PUT",{idTicket:Number(t.idTicket),accion});await cargarTickets();}catch(err){alert(err.message);}});
formularioTicket?.addEventListener("submit",async e=>{e.preventDefault();if(!esDocente)return;const datos=Object.fromEntries(new FormData(formularioTicket));delete datos.prioridad;delete datos.fechaLimite;try{await api("POST",datos);formularioTicket.reset();modalTicket.hidden=true;await cargarTickets();}catch(err){alert(err.message);}});
cargarTickets();

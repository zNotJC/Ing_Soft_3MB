(() => {
  const CLAVE_TEMA = 'jojolos-tema';
  const CLAVE_IDIOMA = 'jojolos-idioma';
  const originales = new WeakMap();
  const originalesAtributos = new WeakMap();
  const tituloOriginal = document.title;
  const traducciones = {
    'Sistema de Gestión de Recursos y Soporte de Informática': 'IT Resources and Support Management System',
    'JOJOLOS - Sistema de Gestión de Recursos y Soporte de Informática': 'JOJOLOS - IT Resources and Support Management System',
    'Inicio': 'Home', 'Gestionar usuarios': 'Manage users', 'Inventario y laboratorios': 'Inventory and laboratories',
    'Tickets': 'Tickets', 'Solicitudes': 'Requests', 'Préstamos': 'Loans', 'Historial': 'History', 'Cerrar sesión': 'Log out',
    'Inicio de Sesión': 'Login', 'Cédula': 'National ID', 'Contraseña': 'Password', 'Ingresar': 'Sign in',
    'Gestión de Tickets': 'Ticket management', 'Gestión de Solicitudes': 'Request management', 'Gestión de Préstamos': 'Loan management',
    'Nuevo Ticket': 'New ticket', 'Nueva Solicitud': 'New request', 'Nuevo Préstamo': 'New loan', 'Guardar Ticket': 'Save ticket',
    'Guardar Préstamo': 'Save loan', 'Registrar Solicitud': 'Submit request', 'Panel Principal': 'Dashboard',
    '¡Bienvenido/a a JOJOLOS!': 'Welcome to JOJOLOS!', 'Bienvenido/a a JOJOLOS!': 'Welcome to JOJOLOS!',
    'Gestionar Tickets': 'Manage tickets', 'Consultar Tickets': 'View tickets', 'Gestionar Solicitudes': 'Manage requests',
    'Consultar Solicitudes': 'View requests', 'Mis Solicitudes': 'My requests', 'Gestionar Préstamos': 'Manage loans',
    'Ver Historial': 'View history', 'Tickets Pendientes': 'Pending tickets', 'Tickets Resueltos': 'Resolved tickets',
    'Solicitudes Activas': 'Active requests', 'Préstamos Activos': 'Active loans', 'Equipos Registrados': 'Registered equipment',
    'Últimos Accesos al Sistema': 'Latest system access', 'Usuario': 'User', 'Rol': 'Role', 'Fecha y Hora': 'Date and time',
    'Cargando...': 'Loading...', 'Claro / Oscuro': 'Light / Dark', 'Oscuro / Claro': 'Dark / Light',
    'Sistema de Gestión de Laboratorios': 'IT Resources and Support Management System',
    'Consulta las operaciones registradas en el sistema.': 'View the operations recorded in the system.',
    'Todos los tipos': 'All types', 'Todos los estados': 'All statuses', 'Todos los usuarios': 'All users',
    'Tipo': 'Type', 'Detalle': 'Details', 'Estado': 'Status', 'Fecha': 'Date', 'Título': 'Title', 'Fecha Necesaria': 'Needed date', 'ID': 'ID', 'ID:': 'ID:',
    'Préstamo': 'Loan', 'Solicitud': 'Request', 'Cancelado': 'Cancelled', 'Cancelada': 'Cancelled',
    'En espera': 'Waiting', 'Pendiente': 'Pending', 'Aprobada': 'Approved', 'Rechazada': 'Rejected',
    'Resuelto': 'Resolved', 'En Proceso': 'In progress', 'Vencido': 'Overdue', 'Denegado': 'Denied', 'Activo': 'Active', 'En uso': 'In use', 'Media': 'Medium',
    'Baja': 'Low', 'Alta': 'High', 'Docente': 'Teacher', 'Técnico': 'Technician', 'Administrador': 'Administrator',
    'Registro y seguimiento de incidencias detectadas en los laboratorios informáticos.': 'Record and track incidents reported in the computer labs.',
    'Todos los laboratorios': 'All laboratories', 'Título de la incidencia': 'Incident title',
    'Laboratorio/Taller': 'Laboratory/Workshop', 'Prioridad': 'Priority', 'Acciones': 'Actions', 'Ver': 'View',
    'Detalle del Ticket': 'Ticket details', 'Título:': 'Title:', 'Descripción:': 'Description:',
    'Laboratorio/Taller:': 'Laboratory/Workshop:', 'Solicitante:': 'Requester:', 'Prioridad:': 'Priority:',
    'Estado:': 'Status:', 'Fecha de registro:': 'Created on:', 'Fecha límite:': 'Due date:', 'Fecha de atención:': 'Accepted on:', 'Cerrar': 'Close',
    'Revisar ticket': 'Review ticket', 'Aceptar ticket': 'Accept ticket', 'Esperar': 'Leave pending', 'Denegar': 'Deny',
    'Registrar Ticket': 'Create ticket', 'Laboratorio / Taller': 'Laboratory / Workshop', 'Seleccione': 'Select',
    'Seleccione una opción': 'Select an option', 'Cargando laboratorios...': 'Loading laboratories...',
    'La cédula o la contraseña son incorrectas.': 'The ID number or password is incorrect.',
    'El usuario se encuentra inactivo.': 'This user account is inactive.',
    'El usuario no tiene un rol asignado.': 'This user has no role assigned.', 'Acceso no válido.': 'Invalid access.',
    'Inicia sesión para continuar.': 'Log in to continue.',
    'No se pudo completar el inicio de sesión. Verifica la conexión y la actualización de la base de datos.': 'Login failed. Check the database connection and schema version.',
    'Equipo afectado': 'Affected equipment', 'Seleccione un equipo': 'Select equipment',
    'Descripción de la incidencia': 'Incident description', 'Guardar ticket': 'Save ticket', 'Guardar Ticket': 'Save ticket',
    'Consulta y administra las solicitudes registradas.': 'View and manage recorded requests.',
    'No hay tickets registrados.': 'There are no tickets to show.', 'No hay solicitudes registradas.': 'There are no requests to show.',
    'No hay registros en el historial.': 'There are no history records to show.', 'Cargando tickets...': 'Loading tickets...',
    'Cargando solicitudes...': 'Loading requests...', 'Indica el motivo del rechazo.': 'Enter the reason for rejection.',
    'Tipo de Solicitud': 'Request type', 'Laboratorio': 'Laboratory', 'Reparación': 'Repair',
    'Seleccione un laboratorio': 'Select a laboratory', 'Seleccione un tipo': 'Select a type',
    'Tipo de equipo': 'Equipment type', 'Equipo a reparar': 'Equipment to repair', 'Equipo': 'Equipment',
    'Equipo disponible': 'Available equipment', 'Equipo/Recurso': 'Equipment/Resource', 'Laboratorio disponible': 'Available laboratory',
    'Laboratorio:': 'Laboratory:', 'Disponible': 'Available', 'En uso': 'In use',
    'Descripción': 'Description', 'Fecha Necesaria': 'Needed date', 'Guardar Solicitud': 'Save request',
    'Modificar Solicitud': 'Edit request', 'Guardar Cambios': 'Save changes', 'Todos los estados': 'All statuses',
    'Día solicitado': 'Requested day', 'Día solicitado para el laboratorio': 'Requested laboratory day', 'Día solicitado (opcional)': 'Requested day (optional)',
    'Revisar préstamo': 'Review loan', 'Guardar prioridad': 'Save priority',
    'Ordenadores': 'Computers', 'Periféricos': 'Peripherals', 'Televisores': 'Televisions', 'Aires Acondicionados': 'Air conditioners',
    'Tipo de dispositivo': 'Device type', 'Fecha de Préstamo': 'Loan date', 'Fecha de Devolución': 'Return date',
    'Denegado': 'Denied', 'Aceptar': 'Accept', 'Cancelar': 'Cancel', 'Registrar Préstamo': 'Create loan',
    'Administración de usuarios': 'User administration', 'Correo': 'Email', 'Crear usuario': 'Create user',
    'Cancelar edición': 'Cancel editing', 'Mínimo 8 caracteres': 'At least 8 characters',
    'Al modificar, deja el campo vacío para conservar la contraseña actual.': 'Leave this blank when editing to keep the current password.',
    'Mostrar contraseña': 'Show password', 'Ocultar contraseña': 'Hide password', 'Abrir menú': 'Open menu',
    'Gestiona laboratorios y equipos del inventario.': 'Manage laboratories and inventory equipment.',
    'Consulta los laboratorios y equipos registrados.': 'View registered laboratories and equipment.',
    'Registrar o modificar laboratorio': 'Add or edit laboratory', 'Nombre del laboratorio': 'Laboratory name',
    'Registrar laboratorio': 'Add laboratory', 'Laboratorios': 'Laboratories', 'Equipos': 'Equipment',
    'Registrar o modificar equipo': 'Add or edit equipment', 'Nombre del equipo': 'Equipment name',
    'Tipo de inventario': 'Inventory type', 'Tipo de equipo': 'Equipment type', 'Registrar equipo': 'Add equipment',
    'Cancelar edición': 'Cancel editing', 'Roto': 'Broken', 'Sin laboratorio asignado': 'No laboratory assigned',
    'No hay laboratorios registrados.': 'No laboratories are registered.', 'No hay equipos registrados.': 'No equipment is registered.',
    'PC-1, TV-3, Mouse-1': 'PC-1, TV-3, Mouse-1', 'Guardar cambios': 'Save changes',
    'Puedes solicitar un préstamo seleccionando el tipo de dispositivo y uno de los equipos disponibles.': 'Request a loan by choosing a device type and an available item.',
    'Gestiona los préstamos, sus estados y las devoluciones.': 'Manage loans, their status, and returns.',
    'Consulta los préstamos registrados.': 'View recorded loans.', 'Modificar Préstamo': 'Edit loan',
    'Todos los equipos': 'All equipment', 'Todas las cédulas': 'All national IDs',
    'Fecha Préstamo': 'Loan date', 'Fecha Devolución': 'Return date', 'Nombre': 'Name', 'Cédula': 'National ID',
    'Revisar préstamo': 'Review loan', 'Registrar devolución': 'Record return', 'No hay préstamos registrados.': 'There are no loans to show.',
    'Registrar Solicitud': 'Create request', 'Solicitud registrada correctamente.': 'Request submitted successfully.',
    'Modificar': 'Edit', 'Cancelar': 'Cancel', 'Aprobar': 'Approve', 'Rechazar': 'Reject',
    'Aprobar Solicitud': 'Approve request', 'Rechazar Solicitud': 'Reject request',
    'Observación para el solicitante (opcional).': 'Note for the requester (optional).',
    'Indique el motivo del rechazo.': 'Enter the reason for rejection.', 'Responder Solicitud': 'Respond to request',
    'Respuesta': 'Response', 'Guardar Respuesta': 'Save response', 'Indica el motivo del rechazo.': 'Enter the reason for rejection.',
    'Indique el motivo del rechazo.': 'Enter the reason for rejection.', 'Vacía para conservar la contraseña actual': 'Leave blank to keep the current password',
    'Usuario actualizado correctamente.': 'User updated successfully.', 'Usuario creado correctamente.': 'User created successfully.',
    'Usuario desactivado.': 'User deactivated.', 'Usuario eliminado.': 'User deleted.', 'Usuario activado.': 'User activated.',
    'Por seguridad no se puede recuperar la contraseña actual. Escribe una nueva o deja el campo vacío para conservarla.': 'For security, the current password cannot be recovered. Enter a new one or leave this blank to keep it.',
    'No se pudieron cargar las solicitudes.': 'Requests could not be loaded.', 'No se pudo completar la operación.': 'The operation could not be completed.',
    'Usuario actualizado correctamente.': 'User updated successfully.', 'Usuario creado correctamente.': 'User created successfully.',
    'Usuario desactivado.': 'User deactivated.', 'Usuario eliminado.': 'User deleted.', 'Usuario activado.': 'User activated.',
    'No se pudo completar la operación de usuarios.': 'The user operation could not be completed.',
    'No se pudo completar la operación de inventario.': 'The inventory operation could not be completed.',
    'No se pudo completar la operación de préstamos. Comprueba las columnas de prestamo, equipo y usuario.': 'The loan operation could not be completed. Check the loan, equipment, and user columns.',
    'No se pudo completar la operación de solicitudes.': 'The request operation could not be completed.',
    'No se pudo completar la operación de tickets. Comprueba las columnas de ticket, laboratorio y usuario.': 'The ticket operation could not be completed. Check the ticket, laboratory, and user columns.',
    'No se pudo cargar el historial. Comprueba el esquema de las tablas ticket, solicitud y prestamo.': 'History could not be loaded. Check the ticket, request, and loan table schemas.',
    'No se pudo cargar el dashboard.': 'The dashboard could not be loaded.',
    'La sesión no está iniciada.': 'No active session.', 'Debes iniciar sesión.': 'You must sign in.',
    'No tienes permisos para realizar esta operación.': 'You are not allowed to perform this operation.',
    'Solo el Administrador puede modificar el inventario.': 'Only the Administrator can modify inventory.',
    'Selecciona el estado del laboratorio.': 'Select a laboratory status.', 'Laboratorio registrado.': 'Laboratory added.',
    'Laboratorio actualizado.': 'Laboratory updated.', 'Laboratorio eliminado.': 'Laboratory deleted.',
    'No se encontró el laboratorio o no hubo cambios.': 'The laboratory was not found or no changes were made.',
    'No se puede eliminar un laboratorio que contiene equipos o tickets.': 'A laboratory with equipment or tickets cannot be deleted.',
    'Tipo de inventario no válido.': 'Invalid inventory type.', 'Selecciona un tipo y estado válidos.': 'Select a valid type and status.',
    'Equipo registrado.': 'Equipment added.', 'Equipo actualizado.': 'Equipment updated.', 'Equipo eliminado.': 'Equipment deleted.',
    'No se encontró el equipo o no hubo cambios.': 'The equipment was not found or no changes were made.',
    'No se puede eliminar un equipo con préstamos asociados.': 'Equipment with associated loans cannot be deleted.',
    'No se pudo guardar o consultar el inventario. Comprueba la base de datos, los laboratorios existentes y los registros duplicados.': 'Inventory could not be saved or loaded. Check the database, existing laboratories, and duplicate records.',
    'Inventario consultado correctamente.': 'Inventory loaded successfully.', 'Dashboard consultado correctamente.': 'Dashboard loaded successfully.',
    'Historial consultado correctamente.': 'History loaded successfully.', 'Préstamos consultados correctamente.': 'Loans loaded successfully.',
    'Solicitudes consultadas correctamente.': 'Requests loaded successfully.', 'Tickets consultados correctamente.': 'Tickets loaded successfully.',
    'El Administrador gestiona los préstamos existentes, pero no los registra.': 'The Administrator can manage existing loans but cannot create them.',
    'El Administrador solo consulta préstamos y el Docente solo crea los propios.': 'The Administrator can only view loans, and Teachers can only create their own.',
    'El Administrador no gestiona solicitudes.': 'The Administrator cannot manage requests.',
    'Solo el Docente puede registrar solicitudes.': 'Only Teachers can submit requests.',
    'Solo el Técnico puede gestionar incidencias.': 'Only Technicians can manage tickets.',
    'El Administrador solo puede consultar tickets.': 'The Administrator can only view tickets.',
    'Acción no válida.': 'Invalid action.', 'Estado de solicitud no válido.': 'Invalid request status.',
    'Los datos del préstamo no son válidos.': 'The loan details are invalid.',
    'Los datos de la solicitud no son válidos.': 'The request details are invalid.',
    'Los datos del ticket no son válidos.': 'The ticket details are invalid.',
    'Los datos del usuario no son válidos.': 'The user details are invalid.',
    'Los datos no son válidos. La contraseña debe tener al menos 8 caracteres.': 'Invalid details. The password must be at least 8 characters long.',
    'Cédula no válida.': 'Invalid national ID.', 'Selecciona una prioridad válida.': 'Select a valid priority.',
    'Selecciona un tipo y estado válidos.': 'Select a valid type and status.',
    'El ticket no existe o ya está cerrado.': 'The ticket does not exist or is already closed.',
    'El préstamo no existe o no admite esa operación.': 'The loan does not exist or cannot be changed this way.',
    'La solicitud no existe o no admite esa modificación.': 'The request does not exist or cannot be changed this way.',
    'La solicitud no existe o no se puede cancelar.': 'The request does not exist or cannot be cancelled.',
    'Préstamo solicitado correctamente.': 'Loan request submitted successfully.',
    'Préstamo actualizado correctamente.': 'Loan updated successfully.',
    'Préstamo cancelado correctamente.': 'Loan cancelled successfully.',
    'Solicitud actualizada correctamente.': 'Request updated successfully.', 'Solicitud cancelada correctamente.': 'Request cancelled successfully.',
    'Ticket registrado correctamente.': 'Ticket submitted successfully.', 'Ticket actualizado correctamente.': 'Ticket updated successfully.',
    'Ticket cancelado correctamente.': 'Ticket cancelled successfully.',
    'No se pudo guardar el usuario. Verifica que la cédula, el usuario y el correo no estén repetidos.': 'The user could not be saved. Make sure the ID, username, and email are not already in use.',
    'No puedes cambiar tu propio rol durante la sesión actual.': 'You cannot change your own role during this session.',
    'No puedes cambiar tu propia cédula durante la sesión actual.': 'You cannot change your own ID during this session.',
    'No puedes eliminar ni desactivar tu propia cuenta.': 'You cannot delete or deactivate your own account.',
    'No se encontró el usuario o no se realizaron cambios.': 'The user was not found or no changes were made.',
    'No se encontró una cuenta inactiva con esa cédula.': 'No inactive account was found with that ID.',
    'No se encontró una cuenta activa con esa cédula.': 'No active account was found with that ID.',
    'No se puede eliminar este usuario porque no existe o tiene solicitudes, tickets o préstamos asociados. Puedes desactivarlo.': 'This user cannot be deleted because the account does not exist or has linked requests, tickets, or loans. You can deactivate it.',
    'Usuario activado correctamente.': 'User activated successfully.', 'Usuario desactivado correctamente.': 'User deactivated successfully.',
    '¿Deseas cancelar esta solicitud?': 'Do you want to cancel this request?',
    '¿Deseas cancelar este ticket?': 'Do you want to cancel this ticket?', '¿Deseas denegar este ticket?': 'Do you want to deny this ticket?',
    '¿Deseas cancelar este préstamo?': 'Do you want to cancel this loan?',
    '¿Deseas cancelar esta solicitud?': 'Do you want to cancel this request?',
    '¿Deseas cancelar este ticket?': 'Do you want to cancel this ticket?', '¿Deseas denegar este ticket?': 'Do you want to deny this ticket?',
    'Revisar ticket': 'Review ticket', 'Registrar Completado': 'Mark complete', 'Cancelar': 'Cancel',
    'Aceptar ticket': 'Accept ticket', 'Espera': 'Wait', 'No hay tickets registrados.': 'There are no tickets to show.',
    'Gestiona los usuarios registrados.': 'Manage registered users.', 'Actualizar usuario': 'Update user',
    'Cargando usuarios...': 'Loading users...', 'No hay usuarios registrados.': 'There are no users to show.',
    'Cargando laboratorios...': 'Loading laboratories...', 'Cargando equipos...': 'Loading equipment...',
    'Todos los estados': 'All statuses', 'Todos los tipos': 'All types', 'Todos los usuarios': 'All users',
    'Filtrar por tipo de solicitud': 'Filter by request type', 'Equipo o recurso': 'Equipment or resource',
    'Título de la incidencia': 'Incident title', 'Todas las cédulas': 'All national IDs',
    'El equipo no existe o no está disponible.': 'The equipment does not exist or is unavailable.',
    'Los datos del préstamo no son válidos.': 'The loan details are invalid.',
    'La fecha de devolución no puede ser anterior a la fecha de préstamo.': 'The return date cannot be earlier than the loan date.',
    'Selecciona una prioridad.': 'Select a priority.', 'Indica una prioridad válida.': 'Select a valid priority.',
    'Los datos de la solicitud no son válidos.': 'The request details are invalid.',
    'Indica un día válido para solicitar el laboratorio.': 'Select a valid date for the laboratory request.',
    'Selecciona un laboratorio disponible.': 'Select an available laboratory.',
    'El laboratorio seleccionado no está disponible.': 'The selected laboratory is unavailable.',
    'Selecciona un equipo registrado en el laboratorio elegido.': 'Select equipment registered in the chosen laboratory.',
    'Título': 'Title', 'Detalles': 'Details', 'Todas las fechas': 'All dates'
    , 'JOJOLOS - Historial': 'JOJOLOS - History', 'JOJOLOS - Gestión de Tickets': 'JOJOLOS - Ticket management'
    , 'JOJOLOS - Gestión de Solicitudes': 'JOJOLOS - Request management', 'JOJOLOS - Gestión de Préstamos': 'JOJOLOS - Loan management'
    , 'JOJOLOS - Panel Principal': 'JOJOLOS - Dashboard', 'JOJOLOS - Inicio de Sesión': 'JOJOLOS - Login'
    , 'JOJOLOS - Inventario y laboratorios': 'JOJOLOS - Inventory and laboratories', 'JOJOLOS - Usuarios': 'JOJOLOS - Users'
    , 'Tipo de equipo (opcional)': 'Equipment type (optional)', 'Cambiar idioma': 'Change language', 'Cambiar tema': 'Change theme'
    , 'Abrir menú': 'Open menu', 'Preferencias de accesibilidad': 'Accessibility preferences',
    'Usuario:': 'User:', 'Rol:': 'Role:', 'Manage tickets': 'Manage tickets', 'My requests': 'My requests', 'View history': 'View history',
    'JOJOLOS es una plataforma diseñada para centralizar la gestión de incidencias, solicitudes y préstamos de equipos dentro de los laboratorios informáticos. El sistema permite registrar información, realizar seguimientos y consultar estadísticas para mejorar la organización de los recursos tecnológicos.':
      'JOJOLOS is a platform designed to centralize the management of incidents, requests, and equipment loans in computer labs. The system lets users record information, track progress, and review statistics to improve the organization of technology resources.',
    'Equipo: ': 'Equipment: ', 'Equipo:': 'Equipment:'
  };

  function aplicarTema(tema) {
    document.body.classList.toggle('tema-oscuro', tema === 'oscuro');
    document.documentElement.dataset.tema = tema;
    const boton = document.getElementById('alternarTema');
    if (boton) boton.textContent = tema === 'oscuro' ? 'Oscuro / Claro' : 'Claro / Oscuro';
  }

  function textoTraducido(texto, idioma) {
    if (idioma !== 'en') return texto;
    const limpio = texto.trim();
    const normalizado = limpio.replace(/\s+/g, ' ');
    const inicio = texto.match(/^\s*/)?.[0] || '';
    const fin = texto.match(/\s*$/)?.[0] || '';
    if (Object.prototype.hasOwnProperty.call(traducciones, normalizado)) return inicio + traducciones[normalizado] + fin;
    if (normalizado.startsWith('Equipo: ')) return inicio + normalizado.replace(/^Equipo: /, 'Equipment: ') + fin;
    const dynamicTranslations = [
      [/^¿Eliminar el laboratorio (.+)\?$/, 'Delete the laboratory $1?'],
      [/^¿Eliminar el equipo (.+)\?$/, 'Delete the equipment $1?'],
      [/^¿Desactivar la cuenta (.+)\?$/, 'Deactivate the $1 account?'],
      [/^¿Activar la cuenta (.+)\?$/, 'Activate the $1 account?'],
      [/^¿Eliminar definitivamente la cuenta (.+)\? Si tiene tickets, solicitudes o préstamos asociados, la operación será rechazada\.$/, 'Permanently delete the $1 account? If it has linked tickets, requests, or loans, the operation will be rejected.']
    ];
    for (const [patron, reemplazo] of dynamicTranslations) {
      if (patron.test(normalizado)) return inicio + normalizado.replace(patron, reemplazo) + fin;
    }
    return texto;
  }

  function traducirNodo(nodo, idioma) {
    if (!originales.has(nodo)) originales.set(nodo, nodo.textContent);
    const original = originales.get(nodo);
    const traducido = textoTraducido(original, idioma);
    if (nodo.textContent !== traducido) nodo.textContent = traducido;
  }

  function aplicarIdioma(idioma, raiz = document.body) {
    if (!raiz) return;
    document.documentElement.lang = idioma;
    document.title = textoTraducido(tituloOriginal, idioma);
    const nodos = [];
    const caminante = document.createTreeWalker(raiz, NodeFilter.SHOW_TEXT);
    while (caminante.nextNode()) {
      const nodo = caminante.currentNode;
      if (nodo.textContent.trim() && !nodo.parentElement?.closest('#controlesPreferencias, #preferenciasLogin')) nodos.push(nodo);
    }
    nodos.forEach(nodo => traducirNodo(nodo, idioma));
    raiz.querySelectorAll?.('[data-i18n-es][data-i18n-en]').forEach(el => {
      const valor = idioma === 'en' ? el.dataset.i18nEn : el.dataset.i18nEs;
      if (el.textContent !== valor) el.textContent = valor;
    });
    raiz.querySelectorAll?.('[placeholder],[aria-label],[title]').forEach(el => {
      ['placeholder', 'aria-label', 'title'].forEach(atributo => {
        const valor = el.getAttribute(atributo);
        if (valor) {
          let valoresOriginales = originalesAtributos.get(el);
          if (!valoresOriginales) { valoresOriginales = new Map(); originalesAtributos.set(el, valoresOriginales); }
          if (!valoresOriginales.has(atributo)) valoresOriginales.set(atributo, valor);
          let original = valoresOriginales.get(atributo);
          if (valor !== original && valor !== traducciones[original]) {
            original = valor;
            valoresOriginales.set(atributo, original);
          }
          const traducido = idioma === 'en' ? (traducciones[original] || original) : original;
          if (valor !== traducido) el.setAttribute(atributo, traducido);
        }
      });
    });
    const boton = document.getElementById('alternarIdioma');
    if (boton) boton.textContent = idioma === 'en' ? 'EN / ES' : 'ES / EN';
    document.documentElement.dataset.idioma = idioma;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const temaInicial = sessionStorage.getItem(CLAVE_TEMA) || 'claro';
    let idiomaActual = sessionStorage.getItem(CLAVE_IDIOMA) || 'es';
    aplicarTema(temaInicial);
    aplicarIdioma(idiomaActual);

    const observador = new MutationObserver(cambios => {
      cambios.forEach(cambio => {
        if (cambio.type === 'attributes') {
          aplicarIdioma(idiomaActual, cambio.target);
          return;
        }
        if (cambio.type === 'characterData') {
          traducirNodo(cambio.target, idiomaActual);
          return;
        }
        cambio.addedNodes.forEach(nodo => {
          if (nodo.nodeType === Node.TEXT_NODE) traducirNodo(nodo, idiomaActual);
          else if (nodo.nodeType === Node.ELEMENT_NODE) aplicarIdioma(idiomaActual, nodo);
        });
      });
    });
    observador.observe(document.body, { childList: true, subtree: true, characterData: true, attributes: true, attributeFilter: ['placeholder', 'aria-label', 'title'] });
    const alertOriginal = window.alert.bind(window);
    const confirmOriginal = window.confirm.bind(window);
    window.alert = mensaje => alertOriginal(textoTraducido(String(mensaje), idiomaActual));
    window.confirm = mensaje => confirmOriginal(textoTraducido(String(mensaje), idiomaActual));

    document.addEventListener('click', evento => {
      if (evento.target.closest('#alternarIdioma')) {
        idiomaActual = idiomaActual === 'es' ? 'en' : 'es';
        sessionStorage.setItem(CLAVE_IDIOMA, idiomaActual);
        aplicarIdioma(idiomaActual);
      }
      if (evento.target.closest('#alternarTema')) {
        const nuevo = (sessionStorage.getItem(CLAVE_TEMA) || 'claro') === 'claro' ? 'oscuro' : 'claro';
        sessionStorage.setItem(CLAVE_TEMA, nuevo);
        aplicarTema(nuevo);
      }
    });
  });
})();

(() => {
  const CLAVE_TEMA = 'jojolos-tema';
  const CLAVE_IDIOMA = 'jojolos-idioma';
  const originalesNodos = new WeakMap();
  const traducciones = {
    'Sistema de Gestión de Recursos y Soporte de Informática': 'IT Resources and Support Management System',
    'JOJOLOS - Sistema de Gestión de Recursos y Soporte de Informática': 'JOJOLOS - IT Resources and Support Management System',
    'Inicio': 'Home', 'Gestionar usuarios': 'Manage users',
    'Inventario y laboratorios': 'Inventory and laboratories',
    'Tickets': 'Tickets', 'Solicitudes': 'Requests', 'Préstamos': 'Loans',
    'Historial': 'History', 'Cerrar sesión': 'Log out',
    'Inicio de Sesión': 'Login', 'Cédula': 'National ID',
    'Contraseña': 'Password', 'Ingresar': 'Sign in',
    'Gestión de Tickets': 'Ticket management',
    'Gestión de Solicitudes': 'Request management',
    'Gestión de Préstamos': 'Loan management',
    'Nuevo Ticket': 'New ticket', 'Nueva Solicitud': 'New request',
    'Nuevo Préstamo': 'New loan', 'Guardar Ticket': 'Save ticket',
    'Guardar Préstamo': 'Save loan', 'Registrar Solicitud': 'Submit request',
    'Panel Principal': 'Dashboard', '¡Bienvenido/a a JOJOLOS!': 'Welcome to JOJOLOS!',
    'Gestionar Tickets': 'Manage tickets', 'Consultar Tickets': 'View tickets',
    'Gestionar Solicitudes': 'Manage requests', 'Consultar Solicitudes': 'View requests',
    'Mis Solicitudes': 'My requests', 'Gestionar Préstamos': 'Manage loans',
    'Ver Historial': 'View history', 'Tickets Pendientes': 'Pending tickets',
    'Tickets Resueltos': 'Resolved tickets', 'Solicitudes Activas': 'Active requests',
    'Préstamos Activos': 'Active loans', 'Equipos Registrados': 'Registered equipment',
    'Últimos Accesos al Sistema': 'Latest system access',
    'Usuario': 'User', 'Rol': 'Role', 'Fecha y Hora': 'Date and time',
    'Cargando...': 'Loading...', 'Claro / Oscuro': 'Light / Dark',
    'Sistema de Gestión de Laboratorios': 'IT Resources and Support Management System'
  };

  function aplicarTema(tema) {
    document.body.classList.toggle('tema-oscuro', tema === 'oscuro');
    document.documentElement.dataset.tema = tema;
    const boton = document.getElementById('alternarTema');
    if (boton) boton.textContent = tema === 'oscuro' ? 'Oscuro / Claro' : 'Claro / Oscuro';
  }

  function traducirElemento(el, idioma) {
    if (!el || el.closest('#controlesPreferencias, #preferenciasLogin')) return;
    if (el.children.length === 0) {
      if (!el.dataset.textoOriginal) el.dataset.textoOriginal = el.textContent.trim();
      const original = el.dataset.textoOriginal;
      el.textContent = idioma === 'en' ? (traducciones[original] || original) : original;
      return;
    }
    Array.from(el.childNodes).forEach(nodo => {
      if (nodo.nodeType === Node.TEXT_NODE && nodo.textContent.trim()) {
        if (!originalesNodos.has(nodo)) originalesNodos.set(nodo, nodo.textContent);
        const textoOriginal = originalesNodos.get(nodo);
        const original = textoOriginal.trim();
        if (idioma === 'es') nodo.textContent = textoOriginal;
        else if (traducciones[original]) nodo.textContent = textoOriginal.replace(original, traducciones[original]);
      }
    });
  }

  function aplicarIdioma(idioma) {
    document.documentElement.lang = idioma;
    document.querySelectorAll('a,button,label,h1,h2,h3,p,th,td,option').forEach(el => {
      if (el.id !== 'alternarIdioma' && el.id !== 'alternarTema') traducirElemento(el, idioma);
    });
    const boton = document.getElementById('alternarIdioma');
    if (boton) boton.textContent = idioma === 'en' ? 'EN / ES' : 'ES / EN';
    document.documentElement.dataset.idioma = idioma;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const temaInicial = sessionStorage.getItem(CLAVE_TEMA) || 'claro';
    const idiomaInicial = sessionStorage.getItem(CLAVE_IDIOMA) || 'es';
    aplicarTema(temaInicial);
    aplicarIdioma(idiomaInicial);

    const idioma = document.getElementById('alternarIdioma');
    if (idioma) idioma.addEventListener('click', () => {
      const nuevo = (sessionStorage.getItem(CLAVE_IDIOMA) || 'es') === 'es' ? 'en' : 'es';
      sessionStorage.setItem(CLAVE_IDIOMA, nuevo);
      aplicarIdioma(nuevo);
    });

    const tema = document.getElementById('alternarTema');
    if (tema) tema.addEventListener('click', () => {
      const nuevo = (sessionStorage.getItem(CLAVE_TEMA) || 'claro') === 'claro' ? 'oscuro' : 'claro';
      sessionStorage.setItem(CLAVE_TEMA, nuevo);
      aplicarTema(nuevo);
    });

    document.querySelectorAll('a[href*="cerrarSesion.php"],a[href*="cerrar-sesion"]').forEach(a => a.addEventListener('click', () => {
      sessionStorage.removeItem(CLAVE_TEMA);
      sessionStorage.removeItem(CLAVE_IDIOMA);
    }));
  });
})();

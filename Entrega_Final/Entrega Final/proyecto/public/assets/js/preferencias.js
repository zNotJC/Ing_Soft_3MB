(() => {
  const CLAVE_TEMA = 'jojolos-tema';
  const CLAVE_IDIOMA = 'jojolos-idioma';
  const cuerpo = document.body;
  function aplicarTema(tema) {
    cuerpo.classList.toggle('tema-oscuro', tema === 'oscuro');
    document.documentElement.dataset.tema = tema;
  }
  function aplicarIdioma(idioma) {
    document.documentElement.lang = idioma;
    const traducciones = {
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
      'Guardar Préstamo': 'Save loan', 'Registrar Solicitud': 'Submit request'
    };
    document.querySelectorAll('a,button,label,h1,h2').forEach(el => {
      if (!el.dataset.textoOriginal) el.dataset.textoOriginal = el.textContent.trim();
      const original = el.dataset.textoOriginal;
      if (idioma === 'en') el.textContent = traducciones[original] || original;
      else el.textContent = original;
    });
    document.documentElement.dataset.idioma = idioma;
  }
  function agregarControles() {
    if (document.getElementById('controlesPreferencias')) return;
    const grupo = document.createElement('div');
    grupo.id = 'controlesPreferencias';
    grupo.className = 'controles-preferencias';
    const idioma = document.createElement('button');
    idioma.type = 'button'; idioma.id = 'alternarIdioma'; idioma.className = 'btn-preferencia';
    idioma.textContent = 'ES / EN'; idioma.setAttribute('aria-label', 'Cambiar idioma');
    idioma.addEventListener('click', () => {
      const nuevo = (localStorage.getItem(CLAVE_IDIOMA) || 'es') === 'es' ? 'en' : 'es';
      localStorage.setItem(CLAVE_IDIOMA, nuevo); aplicarIdioma(nuevo);
    });
    const tema = document.createElement('button');
    tema.type = 'button'; tema.id = 'alternarTema'; tema.className = 'btn-preferencia';
    tema.textContent = 'Claro / Oscuro'; tema.setAttribute('aria-label', 'Cambiar tema');
    tema.addEventListener('click', () => {
      const nuevo = (localStorage.getItem(CLAVE_TEMA) || 'claro') === 'claro' ? 'oscuro' : 'claro';
      localStorage.setItem(CLAVE_TEMA, nuevo); aplicarTema(nuevo);
    });
    grupo.append(idioma, tema); document.body.appendChild(grupo);
  }
  document.addEventListener('DOMContentLoaded', () => {
    aplicarTema(localStorage.getItem(CLAVE_TEMA) || 'claro');
    aplicarIdioma(localStorage.getItem(CLAVE_IDIOMA) || 'es');
    agregarControles();
    document.querySelectorAll('a[href*="cerrarSesion.php"],a[href*="cerrar-sesion"]').forEach(a => a.addEventListener('click', () => {
      localStorage.removeItem(CLAVE_TEMA); localStorage.removeItem(CLAVE_IDIOMA);
    }));
  });
})();

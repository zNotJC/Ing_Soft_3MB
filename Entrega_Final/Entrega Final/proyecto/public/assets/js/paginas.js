(() => {
  const pageScript = document.currentScript?.dataset.pageScript || '';

  async function iniciarPagina() {
    try {
      const respuesta = await fetch('../index.php?api=session', { headers: { Accept: 'application/json' }, cache: 'no-store' });
      const resultado = await respuesta.json();
      if (!respuesta.ok || resultado.status !== 'success' || !resultado.data?.rol) throw new Error('Sesión no válida');
      const { rol, usuario } = resultado.data;
      document.body.dataset.rol = rol;
      document.body.dataset.esDocente = String(rol === 'Docente');
      document.body.dataset.esTecnico = String(rol === 'Tecnico');
      document.body.dataset.esAdministrador = String(rol === 'Administrador');
      document.querySelectorAll('[data-roles]').forEach(elemento => {
        const roles = elemento.dataset.roles.split(',').map(valor => valor.trim());
        elemento.hidden = !roles.includes(rol);
      });
      document.querySelectorAll('[data-session-user]').forEach(elemento => { elemento.textContent = usuario; });
      document.querySelectorAll('[data-session-role]').forEach(elemento => { elemento.textContent = rol === 'Tecnico' ? 'Técnico' : rol; });

      const cabecera = document.getElementById('appNavbar');
      if (cabecera) {
        const parcial = await fetch('../partes/navbar.html', { cache: 'no-store' });
        if (!parcial.ok) throw new Error('No se pudo cargar la navegación.');
        cabecera.outerHTML = await parcial.text();
        document.querySelectorAll('[data-roles]').forEach(elemento => {
          const roles = elemento.dataset.roles.split(',').map(valor => valor.trim());
          elemento.hidden = !roles.includes(rol);
        });
        const rutaActual = location.pathname.split('/').pop();
        document.querySelectorAll('.navbar-nav a').forEach(enlace => {
          if (enlace.getAttribute('href') === rutaActual) enlace.classList.add('active');
        });
      }

      document.body.classList.remove('auth-pendiente');
      if (pageScript) {
        const script = document.createElement('script');
        script.src = `../assets/js/${pageScript}`;
        script.onerror = () => console.error(`No se pudo cargar ${pageScript}.`);
        document.body.append(script);
      }
    } catch (error) {
      location.replace('../index.html?error=permisos');
    }
  }

  iniciarPagina();
})();

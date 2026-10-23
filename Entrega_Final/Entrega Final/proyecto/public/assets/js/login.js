(() => {
  const mensajes = {
    credenciales: 'La cédula o la contraseña son incorrectas.',
    inactivo: 'El usuario se encuentra inactivo.',
    sinroles: 'El usuario no tiene un rol asignado.',
    acceso: 'Acceso no válido.',
    permisos: 'Inicia sesión para continuar.',
    bd: 'No se pudo completar el inicio de sesión. Verifica la conexión y la actualización de la base de datos.',
    esquema: 'No se pudo completar el inicio de sesión. Verifica la conexión y la actualización de la base de datos.'
  };
  const error = new URLSearchParams(location.search).get('error');
  const alerta = document.getElementById('loginError');
  if (error && mensajes[error] && alerta) {
    alerta.textContent = mensajes[error];
    alerta.hidden = false;
  }
})();

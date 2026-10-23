document.addEventListener("click", evento => {
    const boton = evento.target.closest("[data-password-target]");
    if (!boton) return;
    const campo = document.getElementById(boton.dataset.passwordTarget);
    if (!campo) return;
    const visible = campo.type === "password";
    campo.type = visible ? "text" : "password";
    boton.setAttribute("aria-pressed", String(visible));
    boton.setAttribute("aria-label", visible ? "Ocultar contraseña" : "Mostrar contraseña");
    boton.textContent = visible ? "◎" : "◉";
});

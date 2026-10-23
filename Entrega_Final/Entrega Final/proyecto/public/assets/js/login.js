const formulario = document.getElementById("loginForm");

formulario.addEventListener("submit", function (e) {

    e.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const contrasena = document.getElementById("contrasena").value;

    const usuarios =
        JSON.parse(localStorage.getItem("usuarios")) || [];

    const usuarioEncontrado = usuarios.find(
        (u) =>
            u.usuario === usuario &&
            u.contrasena === contrasena &&
            u.activo !== false
    );

    if (!usuarioEncontrado) {

        alert(
            "Usuario o contraseña incorrectos. Verifique los datos e intente nuevamente."
        );

        return;
    }

    console.log("LOGIN EJECUTADO");

    const fechaAcceso =
        new Date().toLocaleString();

        localStorage.setItem(
            "usuarioActivo",
            JSON.stringify({
                usuario: usuarioEncontrado.usuario,
                rol: usuarioEncontrado.rol,
                fechaAcceso: fechaAcceso
            })
        );

        let historialAccesos =
        JSON.parse(
            localStorage.getItem("historialAccesos")
        ) || [];
        
        console.log(
            "Antes de agregar:",
            historialAccesos.length
        );

        historialAccesos.push({

            usuario: usuarioEncontrado.usuario,

            rol: usuarioEncontrado.rol,

            fechaAcceso: fechaAcceso

        });

        console.log(
            "Después de agregar:",
            historialAccesos.length
        );

        localStorage.setItem(
            "historialAccesos",
            JSON.stringify(historialAccesos)
        );

        window.location.href =
        "dashboard.html";

});
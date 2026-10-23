const formulario = document.getElementById("registroForm");

if (formulario) {

    formulario.addEventListener("submit", function (e) {

        const contrasena =
            document.getElementById("contrasena").value;

        const confirmarContrasena =
            document.getElementById("confirmarContrasena").value;

        if (contrasena !== confirmarContrasena) {

            e.preventDefault();

            alert("Las contraseñas no coinciden.");

            return;
        }

    });


    const botonesMostrar =
        document.querySelectorAll(".mostrar-contrasena");

    botonesMostrar.forEach(function (boton) {

        boton.addEventListener("click", function () {

            const idCampo =
                boton.getAttribute("data-target");

            const campo =
                document.getElementById(idCampo);

            if (campo.type === "password") {

                campo.type = "text";

                boton.textContent = "🙈";

                boton.setAttribute(
                    "aria-label",
                    "Ocultar contraseña"
                );

            } else {

                campo.type = "password";

                boton.textContent = "👁";

                boton.setAttribute(
                    "aria-label",
                    "Mostrar contraseña"
                );

            }

        });

    });

}
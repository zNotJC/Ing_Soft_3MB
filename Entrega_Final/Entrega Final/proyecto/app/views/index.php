<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>JOJOLOS - Inicio de Sesión</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/index.css">

</head>

<body>

    <header>

        <h1>JOJOLOS</h1>

        <p>Sistema de Gestión de Laboratorios</p>

    </header>

    <main>

        <section>

            <h2>Inicio de Sesión</h2>

            <?php

            $mensaje = "";

            if (isset($_GET["error"])) {

                switch ($_GET["error"]) {

                    case "credenciales":
                        $mensaje = "La cédula o la contraseña son incorrectas.";
                        break;

                    case "inactivo":
                        $mensaje = "El usuario se encuentra inactivo.";
                        break;

                    case "sinroles":
                        $mensaje = "El usuario no tiene un rol asignado.";
                        break;

                    case "acceso":
                        $mensaje = "Acceso no válido.";
                        break;

                    case "bd":
                    case "esquema":
                        $mensaje = "No se pudo completar el inicio de sesión. Verifica la conexión y la actualización de la base de datos.";
                        break;

                }

            }

            ?>

            <?php if ($mensaje !== "") : ?>

                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>

            <form
                action="login.php"
                method="POST">

                <label for="cedula">Cédula</label>

                <input
                    type="text"
                    id="cedula"
                    name="cedula"
                    required
                    minlength="8"
                    maxlength="8"
                    pattern="[0-9]{8}">

                <label for="contrasena">Contraseña</label>

                <input
                    type="password"
                    id="contrasena"
                    name="contrasena"
                    required
                    minlength="6"
                    maxlength="30">

                <button type="submit">
                    Ingresar
                </button>

            </form>



        </section>

    </main>

    <footer>

        <p>
            JOJOLOS - Gestión de Incidencias, Solicitudes y Préstamos
        </p>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
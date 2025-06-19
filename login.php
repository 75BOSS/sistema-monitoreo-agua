<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/db.php'; // Cambio aquí

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $clave = hash('sha256', $_POST['clave']);

    $stmt = $conexion->prepare("SELECT id, nombre, rol FROM usuarios WHERE correo = ? AND contraseña = ?");
    $stmt->bind_param("ss", $correo, $clave);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nombre, $rol);
        $stmt->fetch();
        $_SESSION['id'] = $id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['rol'] = $rol;

        switch ($rol) {
            case 'administrador':
                header("Location: admin/dashboard.php");
                break;
            case 'tecnico':
                header("Location: tecnico/dashboard.php");
                break;
            case 'usuario':
                header("Location: usuario/dashboard.php");
                break;
            default:
                session_destroy();
                header("Location: login.php");
        }
        exit();
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div id="tservicios" class="contenedor">
        <div class="contenedor__todo">
 <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar en el sistema</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera-register">
                    <h3>¿No tienes una cuenta?</h3>
                    <p>Contacta al administrador para registrarte</p>
                    <button id="btn__registrarse">Registro</button>
                </div>
            </div>

            <div class="contenedor__login-register">
                <!-- Formulario de Login -->
                <form method="POST" class="formulario__login">
                    <h2>Iniciar Sesión</h2>
                    <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                    <input type="password" name="clave" placeholder="Contraseña" required>
                    <button type="submit">Entrar</button>
                </form>

                <!-- Formulario de Registro (solo visual, desactivado) -->
                <form class="formulario__register">
                    <h2>Registro</h2>
                    <p style="text-align:center;">Registro deshabilitado. Contacta al administrador.</p>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.querySelector(".formulario__login");
        const registerForm = document.querySelector(".formulario__register");
        const containerLoginRegister = document.querySelector(".contenedor__login-register");
        const boxBackLogin = document.querySelector(".caja__trasera-login");
        const boxBackRegister = document.querySelector(".caja__trasera-register");
        const containerAll = document.querySelector(".contenedor__todo");

        function toggleForm(showLogin) {
            const isDesktop = window.innerWidth > 850;
            if (isDesktop) {
                containerLoginRegister.style.left = showLogin ? "0px" : "calc(100% - 400px)";
                boxBackLogin.style.opacity = showLogin ? "0" : "1";
                boxBackRegister.style.opacity = showLogin ? "1" : "0";
            } else {
                containerLoginRegister.style.left = "0px";
                boxBackLogin.style.display = showLogin ? "none" : "block";
                boxBackRegister.style.display = showLogin ? "block" : "none";
            }
            loginForm.style.display = showLogin ? "block" : "none";
            registerForm.style.display = showLogin ? "none" : "block";
        }

        document.getElementById("btn__iniciar-sesion").addEventListener("click", () => toggleForm(true));
        document.getElementById("btn__registrarse").addEventListener("click", () => toggleForm(false));
        toggleForm(true);
    });
    </script>
</body>
</html>

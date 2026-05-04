<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloAjustes.php';
class ControladorLogin {
    
    public function index() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=dashboard&a=index');
            // Por ahora solo mostramos mensaje
            echo "Ya estás logueado. Redirigiendo al dashboard...";
            exit;
        }
        // Mostrar formulario de login
        require_once 'Vistas/Login/login.php';
    }

        public function verificar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error_login'] = "Por favor, complete todos los campos.";
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        $modeloUsuario = new ModeloUsuario();
        $usuario = $modeloUsuario->verificarCredenciales($email, $password);

        if ($usuario) {
            // Login exitoso
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];

            // --- CARGA DE AJUSTES EN SESIÓN ---
            $modeloAj = new ModeloAjustes();
            $ajustes = $modeloAj->obtenerAjustes($usuario['id_usuario']); // crea si no existen
            $_SESSION['ajustes'] = $ajustes;

            header('Location: ' . BASE_URL . '?c=dashboard&a=index');
            exit;
        } else {
            $_SESSION['error_login'] = "Email o contraseña incorrectos.";
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '?c=login');
        exit;
    }
}
?>
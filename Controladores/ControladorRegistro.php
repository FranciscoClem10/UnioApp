<?php
require_once 'Modelos/ModeloUsuario.php';
class ControladorRegistro {
    
    public function index() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }
        // Mostrar formulario de registro
        require_once 'Vistas/Registro/registro.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        // 1. Validación básica de campos obligatorios
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
        $apellido_materno = trim($_POST['apellido_materno'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

        // Validar que los campos requeridos no estén vacíos (apellido paterno es obligatorio, materno opcional)
        if (empty($nombre) || empty($apellido_paterno) || empty($email) || empty($password) || empty($fecha_nacimiento)) {
            $_SESSION['error_registro'] = "Por favor, complete todos los campos obligatorios (nombre, apellido paterno, email, contraseña y fecha de nacimiento).";
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }

        // Validar que las contraseñas coincidan
        if ($password !== $confirm_password) {
            $_SESSION['error_registro'] = "Las contraseñas no coinciden.";
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_registro'] = "El formato del correo electrónico no es válido.";
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }

        // 2. Validar que el email no esté registrado previamente
        $modeloUsuario = new ModeloUsuario();
        if ($modeloUsuario->obtenerPorEmail($email)) {
            $_SESSION['error_registro'] = "Este correo electrónico ya está registrado.";
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }

        // 3. Procesar la foto de perfil
        $foto_blob = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            // Validar tamaño (ej: 2MB)
            if ($_FILES['foto_perfil']['size'] > 2 * 1024 * 1024) {
                $_SESSION['error_registro'] = "La foto no puede superar los 2MB.";
                header('Location: ' . BASE_URL . '?c=registro');
                exit;
            }
            // Validar tipo de imagen (JPEG, PNG, WEBP)
            $tipo_imagen = mime_content_type($_FILES['foto_perfil']['tmp_name']);
            if (!in_array($tipo_imagen, ['image/jpeg', 'image/png', 'image/webp'])) {
                $_SESSION['error_registro'] = "Formato de imagen no válido. Solo se permiten JPG, PNG y WEBP.";
                header('Location: ' . BASE_URL . '?c=registro');
                exit;
            }
            // Leer el contenido del archivo como binario
            $foto_blob = file_get_contents($_FILES['foto_perfil']['tmp_name']);
        } elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Manejar otros errores de subida
            $error_codigo = $_FILES['foto_perfil']['error'];
            $_SESSION['error_registro'] = "Error al subir la foto. Código: " . $error_codigo;
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }

        // 4. Procesar ubicación (latitud y longitud)
        $latitud = !empty($_POST['latitud']) ? (float)$_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? (float)$_POST['longitud'] : null;

        // 5. Preparar datos para el modelo (usando apellido_paterno y apellido_materno)
        $datos_usuario = [
            'nombre' => $nombre,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha_nacimiento' => $fecha_nacimiento,
            'genero' => $_POST['genero'] ?? 'Prefiero no decir',
            'latitud' => $latitud,
            'longitud' => $longitud,
            'biografia' => trim($_POST['biografia'] ?? ''),
            'foto_blob' => $foto_blob,
        ];

        // 6. Intentar guardar en la base de datos
        $usuario_id = $modeloUsuario->crearUsuario($datos_usuario);

        if ($usuario_id) {
            // Registro exitoso: iniciar sesión automáticamente
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_email'] = $email;
            
            // Redirigir al dashboard
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        } else {
            $_SESSION['error_registro'] = "Ocurrió un error al crear tu cuenta. Por favor, intenta de nuevo.";
            header('Location: ' . BASE_URL . '?c=registro');
            exit;
        }
    }
}
?>
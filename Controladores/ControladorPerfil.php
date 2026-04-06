<?php
class ControladorPerfil {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $usuario = $modelo->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            session_destroy();
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        // Convertir foto a base64 si existe
        if (!empty($usuario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $usuario['foto_perfil']);
            finfo_close($finfo);
            $usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
        } else {
            $usuario['foto_base64'] = null;
        }
        $amigos = $modelo->obtenerAmigos($_SESSION['usuario_id']);
        $solicitudes = $modelo->obtenerSolicitudesPendientes($_SESSION['usuario_id']);
        require_once 'Vistas/Perfil/index.php';
    }

    public function editar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $usuario = $modelo->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        if (!empty($usuario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $usuario['foto_perfil']);
            finfo_close($finfo);
            $usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
        } else {
            $usuario['foto_base64'] = null;
        }
        require_once 'Vistas/Perfil/editar.php';
    }

    public function actualizar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=perfil&a=editar');
            exit;
        }
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $genero = $_POST['genero'] ?? 'Prefiero no decir';
        $biografia = trim($_POST['biografia'] ?? '');
        $latitud = isset($_POST['latitud']) && $_POST['latitud'] !== '' ? (float)$_POST['latitud'] : null;
        $longitud = isset($_POST['longitud']) && $_POST['longitud'] !== '' ? (float)$_POST['longitud'] : null;

        if (empty($nombre) || empty($apellidos) || empty($fecha_nacimiento)) {
            $_SESSION['error_perfil'] = "Nombre, apellidos y fecha de nacimiento son obligatorios.";
            header('Location: ' . BASE_URL . '?c=perfil&a=editar');
            exit;
        }

        // Procesar foto
        $foto_blob = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['foto_perfil']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error_perfil'] = "La foto no puede superar los 5MB.";
                header('Location: ' . BASE_URL . '?c=perfil&a=editar');
                exit;
            }
            $tipo = mime_content_type($_FILES['foto_perfil']['tmp_name']);
            if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
                $_SESSION['error_perfil'] = "Formato no válido. Use JPG, PNG o WEBP.";
                header('Location: ' . BASE_URL . '?c=perfil&a=editar');
                exit;
            }
            $foto_blob = file_get_contents($_FILES['foto_perfil']['tmp_name']);
        }

        $datos = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'telefono' => $telefono,
            'fecha_nacimiento' => $fecha_nacimiento,
            'genero' => $genero,
            'biografia' => $biografia,
            'latitud' => $latitud,
            'longitud' => $longitud
        ];

        $modelo = new ModeloUsuario();
        if ($modelo->actualizarPerfil($_SESSION['usuario_id'], $datos, $foto_blob)) {
            $_SESSION['mensaje_perfil'] = "Perfil actualizado correctamente.";
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        } else {
            $_SESSION['error_perfil'] = "Error al actualizar el perfil.";
            header('Location: ' . BASE_URL . '?c=perfil&a=editar');
            exit;
        }
    }

    // Mostrar página de nuevos amigos (búsqueda y solicitudes pendientes)
    public function nuevosAmigos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $solicitudes = $modelo->obtenerSolicitudesPendientes($_SESSION['usuario_id']);
        $resultados = [];
        if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
            $termino = trim($_GET['buscar']);
            $resultados = $modelo->buscarUsuarios($termino, $_SESSION['usuario_id']);
        }
        require_once 'Vistas/Perfil/nuevosAmigos.php';
    }

    // Enviar solicitud de amistad (vía POST)
    public function enviarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
            exit;
        }
        $id_receptor = (int)($_POST['id_usuario'] ?? 0);
        if ($id_receptor <= 0) {
            $_SESSION['error_amigos'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        $resultado = $modelo->enviarSolicitud($_SESSION['usuario_id'], $id_receptor);
        if ($resultado === true) {
            $_SESSION['mensaje_amigos'] = "Solicitud enviada.";
        } elseif ($resultado === 'ya_son_amigos') {
            $_SESSION['error_amigos'] = "Ya son amigos.";
        } elseif ($resultado === 'solicitud_pendiente') {
            $_SESSION['error_amigos'] = "Ya existe una solicitud pendiente.";
        } else {
            $_SESSION['error_amigos'] = "Error al enviar solicitud.";
        }
        header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
        exit;
    }

    // Aceptar solicitud de amistad
    public function aceptarSolicitud() {
        $this->responder('aceptado');
    }

    // Rechazar solicitud
    public function rechazarSolicitud() {
        $this->responder('rechazado');
    }

    private function responder($estado) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $id_solicitante = (int)($_POST['id_solicitante'] ?? 0);
        if ($id_solicitante <= 0) {
            $_SESSION['error_perfil'] = "Datos inválidos.";
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $estado)) {
            $_SESSION['mensaje_perfil'] = "Solicitud " . ($estado == 'aceptado' ? "aceptada" : "rechazada");
        } else {
            $_SESSION['error_perfil'] = "Error al procesar la solicitud.";
        }
        header('Location: ' . BASE_URL . '?c=perfil');
        exit;
    }
}
?>
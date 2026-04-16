<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloNotificacion.php';
class ControladorAmigos {
    public function nuevosAmigos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $resultados = [];
        $termino = '';
        if (isset($_GET['buscar']) && strlen(trim($_GET['buscar'])) > 2) {
            $termino = trim($_GET['buscar']);
            $resultados = $modelo->buscarUsuariosConRelacion($_SESSION['usuario_id'], $termino);
            // Añadir campo 'apellidos' para compatibilidad con vistas antiguas
            foreach ($resultados as &$usuario) {
                $usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
            }
        }
        require_once 'Vistas/Amigos/nuevosAmigos.php';
    }

    public function enviarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_receptor = (int)($_GET['id'] ?? 0);
        if ($id_receptor <= 0) {
            $_SESSION['error_amigos'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=amigos&a=nuevosAmigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->enviarSolicitudAmistad($_SESSION['usuario_id'], $id_receptor)) {
            $_SESSION['mensaje_amigos'] = "Solicitud enviada correctamente.";

            // Crear notificación para el receptor
            $modeloNotif = new ModeloNotificacion();
            $nombreRemitente = $_SESSION['usuario_nombre'] ?? 'Un usuario';
            $modeloNotif->crear(
                $id_receptor,
                'solicitud_amistad',
                'Nueva solicitud de amistad',
                "$nombreRemitente te ha enviado una solicitud de amistad.",
                '?c=perfil'
            );
        } else {
            $_SESSION['error_amigos'] = "No se pudo enviar la solicitud (quizás ya existe).";
        }
        header('Location: ' . BASE_URL . '?c=amigos&a=nuevosAmigos');
        exit;
    }

    public function responder() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_solicitante = (int)($_GET['id'] ?? 0);
        $accion = $_GET['accion'] ?? '';
        if ($id_solicitante <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
            $_SESSION['error_amigos'] = "Datos inválidos.";
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $accion)) {
            $_SESSION['mensaje_amigos'] = "Solicitud $accion exitosamente.";
        } else {
            $_SESSION['error_amigos'] = "Error al procesar la solicitud.";
        }

        $modeloNotif = new ModeloNotificacion();
        if ($accion === 'aceptar') {
            $modeloNotif->crear(
                $id_solicitante,
                'amistad',
                'Solicitud de amistad aceptada',
                $_SESSION['usuario_nombre'] . ' ha aceptado tu solicitud de amistad.',
                '?c=perfil'
            );
        } else {
            $modeloNotif->crear(
                $id_solicitante,
                'amistad',
                'Solicitud de amistad rechazada',
                $_SESSION['usuario_nombre'] . ' ha rechazado tu solicitud de amistad.',
                null
            );
        }

        header('Location: ' . BASE_URL . '?c=perfil');
        exit;
    }

    public function verPerfil() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }
        $modelo = new ModeloUsuario();
        $usuario = $modelo->obtenerPorId($id);
        if (!$usuario) {
            die("Usuario no encontrado");
        }
        // Añadir campo 'apellidos' para compatibilidad
        $usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
        // Convertir foto
        if (!empty($usuario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $usuario['foto_perfil']);
            finfo_close($finfo);
            $usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
        }
        // También podemos mostrar amigos comunes, etc.
        require_once 'Vistas/Amigos/verPerfil.php';
    }
}
?>
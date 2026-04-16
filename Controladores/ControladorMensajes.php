<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloMensaje.php';
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloNotificacion.php';

class ControladorMensajes {

    public function chats() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modeloMsg = new ModeloMensaje();
        $conversacionesActividad = $modeloMsg->obtenerConversacionesActividad($_SESSION['usuario_id']);
        $conversacionesPrivadas = $modeloMsg->obtenerConversacionesPrivadas($_SESSION['usuario_id']);
        require_once 'Vistas/Mensajes/chats.php';
    }

    public function verActividad() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_actividad = (int)($_GET['id'] ?? 0);
        if ($id_actividad <= 0) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        // Verificar que el usuario sea participante de la actividad
        $modeloAct = new ModeloActividad();
        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if (!$actividad) {
            die("Actividad no encontrada");
        }
        // Verificar participación (podría hacerse con un método en modelo actividad)
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT 1 FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user AND estado = 'aceptado'");
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $_SESSION['usuario_id']]);
        if (!$stmt->fetchColumn()) {
            die("No tienes acceso a este chat de actividad.");
        }

        $modeloMsg = new ModeloMensaje();
        $mensajes = $modeloMsg->obtenerMensajesActividad($id_actividad, $_SESSION['usuario_id']);
        require_once 'Vistas/Mensajes/verActividad.php';
    }

    public function verPrivado() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_amigo = (int)($_GET['id'] ?? 0);
        if ($id_amigo <= 0) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        // Verificar que sean amigos
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT 1 FROM amistades WHERE ((id_solicitante = :id1 AND id_receptor = :id2) OR (id_solicitante = :id2 AND id_receptor = :id1)) AND estado = 'aceptado'");
        $stmt->execute([':id1' => $_SESSION['usuario_id'], ':id2' => $id_amigo]);
        if (!$stmt->fetchColumn()) {
            die("No eres amigo de este usuario.");
        }

        $modeloMsg = new ModeloMensaje();
        $mensajes = $modeloMsg->obtenerMensajesPrivados($_SESSION['usuario_id'], $id_amigo);
        // Obtener datos del amigo
        $modeloUser = new ModeloUsuario();
        $amigo = $modeloUser->obtenerPorId($id_amigo);
        if (!$amigo) die("Amigo no encontrado");
        require_once 'Vistas/Mensajes/verPrivado.php';
    }

    public function enviar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        $tipo = $_POST['tipo'] ?? '';
        $contenido = trim($_POST['contenido'] ?? '');
        if (empty($contenido)) {
            $_SESSION['error_mensaje'] = "El mensaje no puede estar vacío.";
            if ($tipo == 'actividad') {
                $id_actividad = (int)($_POST['id_actividad'] ?? 0);
                header('Location: ' . BASE_URL . '?c=mensajes&a=verActividad&id=' . $id_actividad);
            } else {
                $id_amigo = (int)($_POST['id_amigo'] ?? 0);
                header('Location: ' . BASE_URL . '?c=mensajes&a=verPrivado&id=' . $id_amigo);
            }
            exit;
        }

        $modelo = new ModeloMensaje();
        $exito = false;
        $redir = '';

        if ($tipo == 'actividad') {
            $id_actividad = (int)($_POST['id_actividad'] ?? 0);
            $exito = $modelo->guardarMensajeActividad($id_actividad, $_SESSION['usuario_id'], $contenido);
            $redir = '?c=mensajes&a=verActividad&id=' . $id_actividad;
        } elseif ($tipo == 'privado') {
            $id_amigo = (int)($_POST['id_amigo'] ?? 0);
            $exito = $modelo->guardarMensajePrivado($_SESSION['usuario_id'], $id_amigo, $contenido);
            $redir = '?c=mensajes&a=verPrivado&id=' . $id_amigo;

            // ========== CREAR NOTIFICACIÓN PARA EL DESTINATARIO ==========
            if ($exito) {
                $modeloNotif = new ModeloNotificacion();
                // Obtener nombre completo del remitente
                $modeloUser = new ModeloUsuario();
                $remitente = $modeloUser->obtenerPorId($_SESSION['usuario_id']);
                $nombreRemitente = trim($remitente['nombre'] . ' ' . $remitente['apellido_paterno']);
                $modeloNotif->crear(
                    $id_amigo,   // destinatario
                    'mensaje',
                    'Nuevo mensaje privado',
                    $nombreRemitente . ' te ha enviado un mensaje: "' . substr($contenido, 0, 50) . (strlen($contenido) > 50 ? '...' : '') . '"',
                    '?c=mensajes&a=verPrivado&id=' . $_SESSION['usuario_id']
                );
            }
            // ============================================================
        } else {
            $_SESSION['error_mensaje'] = "Tipo de mensaje inválido.";
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }

        if (!$exito) {
            $_SESSION['error_mensaje'] = "No se pudo enviar el mensaje (verifica permisos).";
        }
        header('Location: ' . BASE_URL . $redir);
        exit;
    }


    public function eliminar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_mensaje = (int)($_GET['id'] ?? 0);
        $tipo = $_GET['tipo'] ?? '';
        if ($id_mensaje <= 0) {
            $_SESSION['error_mensaje'] = "Mensaje inválido.";
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        $modelo = new ModeloMensaje();
        $exito = false;
        if ($tipo == 'actividad') {
            $exito = $modelo->eliminarMensajeActividad($id_mensaje, $_SESSION['usuario_id']);
            $id_actividad = (int)($_GET['id_act'] ?? 0);
            $redir = '?c=mensajes&a=verActividad&id=' . $id_actividad;
        } elseif ($tipo == 'privado') {
            $exito = $modelo->eliminarMensajePrivado($id_mensaje, $_SESSION['usuario_id']);
            $id_amigo = (int)($_GET['id_amigo'] ?? 0);
            $redir = '?c=mensajes&a=verPrivado&id=' . $id_amigo;
        } else {
            $_SESSION['error_mensaje'] = "Tipo inválido.";
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        if (!$exito) {
            $_SESSION['error_mensaje'] = "No se pudo eliminar el mensaje.";
        }
        header('Location: ' . BASE_URL . $redir);
        exit;
    }
}
?>
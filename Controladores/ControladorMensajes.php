<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloMensaje.php';
require_once 'Modelos/ModeloNotificacion.php';

class ControladorMensajes {

    public function chats() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modeloMsg = new ModeloMensaje();
        $conversaciones = $modeloMsg->obtenerConversacionesPrivadas($_SESSION['usuario_id']);
        require_once 'Vistas/Mensajes/chats.php';
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
        // Verificar amistad
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT 1 FROM amistades WHERE ((id_solicitante = :id1 AND id_receptor = :id2) OR (id_solicitante = :id2 AND id_receptor = :id1)) AND estado = 'aceptado'");
        $stmt->execute([':id1' => $_SESSION['usuario_id'], ':id2' => $id_amigo]);
        if (!$stmt->fetchColumn()) {
            die("No eres amigo de este usuario.");
        }
        // Marcar notificaciones como leídas
        $modeloNotif = new ModeloNotificacion();
        $modeloNotif->marcarLeidasPorContexto($_SESSION['usuario_id'], 'mensaje_privado', $id_amigo);
        
        // Obtener datos del amigo
        $modeloUser = new ModeloUsuario();
        $amigo = $modeloUser->obtenerPorId($id_amigo);
        if (!$amigo) die("Amigo no encontrado");

        // Convertir foto a base64 (sin finfo)
        if (!empty($amigo['foto_perfil'])) {
            $amigo['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($amigo['foto_perfil']);
        } else {
            $amigo['foto_base64'] = null;
        }
        
        // Actualizar última conexión del usuario actual
        $modeloUser->actualizarUltimaConexion($_SESSION['usuario_id']);
        
        // Obtener mensajes
        $modeloMsg = new ModeloMensaje();
        $mensajes = $modeloMsg->obtenerMensajesPrivados($_SESSION['usuario_id'], $id_amigo);
        
        require_once 'Vistas/Mensajes/verPrivado.php';
    }

    public function enviar() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        $id_amigo = (int)($_POST['id_amigo'] ?? 0);
        $contenido = trim($_POST['contenido'] ?? '');
        if (empty($contenido) || $id_amigo <= 0) {
            $_SESSION['error_mensaje'] = "Mensaje vacío o destinatario inválido.";
            header('Location: ' . BASE_URL . '?c=mensajes&a=verPrivado&id=' . $id_amigo);
            exit;
        }
        $modelo = new ModeloMensaje();
        $exito = $modelo->guardarMensajePrivado($_SESSION['usuario_id'], $id_amigo, $contenido);
        if (!$exito) {
            $_SESSION['error_mensaje'] = "No se pudo enviar el mensaje.";
        } else {
            // Crear notificación para el destinatario (opcional)
            $modeloNotif = new ModeloNotificacion();
            $remitente = (new ModeloUsuario())->obtenerPorId($_SESSION['usuario_id']);
            $nombreRemitente = $remitente['nombre_completo'] ?? $remitente['nombre'];
            $modeloNotif->crear(
                $id_amigo,
                'mensaje_privado',
                'Nuevo mensaje de ' . $nombreRemitente,
                substr($contenido, 0, 100),
                '?c=mensajes&a=verPrivado&id=' . $_SESSION['usuario_id']
            );
        }
        header('Location: ' . BASE_URL . '?c=mensajes&a=verPrivado&id=' . $id_amigo);
        exit;
    }

    public function obtenerNuevos() {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                throw new Exception('No autorizado');
            }
            $id_amigo = (int)($_GET['id'] ?? 0);
            $ultimo_id = (int)($_GET['ultimo_id'] ?? 0);
            if ($id_amigo <= 0) {
                echo json_encode(['success' => true, 'data' => []]);
                exit;
            }
            $modelo = new ModeloMensaje();
            $nuevos = $modelo->obtenerNuevosMensajesPrivados($_SESSION['usuario_id'], $id_amigo, $ultimo_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $nuevos]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
?>
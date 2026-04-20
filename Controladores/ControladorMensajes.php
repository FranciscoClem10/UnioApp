<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloMensaje.php';
require_once 'Modelos/ModeloNotificacion.php';

class ControladorMensajes {

    // Lista de conversaciones privadas
    public function chats() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modeloMensaje = new ModeloMensaje();
        $conversacionesPrivadas = $modeloMensaje->obtenerConversaciones($_SESSION['usuario_id']);
        require_once 'Vistas/Mensajes/chats.php';
    }

    // Vista del chat privado (carga inicial)
    public function verPrivado() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $destinatarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$destinatarioId) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        // Verificar amistad
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT 1 FROM amistades WHERE ((id_solicitante = :id1 AND id_receptor = :id2) OR (id_solicitante = :id2 AND id_receptor = :id1)) AND estado = 'aceptado'");
        $stmt->execute([':id1' => $_SESSION['usuario_id'], ':id2' => $destinatarioId]);
        if (!$stmt->fetchColumn()) {
            die("No eres amigo de este usuario.");
        }
        // Marcar notificaciones como leídas
        $modeloNotif = new ModeloNotificacion();
        $modeloNotif->marcarLeidasPorContexto($_SESSION['usuario_id'], 'mensaje_privado', $destinatarioId);
        // Obtener datos del destinatario
        $modeloUsuario = new ModeloUsuario();
        $destinatario = $modeloUsuario->obtenerPorId($destinatarioId);
        if (!$destinatario) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }
        // Convertir foto a base64 para el header
        if (!empty($destinatario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $destinatario['foto_perfil']);
            finfo_close($finfo);
            $destinatario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($destinatario['foto_perfil']);
        } else {
            $destinatario['foto_base64'] = null;
        }
        // Calcular estado online (última conexión hace menos de 5 minutos)
        $destinatario['online'] = (strtotime($destinatario['ultima_conexion'] ?? '2000-01-01') > time() - 300);
        // Actualizar última conexión del usuario actual
        $modeloUsuario->actualizarUltimaConexion($_SESSION['usuario_id']);
        // Cargar vista
        require_once 'Vistas/Mensajes/verPrivado.php';
    }

    // Endpoint AJAX para obtener mensajes privados (polling)
    public function obtener() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $destinatarioId = isset($_GET['destinatario_id']) ? (int)$_GET['destinatario_id'] : 0;
        $lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
        if (!$destinatarioId) {
            echo json_encode(['error' => 'Falta destinatario']);
            exit;
        }
        $modelo = new ModeloMensaje();
        $mensajes = $modelo->obtenerMensajes($_SESSION['usuario_id'], $destinatarioId, $lastId);
        // Marcar como leídos los recibidos
        $idsNoLeidos = [];
        foreach ($mensajes as &$m) {
            if ($m['id_destinatario'] == $_SESSION['usuario_id'] && $m['leido'] == 0) {
                $idsNoLeidos[] = $m['id_mensaje'];
                $m['leido'] = 1;
            }
        }
        if (!empty($idsNoLeidos)) {
            $modelo->marcarComoLeidos($_SESSION['usuario_id'], $idsNoLeidos);
        }
        header('Content-Type: application/json');
        echo json_encode(['mensajes' => $mensajes]);
    }

    // Endpoint AJAX para enviar mensaje privado
    public function enviar() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        $destinatarioId = isset($_POST['destinatario_id']) ? (int)$_POST['destinatario_id'] : 0;
        $contenido = trim($_POST['contenido'] ?? '');
        if (!$destinatarioId || !$contenido) {
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }
        $modelo = new ModeloMensaje();
        $resultado = $modelo->enviarMensaje($_SESSION['usuario_id'], $destinatarioId, $contenido);
        if ($resultado) {
            // Notificación
            $modeloNotif = new ModeloNotificacion();
            $remitente = (new ModeloUsuario())->obtenerPorId($_SESSION['usuario_id']);
            $nombreRemitente = $remitente['nombre_completo'] ?? $remitente['nombre'];
            $modeloNotif->crear(
                $destinatarioId,
                'mensaje_privado',
                'Nuevo mensaje de ' . $nombreRemitente,
                substr($contenido, 0, 100),
                '?c=mensajes&a=verPrivado&id=' . $_SESSION['usuario_id']
            );
            echo json_encode(['success' => true] + $resultado);
        } else {
            echo json_encode(['error' => 'No se pudo enviar el mensaje']);
        }
    }

    // Eliminar mensaje privado (AJAX)
    public function eliminarMensajePrivado() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $id_mensaje = (int)($_GET['id'] ?? 0);
        if (!$id_mensaje) {
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }
        $modelo = new ModeloMensaje();
        $exito = $modelo->eliminarMensajePrivado($id_mensaje, $_SESSION['usuario_id']);
        echo json_encode(['success' => $exito]);
    }
}
?>
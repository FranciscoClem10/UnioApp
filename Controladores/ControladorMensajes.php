<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloMensaje.php';
require_once 'Modelos/ModeloNotificacion.php';
require_once 'Modelos/ModeloMensajeGrupo.php';
require_once 'Modelos/ModeloActividad.php';

class ControladorMensajes {

    // ------------------------------------------------------------------
    // MÉTODOS PARA CHATS (COMUNES)
    // ------------------------------------------------------------------

    /**
     * Lista combinada de conversaciones (privadas + actividades)
     * Reemplaza a los dos métodos chats() anteriores
     */
    public function chats() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_usuario = $_SESSION['usuario_id'];

        // Modelos
        $modeloMensaje = new ModeloMensaje();
        $modeloMensajeGrupo = new ModeloMensajeGrupo();

        // Obtener conversaciones privadas
        $conversacionesPrivadas = $modeloMensaje->obtenerConversaciones($id_usuario);

        // Obtener conversaciones de actividades
        $conversacionesActividad = $modeloMensajeGrupo->obtenerConversacionesActividad($id_usuario);

        require_once 'Vistas/Mensajes/chats.php';
    }

    // ------------------------------------------------------------------
    // MÉTODOS PARA MENSAJES PRIVADOS
    // ------------------------------------------------------------------

    /** Vista del chat privado (carga inicial) */
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

        // Datos del destinatario
        $modeloUsuario = new ModeloUsuario();
        $destinatario = $modeloUsuario->obtenerPorId($destinatarioId);
        if (!$destinatario) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }

        // Convertir foto a base64
        if (!empty($destinatario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $destinatario['foto_perfil']);
            finfo_close($finfo);
            $destinatario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($destinatario['foto_perfil']);
        } else {
            $destinatario['foto_base64'] = null;
        }

        $destinatario['online'] = (strtotime($destinatario['ultima_conexion'] ?? '2000-01-01') > time() - 300);
        $modeloUsuario->actualizarUltimaConexion($_SESSION['usuario_id']);

        require_once 'Vistas/Mensajes/verPrivado.php';
    }

    /** Endpoint AJAX: obtener mensajes privados (antes "obtener") */
    public function obtenerMensajesPrivados() {
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

    /** Endpoint AJAX: enviar mensaje privado (antes "enviar") */
    public function enviarMensajePrivado() {
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

    /** Eliminar mensaje privado (AJAX) - se mantiene nombre único */
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

    // ------------------------------------------------------------------
    // MÉTODOS PARA MENSAJES DE ACTIVIDADES / GRUPOS
    // ------------------------------------------------------------------

    /** Vista del chat de actividad (antes verActividad) */
    public function verActividad() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_GET['id'] ?? 0;
        if (!$id_actividad) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }

        $modeloActividad = new ModeloActividad();
        if (!$modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            die("No tienes permiso para acceder a este chat.");
        }

        $actividad = $modeloActividad->obtenerPorId($id_actividad);
        $participantes = $modeloActividad->obtenerParticipantes($id_actividad);
        $modeloMensajeGrupo = new ModeloMensajeGrupo();
        $mensajes = $modeloMensajeGrupo->obtenerMensajesActividad($id_actividad);
        $modeloMensajeGrupo->marcarLeidosActividad($id_actividad, $id_usuario);

        require_once 'Vistas/Mensajes/verActividad.php';
    }

    /** Endpoint AJAX: obtener nuevos mensajes de actividad (antes obtenerNuevos) */
    public function obtenerNuevosMensajesActividad() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_GET['id_actividad'] ?? 0;
        $ultimo_id = $_GET['ultimo_id'] ?? 0;

        $modeloActividad = new ModeloActividad();
        if (!$id_actividad || !$modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        $modeloMensajeGrupo = new ModeloMensajeGrupo();
        $nuevos = $modeloMensajeGrupo->obtenerNuevosMensajes($id_actividad, $ultimo_id);
        echo json_encode(['mensajes' => $nuevos]);
    }

    /** Endpoint AJAX: enviar mensaje a actividad (antes enviar) */
    public function enviarMensajeActividad() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_POST['id_actividad'] ?? 0;
        $contenido = trim($_POST['contenido'] ?? '');

        if (!$id_actividad || !$contenido) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        $modeloActividad = new ModeloActividad();
        if (!$modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            echo json_encode(['error' => 'No puedes enviar mensajes a esta actividad']);
            exit;
        }

        $modeloMensajeGrupo = new ModeloMensajeGrupo();
        $exito = $modeloMensajeGrupo->enviarMensaje($id_actividad, $id_usuario, $contenido);
        if ($exito) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error al guardar el mensaje']);
        }
    }
}
?>
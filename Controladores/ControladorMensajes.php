<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloMensaje.php';

class ControladorMensajes {
    // Muestra la vista del chat privado con un usuario
    public function verPrivado() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $destinatarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$destinatarioId) {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }

        $modeloUsuario = new ModeloUsuario();
        $destinatario = $modeloUsuario->obtenerPorId($destinatarioId);
        if (!$destinatario) {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }

        $miNombre = $modeloUsuario->obtenerPorId($_SESSION['usuario_id'])['nombre'];

        require_once 'Vistas/Mensajes/verPrivado.php';
    }

    // Endpoint AJAX para obtener mensajes
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

        // Marcar como leídos los mensajes recibidos
        $idsNoLeidos = [];
        foreach ($mensajes as $m) {
            if ($m['id_destinatario'] == $_SESSION['usuario_id'] && $m['leido'] == 0) {
                $idsNoLeidos[] = $m['id_mensaje'];
                $m['leido'] = 1; // actualizar en la respuesta
            }
        }
        if (!empty($idsNoLeidos)) {
            $modelo->marcarComoLeidos($_SESSION['usuario_id'], $idsNoLeidos);
        }

        header('Content-Type: application/json');
        echo json_encode(['mensajes' => $mensajes]);
    }

    // Endpoint AJAX para enviar mensaje
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
            echo json_encode(['success' => true] + $resultado);
        } else {
            echo json_encode(['error' => 'No se pudo enviar el mensaje']);
        }
    }

    public function chats() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modeloMsg = new ModeloMensaje();
        $conversaciones = $modeloMsg->obtenerConversacionesPrivadas($_SESSION['usuario_id']);
        require_once 'Vistas/Mensajes/chats.php';
    }
}
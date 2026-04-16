<?php
require_once 'Modelos/ModeloNotificacion.php';

class ControladorNotificacion {

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloNotificacion();
        $notificaciones = $modelo->obtenerTodas($_SESSION['usuario_id']);
        $noLeidas = $modelo->contarNoLeidas($_SESSION['usuario_id']);
        require_once 'Vistas/Notificaciones/index.php';
    }

    public function marcarLeida() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_notificacion = (int)($_GET['id'] ?? 0);
        if ($id_notificacion > 0) {
            $modelo = new ModeloNotificacion();
            $modelo->marcarLeida($id_notificacion, $_SESSION['usuario_id']);
        }
        header('Location: ' . BASE_URL . '?c=notificacion');
        exit;
    }

    public function marcarTodasLeidas() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloNotificacion();
        $modelo->marcarTodasLeidas($_SESSION['usuario_id']);
        header('Location: ' . BASE_URL . '?c=notificacion');
        exit;
    }

    /**
     * Endpoint AJAX para obtener el contador de no leídas (útil para badge)
     */
    public function contarNoLeidasAjax() {
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $modelo = new ModeloNotificacion();
        $total = $modelo->contarNoLeidas($_SESSION['usuario_id']);
        header('Content-Type: application/json');
        echo json_encode(['no_leidas' => $total]);
        exit;
    }
}
?>
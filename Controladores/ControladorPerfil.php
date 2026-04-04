<?php
class ControladorPerfil {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        require_once 'Vistas/Perfil/index.php';
    }
}
?>
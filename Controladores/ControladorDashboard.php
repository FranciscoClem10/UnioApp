<?php
require_once __DIR__ . '/../Modelos/ModeloActividad.php';

class ControladorDashboard {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        
        $modeloActividad = new ModeloActividad();
        $actividades = $modeloActividad->obtenerTodasVisibles($_SESSION['usuario_id']);
        $misActividades = $modeloActividad->obtenerPorCreador($_SESSION['usuario_id']);
        $estadisticas = $modeloActividad->obtenerEstadisticas();
        
        $totalActividades = count($actividades);
        $actividadesPorCategoria = $estadisticas['por_categoria'];
        $totalMisActividades = count($misActividades);
        
        require_once 'Vistas/Dashboard/index.php';
    }
}
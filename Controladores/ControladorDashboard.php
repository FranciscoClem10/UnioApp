<?php
require_once __DIR__ . '/../Modelos/ModeloActividad.php';
require_once __DIR__ . '/../Modelos/ModeloUsuario.php'; 

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
        
        $modeloUsuario = new ModeloUsuario();
        $usuario = $modeloUsuario->obtenerPorId($_SESSION['usuario_id']);
        $userLat = $usuario['latitud'] ?? null;
        $userLng = $usuario['longitud'] ?? null;
        
        require_once 'Vistas/Dashboard/index.php';
    }
	
	public function guardarEstadoMapa() {
		// Recibir datos POST
		$lat = $_POST['lat'] ?? null;
		$lng = $_POST['lng'] ?? null;
		$zoom = $_POST['zoom'] ?? null;
		$capaId = $_POST['capa_id'] ?? null;

		// Guardar en sesión
		if ($lat !== null && $lng !== null) {
			$_SESSION['mapCenter'] = ['lat' => (float)$lat, 'lng' => (float)$lng];
		}
		if ($zoom !== null) {
			$_SESSION['mapZoom'] = (int)$zoom;
		}
		if ($capaId !== null) {
			$_SESSION['capaMapa'] = $capaId;
		}

		// Respuesta opcional
		echo json_encode(['status' => 'ok']);
	}
}
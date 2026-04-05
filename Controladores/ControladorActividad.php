<?php
require_once 'Modelos/ModeloActividad.php';
class ControladorActividad {
    public function detalle() {
        
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id = $_GET['id'] ?? 0;
        $modelo = new ModeloActividad();
        $actividad = $modelo->obtenerPorId($id);
        if (!$actividad) {
            die("Actividad no encontrada");
        }
        require_once 'Vistas/Actividad/detalle.php';
    }


    public function crear() {
        // Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        // Obtener tipos de actividad para el select
        $modelo = new ModeloActividad();
        $tipos = $modelo->obtenerTiposActividad();

        // Cargar la vista del formulario
        require_once 'Vistas/Actividad/crear.php';
    }

    public function guardar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }

        // Validar campos obligatorios
        $nombre = trim($_POST['nombre'] ?? '');
        $id_tipo = (int)($_POST['id_tipo'] ?? 0);
        $latitud = (float)($_POST['latitud'] ?? 0);
        $longitud = (float)($_POST['longitud'] ?? 0);
        $privacidad = $_POST['privacidad'] ?? 'publica';

        if (empty($nombre) || $id_tipo <= 0 || $latitud == 0 || $longitud == 0) {
            $_SESSION['error_crear_actividad'] = "Por favor complete todos los campos obligatorios: nombre, tipo y ubicación en el mapa.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }

        // Validar privacidad permitida
        $privacidades = ['publica', 'privada', 'por_aprobacion'];
        if (!in_array($privacidad, $privacidades)) {
            $privacidad = 'publica';
        }

        // Procesar foto de actividad (opcional)
        $foto_blob = null;
        if (isset($_FILES['foto_actividad']) && $_FILES['foto_actividad']['error'] === UPLOAD_ERR_OK) {
            // Validar tamaño máximo (ej: 5MB)
            if ($_FILES['foto_actividad']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error_crear_actividad'] = "La imagen no puede superar los 5MB.";
                header('Location: ' . BASE_URL . '?c=actividad&a=crear');
                exit;
            }
            // Validar tipo de imagen
            $tipo_imagen = mime_content_type($_FILES['foto_actividad']['tmp_name']);
            if (!in_array($tipo_imagen, ['image/jpeg', 'image/png', 'image/webp'])) {
                $_SESSION['error_crear_actividad'] = "Formato de imagen no válido. Solo JPG, PNG o WEBP.";
                header('Location: ' . BASE_URL . '?c=actividad&a=crear');
                exit;
            }
            $foto_blob = file_get_contents($_FILES['foto_actividad']['tmp_name']);
        }

        // Preparar array de datos
        $datos = [
            'id_tipo' => $id_tipo,
            'id_creador' => $_SESSION['usuario_id'],
            'nombre' => $nombre,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'requisitos' => trim($_POST['requisitos'] ?? ''),
            'edad_minima' => (int)($_POST['edad_minima'] ?? 0),
            'edad_maxima' => (int)($_POST['edad_maxima'] ?? 99),
            'limite_participantes_min' => (int)($_POST['limite_participantes_min'] ?? 1),
            'limite_participantes_max' => !empty($_POST['limite_participantes_max']) ? (int)$_POST['limite_participantes_max'] : null,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'privacidad' => $privacidad
        ];

        // Validar rangos de edad
        if ($datos['edad_minima'] > $datos['edad_maxima']) {
            $_SESSION['error_crear_actividad'] = "La edad mínima no puede ser mayor que la máxima.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }

        // Validar límites de participantes
        if ($datos['limite_participantes_min'] < 1) $datos['limite_participantes_min'] = 1;
        if ($datos['limite_participantes_max'] !== null && $datos['limite_participantes_max'] < $datos['limite_participantes_min']) {
            $_SESSION['error_crear_actividad'] = "El límite máximo de participantes no puede ser menor que el mínimo.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }

        $modelo = new ModeloActividad();
        $id_actividad = $modelo->crearActividad($datos, $foto_blob);

        if ($id_actividad) {
            $_SESSION['mensaje_exito'] = "¡Actividad creada exitosamente!";
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        } else {
            // El mensaje de error ya está en $_SESSION['error_crear_actividad'] desde el modelo
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }
    }


}
?>
<?php
require_once 'Modelos/ModeloActividad.php';
class ControladorActividad {
   
    public function crear() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloActividad();
        $tipos = $modelo->obtenerTiposActividad();
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

        // Validar fechas (nuevos campos)
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';
        if (empty($fecha_inicio) || empty($fecha_fin)) {
            $_SESSION['error_crear_actividad'] = "Debes especificar la fecha y hora de inicio y fin.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }
        // Convertir datetime-local (YYYY-MM-DDThh:mm) a formato DATETIME
        $fecha_inicio_dt = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha_inicio)));
        $fecha_fin_dt = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha_fin)));
        if ($fecha_inicio_dt >= $fecha_fin_dt) {
            $_SESSION['error_crear_actividad'] = "La fecha/hora de inicio debe ser anterior a la de fin.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }

        // Privacidad
        $privacidades = ['publica', 'privada', 'por_aprobacion'];
        if (!in_array($privacidad, $privacidades)) $privacidad = 'publica';

        // Foto
        $foto_blob = null;
        if (isset($_FILES['foto_actividad']) && $_FILES['foto_actividad']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['foto_actividad']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error_crear_actividad'] = "La imagen no puede superar los 5MB.";
                header('Location: ' . BASE_URL . '?c=actividad&a=crear');
                exit;
            }
            $tipo_imagen = mime_content_type($_FILES['foto_actividad']['tmp_name']);
            if (!in_array($tipo_imagen, ['image/jpeg', 'image/png', 'image/webp'])) {
                $_SESSION['error_crear_actividad'] = "Formato de imagen no válido. Solo JPG, PNG o WEBP.";
                header('Location: ' . BASE_URL . '?c=actividad&a=crear');
                exit;
            }
            $foto_blob = file_get_contents($_FILES['foto_actividad']['tmp_name']);
        }

        // Datos para el modelo
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
            'fecha_inicio' => $fecha_inicio_dt,
            'fecha_fin' => $fecha_fin_dt,
            'privacidad' => $privacidad
        ];

        // Validaciones adicionales
        if ($datos['edad_minima'] > $datos['edad_maxima']) {
            $_SESSION['error_crear_actividad'] = "La edad mínima no puede ser mayor que la máxima.";
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }
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
            header('Location: ' . BASE_URL . '?c=actividad&a=crear');
            exit;
        }
    }

    public function detalle() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id = $_GET['id'] ?? 0;
        $modelo = new ModeloActividad();
        $actividad = $modelo->obtenerDetalleCompleto($id);
        if (!$actividad) die("Actividad no encontrada");

        // Convertir foto BLOB a base64 si existe
        if (!empty($actividad['foto_actividad'])) {
            // Detectar tipo MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $actividad['foto_actividad']);
            finfo_close($finfo);
            $actividad['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($actividad['foto_actividad']);
        } else {
            $actividad['foto_base64'] = null;
        }

        $resenas = $modelo->obtenerResenas($id);
        $puedeResenar = $modelo->puedeResenar($id, $_SESSION['usuario_id']);
        require_once 'Vistas/Actividad/detalle.php';
    }

    public function guardarResena() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $calificacion = (int)($_POST['calificacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');
        if ($id_actividad <= 0 || $calificacion < 1 || $calificacion > 5) {
            $_SESSION['error_resena'] = "Datos inválidos para la reseña.";
            header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
            exit;
        }
        $modelo = new ModeloActividad();
        if ($modelo->puedeResenar($id_actividad, $_SESSION['usuario_id'])) {
            if ($modelo->guardarResena($id_actividad, $_SESSION['usuario_id'], $calificacion, $comentario)) {
                $_SESSION['exito_resena'] = "¡Gracias por tu reseña!";
            } else {
                $_SESSION['error_resena'] = "Error al guardar la reseña.";
            }
        } else {
            $_SESSION['error_resena'] = "No puedes reseñar esta actividad.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
        exit;
    }
}
?>
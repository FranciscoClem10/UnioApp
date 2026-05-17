<?php
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloNotificacion.php';

class ControladorActividad {
   
    public function crear() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$modelo = new ModeloActividad();
		$tipos = $modelo->obtenerTiposActividad();
		
		// Obtener usuario actual para su ubicación
		$modeloUser = new ModeloUsuario();
		$usuario = $modeloUser->obtenerPorId($_SESSION['usuario_id']);
		$user_lat = $usuario['latitud'] ?? null;
		$user_lng = $usuario['longitud'] ?? null;
		
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
		$direccion = trim($_POST['direccion'] ?? '');

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
            'privacidad' => $privacidad,
			'direccion' => $direccion
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

		// Convertir foto_actividad a base64 (si no está ya en obtenerDetalleCompleto)
		if (!empty($actividad['foto_actividad'])) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_buffer($finfo, $actividad['foto_actividad']);
			finfo_close($finfo);
			$actividad['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($actividad['foto_actividad']);
		} else {
			$actividad['foto_base64'] = null;
		}

		// Obtener datos del organizador (incluyendo foto)
		$modeloUser = new ModeloUsuario();
		$organizador = $modeloUser->obtenerPorId($actividad['organizador_id']);
		if ($organizador && !empty($organizador['foto_perfil'])) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_buffer($finfo, $organizador['foto_perfil']);
			finfo_close($finfo);
			$organizador['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($organizador['foto_perfil']);
		} else {
			$organizador['foto_base64'] = null;
		}

		// Asistentes
		$asistentes = $modelo->obtenerAsistentesLimitados($id, 4);
		$totalAsistentes = $modelo->contarParticipantesAceptados($id);
		$otrosAsistentes = max(0, $totalAsistentes - count($asistentes));

		// Reseñas y estadísticas
		$resenas = $modelo->obtenerResenas($id);
		$statsResenas = $modelo->obtenerEstadisticasResenas($id);
		$puedeResenar = $modelo->puedeResenar($id, $_SESSION['usuario_id']);
        // Después de obtener $organizador, agrega:
        $usuarioActual = $modeloUser->obtenerPorId($_SESSION['usuario_id']);
        $userLat = $usuarioActual['latitud'] ?? null;
        $userLng = $usuarioActual['longitud'] ?? null;

		// Estado de participación del usuario actual
		$participacion = $modelo->obtenerEstadoParticipacion($id, $_SESSION['usuario_id']);
		$yaUnido = ($participacion && $participacion['estado'] === 'aceptado');
		$solicitudPendiente = ($participacion && $participacion['estado'] === 'pendiente');
		$invitado = ($participacion && $participacion['estado'] === 'invitado');
		$esCreador = ($participacion && $participacion['rol'] === 'creador');

		// Validaciones para botón
		$edadValida = true;
		$capacidadLlena = ($actividad['limite_participantes_max'] && $totalAsistentes >= $actividad['limite_participantes_max']);
		$actividadNoDisponible = in_array($actividad['estado'], ['finalizada', 'cancelada', 'en_curso']);

		// Calcular porcentaje de capacidad para la barra
		$porcentajeCapacidad = 0;
		if ($actividad['limite_participantes_max'] > 0) {
			$porcentajeCapacidad = round(($totalAsistentes / $actividad['limite_participantes_max']) * 100);
		}

		// Incluir la nueva vista
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

    // Lista de actividades del creador (para elegir cuál editar)
    public function edicion() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloActividad();
        $actividades = $modelo->obtenerPorCreador($_SESSION['usuario_id']);
        require_once 'Vistas/Actividad/edicion.php';
    }

    // Formulario de edición de una actividad específica
    public function editar() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$id = (int)($_GET['id'] ?? 0);
		if ($id <= 0) {
			header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
			exit;
		}
		$modelo = new ModeloActividad();
		$actividad = $modelo->obtenerPorId($id);
		if (!$actividad || $actividad['id_creador'] != $_SESSION['usuario_id']) {
			$_SESSION['error_edicion'] = "No tienes permiso para editar esta actividad.";
			header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
			exit;
		}
		$tipos = $modelo->obtenerTiposActividad();
		$solicitudes = $modelo->obtenerSolicitudesPendientesParticipacion($id);
		$participantes = $modelo->obtenerParticipantes($id);
		$restricciones = $this->calcularRestricciones($actividad);
		
		$ahora = new DateTime();
		$inicio = new DateTime($actividad['fecha_inicio']);
		$diferencia_horas = ($inicio->getTimestamp() - $ahora->getTimestamp()) / 3600;
		$tiene_miembros = ($modelo->contarMiembrosExcluyendoCreador($id) > 0);
		$restricciones['bloquear_nombre_desc'] = $tiene_miembros && $diferencia_horas < 24;
		$restricciones['bloquear_requisitos'] = $tiene_miembros && $diferencia_horas < 24;
		
		// Obtener ubicación del usuario para el mapa
		$modeloUser = new ModeloUsuario();
		$usuarioActual = $modeloUser->obtenerPorId($_SESSION['usuario_id']);
		$user_lat = $usuarioActual['latitud'] ?? null;
		$user_lng = $usuarioActual['longitud'] ?? null;
		
		require_once 'Vistas/Actividad/editar.php';
	}

    public function actualizar() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
			exit;
		}

		$id = (int)($_POST['id_actividad'] ?? 0);
		$modelo = new ModeloActividad();
		$actividad = $modelo->obtenerPorId($id);
		if (!$actividad || $actividad['id_creador'] != $_SESSION['usuario_id']) {
			$_SESSION['error_edicion'] = "No autorizado.";
			header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
			exit;
		}

		// Obtener restricciones actuales
		$restricciones = $this->calcularRestricciones($actividad);
		if ($restricciones['bloquear_todo']) {
			$_SESSION['error_edicion'] = "Esta actividad no se puede editar porque está " . $actividad['estado'];
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// --- Recolección de datos enviados ---
		$datos = [];
		$datos['nombre'] = trim($_POST['nombre'] ?? '');
		$datos['id_tipo'] = (int)($_POST['id_tipo'] ?? 0);
		$datos['descripcion'] = trim($_POST['descripcion'] ?? '');
		$datos['requisitos'] = trim($_POST['requisitos'] ?? '');
		$datos['edad_minima'] = (int)($_POST['edad_minima'] ?? 0);
		$datos['edad_maxima'] = (int)($_POST['edad_maxima'] ?? 99);
		$datos['limite_participantes_min'] = (int)($_POST['limite_participantes_min'] ?? 1);
		$datos['limite_participantes_max'] = !empty($_POST['limite_participantes_max']) ? (int)$_POST['limite_participantes_max'] : null;
		$datos['privacidad'] = $_POST['privacidad'] ?? 'publica';
		$datos['latitud'] = (float)($_POST['latitud'] ?? 0);
		$datos['longitud'] = (float)($_POST['longitud'] ?? 0);
		$datos['fecha_inicio'] = $_POST['fecha_inicio'] ?? '';
		$datos['fecha_fin'] = $_POST['fecha_fin'] ?? '';
		$direccion_manual = trim($_POST['direccion_manual'] ?? ''); // campo de texto editable

		// --- Validaciones generales ---
		if (empty($datos['nombre']) || $datos['id_tipo'] <= 0) {
			$_SESSION['error_edicion'] = "Nombre y tipo son obligatorios.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
		if ($datos['edad_minima'] > $datos['edad_maxima']) {
			$_SESSION['error_edicion'] = "Edad mínima no puede ser mayor que máxima.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
		// Edad mínima no menor de 18 (según reglas)
		if ($datos['edad_minima'] < 18) {
			$_SESSION['error_edicion'] = "La edad mínima permitida es 18 años.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
		// Fechas deben ser válidas y posteriores a ahora
		if (empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
			$_SESSION['error_edicion'] = "Debe especificar fechas de inicio y fin.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
		$fecha_inicio_dt = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $datos['fecha_inicio'])));
		$fecha_fin_dt = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $datos['fecha_fin'])));
		if ($fecha_inicio_dt >= $fecha_fin_dt) {
			$_SESSION['error_edicion'] = "La fecha de inicio debe ser anterior a la de fin.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
		// Fecha inicio debe ser posterior a ahora (a menos que ya esté en curso, pero en ese caso bloquear_todo es true)
		if ($fecha_inicio_dt <= date('Y-m-d H:i:s')) {
			$_SESSION['error_edicion'] = "La fecha de inicio debe ser posterior a la fecha y hora actual.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// --- Validaciones según presencia de miembros (sin contar creador) ---
		$miembros_actuales = $modelo->contarMiembrosExcluyendoCreador($id);
		$tiene_miembros = ($miembros_actuales > 0);

		// 1. Límites
		if ($tiene_miembros) {
			if ($datos['limite_participantes_min'] < $miembros_actuales) {
				$_SESSION['error_edicion'] = "El límite mínimo no puede ser menor a los participantes actuales ({$miembros_actuales}).";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			if ($datos['limite_participantes_max'] !== null && $datos['limite_participantes_max'] < $miembros_actuales) {
				$_SESSION['error_edicion'] = "El límite máximo no puede ser menor a los participantes actuales ({$miembros_actuales}).";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		}
		if ($datos['limite_participantes_min'] < 1) $datos['limite_participantes_min'] = 1;
		if ($datos['limite_participantes_max'] !== null && $datos['limite_participantes_max'] < $datos['limite_participantes_min']) {
			$_SESSION['error_edicion'] = "El límite máximo no puede ser menor que el mínimo.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// 2. Edades mínima y máxima con participantes existentes (excluyendo creador)
		if ($tiene_miembros) {
			$extremos = $modelo->obtenerExtremosEdadParticipantes($id);
			if ($extremos['min'] !== null && $datos['edad_minima'] > $extremos['min']) {
				$_SESSION['error_edicion'] = "La nueva edad mínima no puede superar la edad del participante más joven ({$extremos['min']} años).";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			if ($extremos['max'] !== null && $datos['edad_maxima'] < $extremos['max']) {
				$_SESSION['error_edicion'] = "La nueva edad máxima no puede ser menor que la edad del participante de mayor edad ({$extremos['max']} años).";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		}

		// 3. Restricciones por tiempo restante (si hay miembros)
		$ahora = new DateTime();
		$inicio_original = new DateTime($actividad['fecha_inicio']);
		$diferencia_horas = ($inicio_original->getTimestamp() - $ahora->getTimestamp()) / 3600;

		// Nombre y descripción: solo si faltan >= 24h
		if ($tiene_miembros && $diferencia_horas < 24) {
			if ($datos['nombre'] !== $actividad['nombre']) {
				$_SESSION['error_edicion'] = "No puedes modificar el nombre porque faltan menos de 24 horas para el inicio.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			if ($datos['descripcion'] !== $actividad['descripcion']) {
				$_SESSION['error_edicion'] = "No puedes modificar la descripción porque faltan menos de 24 horas para el inicio.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			// Requisitos: solo una vez y con al menos 24h
			if ($datos['requisitos'] !== $actividad['requisitos']) {
				$_SESSION['error_edicion'] = "Los requisitos solo se pueden modificar una vez y con al menos 24 horas de anticipación.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		}

		// Ubicación y dirección: solo si faltan >= 24h
		if ($tiene_miembros && $diferencia_horas < 24) {
			if ($datos['latitud'] != $actividad['latitud'] || $datos['longitud'] != $actividad['longitud']) {
				$_SESSION['error_edicion'] = "No puedes modificar la ubicación porque faltan menos de 24 horas para el inicio.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			// Dirección manual también bloqueada
			if (!empty($direccion_manual) && $direccion_manual !== $actividad['direccion']) {
				$_SESSION['error_edicion'] = "No puedes modificar la dirección porque faltan menos de 24 horas para el inicio.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		} else if ($tiene_miembros && $diferencia_horas >= 24) {
			// Permitir cambio de ubicación, pero notificar a participantes más adelante
		}

		// Fecha de inicio: modificable solo si faltan >= 48h, con restricción de retraso máximo 24h
		if ($tiene_miembros && $diferencia_horas < 48) {
			if ($fecha_inicio_dt != $actividad['fecha_inicio']) {
				$_SESSION['error_edicion'] = "No puedes modificar la fecha de inicio porque faltan menos de 48 horas.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		} else if ($tiene_miembros && $diferencia_horas >= 48) {
			$nueva_fecha = new DateTime($fecha_inicio_dt);
			$retraso_horas = ($nueva_fecha->getTimestamp() - $inicio_original->getTimestamp()) / 3600;
			if ($retraso_horas > 24) {
				$_SESSION['error_edicion'] = "Solo puedes retrasar la fecha de inicio hasta 24 horas respecto a la original.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			// Solapamiento con otras actividades del creador/organizadores
			if ($modelo->tieneConflictoHorario($_SESSION['usuario_id'], $fecha_inicio_dt, $fecha_fin_dt, $id)) {
				$_SESSION['error_edicion'] = "El nuevo horario entra en conflicto con otra actividad donde eres creador u organizador.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
		}

		// Fecha de fin: siempre que sea > inicio (ya validado) y si está en curso debe ser > ahora
		if ($actividad['estado'] == 'en_curso' && $fecha_fin_dt <= date('Y-m-d H:i:s')) {
			$_SESSION['error_edicion'] = "Si la actividad ya está en curso, la nueva fecha de fin debe ser posterior a la fecha actual.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// Privacidad: solo antes del inicio
		if ($actividad['estado'] != 'pendiente' && $datos['privacidad'] !== $actividad['privacidad']) {
			$_SESSION['error_edicion'] = "No puedes cambiar la privacidad después de que la actividad ha iniciado.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// Tipo de actividad: solo si no ha empezado
		if ($actividad['estado'] != 'pendiente' && $datos['id_tipo'] != $actividad['id_tipo']) {
			$_SESSION['error_edicion'] = "No puedes cambiar el tipo de actividad después de que ha iniciado.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// --- Detectar cambios que requieren cancelación de solicitudes pendientes ---
		$campos_criticos = false;
		if ($fecha_inicio_dt != $actividad['fecha_inicio'] ||
			$datos['latitud'] != $actividad['latitud'] ||
			$datos['longitud'] != $actividad['longitud'] ||
			$datos['privacidad'] != $actividad['privacidad'] ||
			$datos['limite_participantes_max'] != $actividad['limite_participantes_max'] ||
			$datos['limite_participantes_min'] != $actividad['limite_participantes_min']) {
			$campos_criticos = true;
		}

		// Si hay cambios críticos y la actividad tiene solicitudes pendientes, mostrar advertencia (ya debe haberse pedido confirmación en JS)
		if ($campos_criticos && isset($_POST['confirmar_cancelacion']) && $_POST['confirmar_cancelacion'] == '1') {
			// Cancelar solicitudes pendientes
			$modelo->cancelarSolicitudesPendientes($id);
			// Notificar a los usuarios afectados
			$solicitudes_afectadas = $modelo->obtenerSolicitudesPendientesParticipacion($id); // ya no habrá porque se cancelaron
			// (Opcional) se puede notificar individualmente
		} elseif ($campos_criticos && !isset($_POST['confirmar_cancelacion'])) {
			// Debería llegar el flag desde el frontend, si no, error
			$_SESSION['error_edicion'] = "Debes confirmar la cancelación de solicitudes pendientes.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}

		// --- Procesar imagen (si se subió una nueva) ---
		$foto_blob = null;
		$cambio_imagen = false;
		if (isset($_FILES['foto_actividad']) && $_FILES['foto_actividad']['error'] === UPLOAD_ERR_OK) {
			$cambio_imagen = true;
			if ($_FILES['foto_actividad']['size'] > 5 * 1024 * 1024) {
				$_SESSION['error_edicion'] = "La imagen no puede superar los 5MB.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			$tipo = mime_content_type($_FILES['foto_actividad']['tmp_name']);
			if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
				$_SESSION['error_edicion'] = "Formato no válido. Use JPG, PNG o WEBP.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			$foto_blob = file_get_contents($_FILES['foto_actividad']['tmp_name']);
		}

		// --- Guardar dirección manual (si se permite) ---
		if (!empty($direccion_manual) && $direccion_manual !== $actividad['direccion']) {
			// Verificar restricción de ubicación (si ya pasó la validación de tiempo)
			if ($tiene_miembros && $diferencia_horas < 24) {
				$_SESSION['error_edicion'] = "No puedes modificar la dirección porque faltan menos de 24 horas.";
				header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
				exit;
			}
			$db = Database::getConexion();
			$sqlDir = "UPDATE actividades SET direccion = :dir WHERE id_actividad = :id";
			$stmtDir = $db->prepare($sqlDir);
			$stmtDir->execute([':dir' => $direccion_manual, ':id' => $id]);
		}

		// --- Actualizar datos básicos de la actividad ---
		$datos_actualizar = [
			'nombre' => $datos['nombre'],
			'id_tipo' => $datos['id_tipo'],
			'descripcion' => $datos['descripcion'],
			'requisitos' => $datos['requisitos'],
			'edad_minima' => $datos['edad_minima'],
			'edad_maxima' => $datos['edad_maxima'],
			'limite_participantes_min' => $datos['limite_participantes_min'],
			'limite_participantes_max' => $datos['limite_participantes_max'],
			'latitud' => $datos['latitud'],
			'longitud' => $datos['longitud'],
			'fecha_inicio' => $fecha_inicio_dt,
			'fecha_fin' => $fecha_fin_dt,
			'privacidad' => $datos['privacidad']
		];

		if ($modelo->actualizarActividad($id, $datos_actualizar, $foto_blob)) {
			$_SESSION['exito_edicion'] = "Actividad actualizada correctamente.";

			// --- Notificaciones según cambios ---
			$modeloNotif = new ModeloNotificacion();
			$participantes = $modelo->obtenerTodosParticipantesConEmail($id);

			// Notificar a organizadores si solo se modificaron cosas menores (sin miembros)
			if (!$tiene_miembros) {
				foreach ($participantes as $p) {
					if ($p['rol'] == 'organizador' || $p['rol'] == 'creador') {
						$modeloNotif->crear(
							$p['id_usuario'],
							'actividad',
							'Actividad modificada',
							"La actividad '{$actividad['nombre']}' ha sido actualizada por el creador.",
							'?c=actividad&a=detalle&id=' . $id
						);
					}
				}
			} else {
				// Notificar a todos los participantes por cambios específicos
				$notificar_todos = false;
				if ($fecha_inicio_dt != $actividad['fecha_inicio']) $notificar_todos = true;
				if ($datos['latitud'] != $actividad['latitud'] || $datos['longitud'] != $actividad['longitud']) $notificar_todos = true;
				if ($datos['privacidad'] != $actividad['privacidad']) $notificar_todos = true;
				if ($datos['limite_participantes_max'] != $actividad['limite_participantes_max']) $notificar_todos = true;
				if ($datos['limite_participantes_min'] != $actividad['limite_participantes_min']) $notificar_todos = true;
				if ($cambio_imagen) $notificar_todos = true;
				if ($datos['nombre'] != $actividad['nombre']) $notificar_todos = true;
				if ($datos['descripcion'] != $actividad['descripcion']) $notificar_todos = true;
				if ($datos['requisitos'] != $actividad['requisitos']) $notificar_todos = true;
				if ($datos['edad_minima'] != $actividad['edad_minima']) $notificar_todos = true;
				if ($datos['edad_maxima'] != $actividad['edad_maxima']) $notificar_todos = true;

				if ($notificar_todos) {
					foreach ($participantes as $p) {
						$modeloNotif->crear(
							$p['id_usuario'],
							'actividad',
							'Cambios en la actividad',
							"La actividad '{$actividad['nombre']}' ha sido modificada. Revisa los detalles.",
							'?c=actividad&a=detalle&id=' . $id
						);
					}
				} elseif ($campos_criticos) {
					// Ya se notificó a los afectados por cancelación de solicitudes
				}
			}

			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		} else {
			$_SESSION['error_edicion'] = "Error al actualizar la actividad.";
			header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
			exit;
		}
	}
	
	public function aceptarSolicitud() {
		$this->responderSolicitud('aceptar');
	}

	public function rechazarSolicitud() {
		$this->responderSolicitud('rechazar');
	}

	private function responderSolicitud($accion) {
		if (!isset($_SESSION['usuario_id'])) {
			http_response_code(401);
			echo json_encode(['error' => 'No autorizado']);
			exit;
		}
		$id_actividad = (int)($_POST['id_actividad'] ?? 0);
		$id_usuario = (int)($_POST['id_usuario'] ?? 0);
		if (!$id_actividad || !$id_usuario) {
			http_response_code(400);
			echo json_encode(['error' => 'Datos inválidos']);
			exit;
		}
		// Verificar que el usuario actual sea el creador de la actividad
		$modeloAct = new ModeloActividad();
		$actividad = $modeloAct->obtenerPorId($id_actividad);
		if (!$actividad || $actividad['id_creador'] != $_SESSION['usuario_id']) {
			http_response_code(403);
			echo json_encode(['error' => 'No tienes permiso']);
			exit;
		}
		$modeloAct->responderSolicitudParticipacion($id_actividad, $id_usuario, $accion);
		// Notificar al solicitante
		$modeloNotif = new ModeloNotificacion();
		$mensaje = ($accion === 'aceptar') ? "Tu solicitud para la actividad '{$actividad['nombre']}' ha sido aceptada." : "Tu solicitud para la actividad '{$actividad['nombre']}' ha sido rechazada.";
		$modeloNotif->crear($id_usuario, 'actividad', 'Respuesta a tu solicitud', $mensaje, '?c=actividad&a=detalle&id=' . $id_actividad);
		
		echo json_encode(['success' => true]);
		exit;
	}

    // Eliminar actividad (solo si está finalizada o cancelada)
    public function eliminarActividad() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        $modelo = new ModeloActividad();
        $actividad = $modelo->obtenerPorId($id);
        if (!$actividad || $actividad['id_creador'] != $_SESSION['usuario_id']) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
            exit;
        }
        if (!in_array($actividad['estado'], ['finalizada', 'cancelada'])) {
            $_SESSION['error_edicion'] = "Solo se pueden eliminar actividades finalizadas o canceladas.";
            header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
            exit;
        }
        if ($modelo->eliminarActividad($id)) {
            $_SESSION['exito_edicion'] = "Actividad eliminada.";
        } else {
            $_SESSION['error_edicion'] = "Error al eliminar.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=edicion');
        exit;
    }

    // Helper privado para calcular restricciones de edición
    private function calcularRestricciones($actividad) {
        $modelo = new ModeloActividad();
        $miembros = $modelo->contarMiembros($actividad['id_actividad']);
        $ahora = new DateTime();
        $inicio = new DateTime($actividad['fecha_inicio']);
        $diferenciaHoras = ($inicio->getTimestamp() - $ahora->getTimestamp()) / 3600;
        $participantesActuales = $modelo->contarParticipantesAceptados($actividad['id_actividad']);
        $maxAlcanzado = ($actividad['limite_participantes_max'] !== null && $participantesActuales >= $actividad['limite_participantes_max']);
        
        // Si no hay miembros, se puede editar todo (sin restricciones de tiempo ni límites)
        $hayMiembros = ($miembros > 0);
        
        return [
            'bloquear_todo' => in_array($actividad['estado'], ['finalizada', 'en_curso', 'cancelada']),
            'bloquear_fechas' => $hayMiembros && ($diferenciaHoras <= 6),
            'bloquear_ubicacion' => $hayMiembros && ($diferenciaHoras <= 6),
            'solo_aumentar_max' => $hayMiembros && $maxAlcanzado,
            'participantes_actuales' => $participantesActuales,
            'hay_miembros' => $hayMiembros
        ];
    }
	
	public function actualizarDireccion() {
		if (!isset($_SESSION['usuario_id'])) {
			http_response_code(401);
			echo json_encode(['error' => 'No autorizado']);
			exit;
		}
		$id_actividad = (int)($_POST['id_actividad'] ?? 0);
		$direccion = trim($_POST['direccion'] ?? '');
		if (!$id_actividad || empty($direccion)) {
			http_response_code(400);
			echo json_encode(['error' => 'Datos inválidos']);
			exit;
		}
		$modelo = new ModeloActividad();
		$actividad = $modelo->obtenerPorId($id_actividad);
		if (!$actividad) {
			http_response_code(404);
			echo json_encode(['error' => 'Actividad no encontrada']);
			exit;
		}
		// Solo el creador puede actualizar la dirección
		if ($actividad['id_creador'] != $_SESSION['usuario_id']) {
			http_response_code(403);
			echo json_encode(['error' => 'No tienes permiso para modificar esta actividad']);
			exit;
		}
		// Solo actualizar si el campo dirección está vacío o nulo
		if (!empty($actividad['direccion'])) {
			echo json_encode(['success' => false, 'message' => 'La actividad ya tiene dirección']);
			exit;
		}
		$db = Database::getConexion();
		$sql = "UPDATE actividades SET direccion = :direccion WHERE id_actividad = :id";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute([':direccion' => $direccion, ':id' => $id_actividad]);
		echo json_encode(['success' => $result]);
		exit;
	}
}
?>
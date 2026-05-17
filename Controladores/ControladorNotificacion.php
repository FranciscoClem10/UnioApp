<?php
require_once 'Modelos/ModeloNotificacion.php';
require_once 'Modelos/ModeloUsuario.php';

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
	
	/**
	 * Marca una notificación como leída y redirige a su enlace original
	 */
	public function click() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$id_notificacion = (int)($_GET['id'] ?? 0);
		if ($id_notificacion <= 0) {
			header('Location: ' . BASE_URL . '?c=notificacion');
			exit;
		}
		$modelo = new ModeloNotificacion();
		$notificacion = $modelo->obtenerPorId($id_notificacion, $_SESSION['usuario_id']);
		if (!$notificacion) {
			header('Location: ' . BASE_URL . '?c=notificacion');
			exit;
		}
		// Marcar como leída
		$modelo->marcarLeida($id_notificacion, $_SESSION['usuario_id']);
		// Redirigir al enlace original
		$enlace = $notificacion['enlace'];
		if (empty($enlace)) {
			$enlace = BASE_URL . '?c=notificacion';
		} else {
			if (strpos($enlace, 'http') !== 0 && strpos($enlace, '//') !== 0) {
				$enlace = BASE_URL . ltrim($enlace, '/');
			}
		}
		header('Location: ' . $enlace);
		exit;
	}

	/**
	 * Acepta o rechaza una solicitud de amistad desde una notificación
	 */
	public function responderSolicitud() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$id_notificacion = (int)($_GET['id_notif'] ?? 0);
		$respuesta = $_GET['respuesta'] ?? ''; // 'aceptar' o 'rechazar'
		if ($id_notificacion <= 0 || !in_array($respuesta, ['aceptar', 'rechazar'])) {
			$_SESSION['error_notificacion'] = "Datos inválidos.";
			header('Location: ' . BASE_URL . '?c=notificacion');
			exit;
		}
		$modeloNotif = new ModeloNotificacion();
		$notificacion = $modeloNotif->obtenerPorId($id_notificacion, $_SESSION['usuario_id']);
		if (!$notificacion || $notificacion['tipo'] !== 'solicitud_amistad') {
			$_SESSION['error_notificacion'] = "Notificación no válida.";
			header('Location: ' . BASE_URL . '?c=notificacion');
			exit;
		}
		
		// Extraer el ID del solicitante desde la notificación
		$id_solicitante = $this->_extraerIdSolicitanteDesdeNotificacion($notificacion, $_SESSION['usuario_id']);
		if (!$id_solicitante) {
			$_SESSION['error_notificacion'] = "No se pudo identificar al solicitante.";
			header('Location: ' . BASE_URL . '?c=notificacion');
			exit;
		}
		
		// Procesar la respuesta usando ModeloUsuario
		$modeloUser = new ModeloUsuario();
		$exito = $modeloUser->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $respuesta);
		if ($exito) {
			$_SESSION['mensaje_notificacion'] = "Solicitud $respuesta exitosamente.";
			// Marcar la notificación como leída
			$modeloNotif->marcarLeida($id_notificacion, $_SESSION['usuario_id']);
			// Crear notificación de respuesta para el solicitante
			$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Un usuario';
			$titulo = ($respuesta === 'aceptar') ? 'Solicitud de amistad aceptada' : 'Solicitud de amistad rechazada';
			$contenido = ($respuesta === 'aceptar') ? "$nombreUsuario ha aceptado tu solicitud de amistad." : "$nombreUsuario ha rechazado tu solicitud de amistad.";
			$enlace = ($respuesta === 'aceptar') ? '?c=amigos' : null;
			$modeloNotif->crear($id_solicitante, 'amistad', $titulo, $contenido, $enlace);
		} else {
			$_SESSION['error_notificacion'] = "Error al procesar la solicitud.";
		}
		header('Location: ' . BASE_URL . '?c=notificacion');
		exit;
	}

	/**
	 * Extrae el ID del usuario que envió la solicitud a partir de la notificación.
	 * Soporta notificaciones nuevas (con id_solicitante en el enlace) y antiguas (parseando el contenido).
	 */
	private function _extraerIdSolicitanteDesdeNotificacion($notificacion, $id_usuario_actual) {
		// Intento 1: extraer de enlace (para notificaciones nuevas)
		if (!empty($notificacion['enlace']) && preg_match('/[?&]id_solicitante=(\d+)/', $notificacion['enlace'], $matches)) {
			return (int)$matches[1];
		}
		
		// Intento 2: parsear el contenido para notificaciones antiguas
		// Formato esperado: "Nombre Apellido te ha enviado una solicitud de amistad."
		$contenido = $notificacion['contenido'];
		if (preg_match('/^(.+?) te ha enviado una solicitud de amistad\./', $contenido, $matches)) {
			$nombreCompleto = trim($matches[1]);
			$db = Database::getConexion();
			$partes = explode(' ', $nombreCompleto);
			$nombre = $partes[0];
			$apellidoPaterno = $partes[1] ?? '';
			$apellidoMaterno = $partes[2] ?? '';
			
			$sql = "SELECT u.id_usuario FROM usuarios u
					JOIN amistades a ON (a.id_solicitante = u.id_usuario AND a.id_receptor = :id_actual)
					WHERE u.activo = 1 
					  AND u.nombre = :nombre
					  AND (u.apellido_paterno = :ap_paterno OR :ap_paterno = '')
					  AND (u.apellido_materno = :ap_materno OR :ap_materno = '')
					  AND a.estado = 'pendiente'
					LIMIT 1";
			$stmt = $db->prepare($sql);
			$stmt->execute([
				':id_actual' => $id_usuario_actual,
				':nombre' => $nombre,
				':ap_paterno' => $apellidoPaterno,
				':ap_materno' => $apellidoMaterno
			]);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result) return $result['id_usuario'];
			
			// Fallback: solo por nombre (menos preciso)
			$sql2 = "SELECT u.id_usuario FROM usuarios u
					 JOIN amistades a ON (a.id_solicitante = u.id_usuario AND a.id_receptor = :id_actual)
					 WHERE u.activo = 1 AND u.nombre = :nombre AND a.estado = 'pendiente'";
			$stmt2 = $db->prepare($sql2);
			$stmt2->execute([':id_actual' => $id_usuario_actual, ':nombre' => $nombre]);
			$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			return $result2 ? $result2['id_usuario'] : null;
		}
		return null;
	}
}
?>
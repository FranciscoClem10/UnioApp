<?php
require_once 'Modelos/ModeloUsuario.php';
class ControladorPerfil {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $usuario = $modelo->obtenerPorId($_SESSION['usuario_id']);
        if (!$usuario) {
            session_destroy();
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        // Añadir campo 'apellidos' para compatibilidad con vistas
        $usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
        // Convertir foto a base64 si existe
        if (!empty($usuario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $usuario['foto_perfil']);
            finfo_close($finfo);
            $usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
        } else {
            $usuario['foto_base64'] = null;
        }
        $amigos = $modelo->obtenerAmigos($_SESSION['usuario_id']);
        // Añadir 'apellidos' a cada amigo para compatibilidad
        foreach ($amigos as &$amigo) {
            $amigo['apellidos'] = trim($amigo['apellido_paterno'] . ' ' . $amigo['apellido_materno']);
        }
        $solicitudes = $modelo->obtenerSolicitudesPendientes($_SESSION['usuario_id']);
        foreach ($solicitudes as &$sol) {
            $sol['apellidos'] = trim($sol['apellido_paterno'] . ' ' . $sol['apellido_materno']);
        }
        require_once 'Vistas/Perfil/index.php';
    }

	public function editar() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$modeloUsuario = new ModeloUsuario();
		$usuario = $modeloUsuario->obtenerPorId($_SESSION['usuario_id']);
		if (!$usuario) {
			header('Location: ' . BASE_URL . '?c=perfil');
			exit;
		}
		// Datos básicos
		$usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
		// Foto base64
		if (!empty($usuario['foto_perfil'])) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_buffer($finfo, $usuario['foto_perfil']);
			finfo_close($finfo);
			$usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
		} else {
			$usuario['foto_base64'] = null;
		}
		// Obtener tipos de actividad (intereses)
		require_once 'Modelos/ModeloActividad.php'; // si no está incluido
		$modeloActividad = new ModeloActividad();
		$tipos_actividad = $modeloActividad->obtenerTiposActividad();
		// Intereses actuales del usuario
		$intereses_usuario = $modeloUsuario->obtenerIntereses($_SESSION['usuario_id']); // devuelve array de ids

		require_once 'Vistas/Perfil/editar.php';
	}

    public function actualizar() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: ' . BASE_URL . '?c=perfil&a=editar');
			exit;
		}

		// Recoger datos del formulario
		$nombre = trim($_POST['nombre'] ?? '');
		$apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
		$apellido_materno = trim($_POST['apellido_materno'] ?? '');
		$telefono = trim($_POST['telefono'] ?? '');
		$genero = $_POST['genero'] ?? 'Prefiero no decir';
		$biografia = trim($_POST['biografia'] ?? '');
		$latitud = isset($_POST['latitud']) && $_POST['latitud'] !== '' ? (float)$_POST['latitud'] : null;
		$longitud = isset($_POST['longitud']) && $_POST['longitud'] !== '' ? (float)$_POST['longitud'] : null;

		// Validaciones obligatorias
		if (empty($nombre) || empty($apellido_paterno)) {
			$_SESSION['error_perfil'] = "Nombre y apellido paterno son obligatorios.";
			header('Location: ' . BASE_URL . '?c=perfil&a=editar');
			exit;
		}

		// Manejo de la fecha de nacimiento (solo mes y día, año fijo)
		$modelo = new ModeloUsuario();
		$usuario_actual = $modelo->obtenerPorId($_SESSION['usuario_id']);
		if (!$usuario_actual) {
			session_destroy();
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}

		$anio = date('Y', strtotime($usuario_actual['fecha_nacimiento']));
		$mes = $_POST['mes_nacimiento'] ?? date('m');
		$dia = $_POST['dia_nacimiento'] ?? date('d');

		// Validar que la fecha sea real
		if (!checkdate((int)$mes, (int)$dia, (int)$anio)) {
			$_SESSION['error_perfil'] = "La fecha de nacimiento no es válida.";
			header('Location: ' . BASE_URL . '?c=perfil&a=editar');
			exit;
		}
		$fecha_nacimiento = "$anio-$mes-$dia";

		// Procesar foto de perfil (opcional)
		$foto_blob = null;
		if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
			// Validar tamaño máximo (5 MB)
			if ($_FILES['foto_perfil']['size'] > 5 * 1024 * 1024) {
				$_SESSION['error_perfil'] = "La foto no puede superar los 5 MB.";
				header('Location: ' . BASE_URL . '?c=perfil&a=editar');
				exit;
			}
			// Validar tipo MIME
			$tipo = mime_content_type($_FILES['foto_perfil']['tmp_name']);
			if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
				$_SESSION['error_perfil'] = "Formato de imagen no válido. Use JPG, PNG o WEBP.";
				header('Location: ' . BASE_URL . '?c=perfil&a=editar');
				exit;
			}
			$foto_blob = file_get_contents($_FILES['foto_perfil']['tmp_name']);
		}

		// Construir array de datos para actualizar
		$datos = [
			'nombre' => $nombre,
			'apellido_paterno' => $apellido_paterno,
			'apellido_materno' => $apellido_materno,
			'telefono' => $telefono,
			'fecha_nacimiento' => $fecha_nacimiento,
			'genero' => $genero,
			'biografia' => $biografia,
			'latitud' => $latitud,
			'longitud' => $longitud
		];

		// Actualizar perfil en la base de datos
		if ($modelo->actualizarPerfil($_SESSION['usuario_id'], $datos, $foto_blob)) {
			// Actualizar intereses (categorías de actividad)
			$intereses = isset($_POST['intereses']) ? $_POST['intereses'] : [];
			$modelo->actualizarIntereses($_SESSION['usuario_id'], $intereses);

			$_SESSION['mensaje_perfil'] = "Perfil actualizado correctamente.";
			header('Location: ' . BASE_URL . '?c=perfil&a=editar');
			exit;
		} else {
			$_SESSION['error_perfil'] = "Error al actualizar el perfil. Inténtalo de nuevo.";
			header('Location: ' . BASE_URL . '?c=perfil&a=editar');
			exit;
		}
	}

    // Mostrar página de nuevos amigos (búsqueda y solicitudes pendientes)
    public function nuevosAmigos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $solicitudes = $modelo->obtenerSolicitudesPendientes($_SESSION['usuario_id']);
        foreach ($solicitudes as &$sol) {
            $sol['apellidos'] = trim($sol['apellido_paterno'] . ' ' . $sol['apellido_materno']);
        }
        $resultados = [];
        if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
            $termino = trim($_GET['buscar']);
            $resultados = $modelo->buscarUsuariosConRelacion($_SESSION['usuario_id'], $termino);
            foreach ($resultados as &$usr) {
                $usr['apellidos'] = trim($usr['apellido_paterno'] . ' ' . $usr['apellido_materno']);
            }
        }
        require_once 'Vistas/Perfil/nuevosAmigos.php';
    }

    // Enviar solicitud de amistad (vía POST)
    public function enviarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
            exit;
        }
        $id_receptor = (int)($_POST['id_usuario'] ?? 0);
        if ($id_receptor <= 0) {
            $_SESSION['error_amigos'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        $resultado = $modelo->enviarSolicitudAmistad($_SESSION['usuario_id'], $id_receptor);
        if ($resultado === true) {
            $_SESSION['mensaje_amigos'] = "Solicitud enviada.";
        } else {
            $_SESSION['error_amigos'] = "No se pudo enviar la solicitud (quizás ya existe).";
        }
        header('Location: ' . BASE_URL . '?c=perfil&a=nuevosAmigos');
        exit;
    }

    // Aceptar solicitud de amistad
    public function aceptarSolicitud() {
        $this->responder('aceptado');
    }

    // Rechazar solicitud
    public function rechazarSolicitud() {
        $this->responder('rechazado');
    }

    private function responder($estado) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $id_solicitante = (int)($_POST['id_solicitante'] ?? 0);
        if ($id_solicitante <= 0) {
            $_SESSION['error_perfil'] = "Datos inválidos.";
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $estado)) {
            $_SESSION['mensaje_perfil'] = "Solicitud " . ($estado == 'aceptado' ? "aceptada" : "rechazada");
        } else {
            $_SESSION['error_perfil'] = "Error al procesar la solicitud.";
        }
        header('Location: ' . BASE_URL . '?c=perfil');
        exit;
    }
}
?>
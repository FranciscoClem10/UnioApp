<?php
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloNotificacion.php';
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
        // Obtener datos adicionales necesarios para la vista
        $tipos = $modelo->obtenerTiposActividad();
        $organizadores = $modelo->obtenerOrganizadores($id);
        $solicitudes = $modelo->obtenerSolicitudesPendientes($id);
        $participantes = $modelo->obtenerParticipantesAceptados($id);
        $invitaciones = $modelo->obtenerInvitaciones($id);
        // Verificar restricciones de edición
        $restricciones = $this->calcularRestricciones($actividad);
        require_once 'Vistas/Actividad/editar.php';
    }

    // Procesar actualización de datos básicos de la actividad
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
        $restricciones = $this->calcularRestricciones($actividad);
        if ($restricciones['bloquear_todo']) {
            $_SESSION['error_edicion'] = "Esta actividad no se puede editar porque está " . $actividad['estado'];
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
            exit;
        }

        // Recolectar datos básicos
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

        // Validaciones básicas
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

        // Ubicación (solo si no está bloqueada)
        if (!$restricciones['bloquear_ubicacion']) {
            $datos['latitud'] = (float)($_POST['latitud'] ?? 0);
            $datos['longitud'] = (float)($_POST['longitud'] ?? 0);
        } else {
            $datos['latitud'] = $actividad['latitud'];
            $datos['longitud'] = $actividad['longitud'];
        }

        // Fechas (solo si no están bloqueadas)
        if (!$restricciones['bloquear_fechas']) {
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            if (empty($fecha_inicio) || empty($fecha_fin)) {
                $_SESSION['error_edicion'] = "Fechas inválidas.";
                header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
                exit;
            }
            $datos['fecha_inicio'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha_inicio)));
            $datos['fecha_fin'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha_fin)));
            if ($datos['fecha_inicio'] >= $datos['fecha_fin']) {
                $_SESSION['error_edicion'] = "La fecha de inicio debe ser anterior a la de fin.";
                header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
                exit;
            }
        } else {
            $datos['fecha_inicio'] = $actividad['fecha_inicio'];
            $datos['fecha_fin'] = $actividad['fecha_fin'];
        }

        // Validación de límites según participantes actuales y miembros
        $participantesActuales = $modelo->contarParticipantesAceptados($id);
        if ($restricciones['hay_miembros']) {
            if ($datos['limite_participantes_min'] < $participantesActuales) {
                $_SESSION['error_edicion'] = "El mínimo de participantes no puede ser menor a los ya confirmados ($participantesActuales).";
                header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
                exit;
            }
            if ($datos['limite_participantes_max'] !== null && $datos['limite_participantes_max'] < $participantesActuales) {
                $_SESSION['error_edicion'] = "El máximo no puede ser menor a los confirmados.";
                header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
                exit;
            }
        }
        if ($datos['limite_participantes_min'] < 1) $datos['limite_participantes_min'] = 1;
        if ($datos['limite_participantes_max'] !== null && $datos['limite_participantes_max'] < $datos['limite_participantes_min']) {
            $_SESSION['error_edicion'] = "El máximo no puede ser menor que el mínimo.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
            exit;
        }

        // Procesar foto
        $foto_blob = null;
        if (isset($_FILES['foto_actividad']) && $_FILES['foto_actividad']['error'] === UPLOAD_ERR_OK) {
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

        if ($datos['estado'] == 'cancelada' && $actividad['estado'] != 'cancelada') {
            $sqlPart = "SELECT id_usuario FROM participantes WHERE id_actividad = :id_act AND estado = 'aceptado'";
            $stmtPart = $db->prepare($sqlPart);
            $stmtPart->execute([':id_act' => $id_actividad]);
            $participantes = $stmtPart->fetchAll(PDO::FETCH_COLUMN);
            foreach ($participantes as $id_part) {
                $modeloNotif->crear(
                    $id_part,
                    'actividad',
                    'Actividad cancelada',
                    'La actividad "' . $actividad['nombre'] . '" ha sido cancelada.',
                    '?c=actividad&a=detalle&id=' . $id_actividad
                );
            }
        }
        
        if ($modelo->actualizarActividad($id, $datos, $foto_blob)) {
            $_SESSION['exito_edicion'] = "Actividad actualizada correctamente.";
        } else {
            $_SESSION['error_edicion'] = "Error al actualizar la actividad.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id);
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

    // --- Acciones para gestionar organizadores, solicitudes, invitaciones ---
    public function agregarOrganizador() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        $modelo = new ModeloActividad();
        $actividad = $modelo->obtenerPorId($id_actividad);
        if ($actividad['id_creador'] != $_SESSION['usuario_id']) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($id_usuario <= 0) {
            $_SESSION['error_edicion'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modelo->agregarOrganizador($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Organizador agregado.";
        } else {
            $_SESSION['error_edicion'] = "Error al agregar organizador.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }
    

    public function quitarOrganizador() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);
        $modelo = new ModeloActividad();
        $actividad = $modelo->obtenerPorId($id_actividad);
        if ($actividad['id_creador'] != $_SESSION['usuario_id']) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modelo->quitarOrganizador($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Organizador removido.";
        } else {
            $_SESSION['error_edicion'] = "Error al remover organizador.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function aceptarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);
        $modelo = new ModeloActividad();

        // Verificar permisos
        if (!$modelo->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        // Obtener datos de la actividad (para el nombre en la notificación)
        $actividad = $modelo->obtenerPorId($id_actividad);
        if (!$actividad) {
            $_SESSION['error_edicion'] = "Actividad no encontrada.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        // Cambiar estado
        if ($modelo->cambiarEstadoParticipante($id_actividad, $id_usuario, 'aceptado')) {
            $_SESSION['exito_edicion'] = "Solicitud aceptada.";

            //Notificación de ACEPTACIÓN
            $modeloNotif = new ModeloNotificacion();
            $modeloNotif->crear(
                $id_usuario,
                'actividad',
                'Solicitud aceptada',
                'Tu solicitud para la actividad "' . htmlspecialchars($actividad['nombre']) . '" ha sido ACEPTADA.',
                '?c=actividad&a=detalle&id=' . $id_actividad
            );
        } else {
            $_SESSION['error_edicion'] = "Error al aceptar.";
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }
    
    public function rechazarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);
        $modelo = new ModeloActividad();

        if (!$modelo->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        $actividad = $modelo->obtenerPorId($id_actividad);
        if (!$actividad) {
            $_SESSION['error_edicion'] = "Actividad no encontrada.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        if ($modelo->cambiarEstadoParticipante($id_actividad, $id_usuario, 'rechazado')) {
            $_SESSION['exito_edicion'] = "Solicitud rechazada.";

            // ✅ Notificación de RECHAZO
            $modeloNotif = new ModeloNotificacion();
            $modeloNotif->crear(
                $id_usuario,
                'actividad',
                'Solicitud rechazada',
                'Tu solicitud para la actividad "' . htmlspecialchars($actividad['nombre']) . '" ha sido RECHAZADA.',
                null
            );
        } else {
            $_SESSION['error_edicion'] = "Error al rechazar.";
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function expulsarParticipante() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);
        $modelo = new ModeloActividad();
        if (!$modelo->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modelo->expulsarParticipante($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Participante expulsado.";
        } else {
            $_SESSION['error_edicion'] = "Error al expulsar.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    // Enviar invitación a usuario registrado o email externo
    public function enviarInvitacion() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $tipo = $_POST['tipo_invitacion'] ?? '';
        $destinatario = trim($_POST['destinatario'] ?? '');
        $modelo = new ModeloActividad();
        if (!$modelo->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($tipo === 'usuario') {
            $id_usuario = (int)$destinatario;
            $resultado = $modelo->invitarUsuario($id_actividad, $_SESSION['usuario_id'], $id_usuario);
        } elseif ($tipo === 'email') {
            $resultado = $modelo->invitarEmail($id_actividad, $_SESSION['usuario_id'], $destinatario);
        } else {
            $resultado = false;
        }
        if ($resultado) {
            $_SESSION['exito_edicion'] = "Invitación enviada.";
        } else {
            $_SESSION['error_edicion'] = "Error al enviar invitación.";
        }

        $modeloNotif->crear(
            $id_invitado,
            'invitacion',
            'Invitación a actividad',
            'Has sido invitado a la actividad "' . $actividad['nombre'] . '".',
            '?c=actividad&a=detalle&id=' . $id_actividad
        );
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

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

    public function buscarAmigos() {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
    $term = $_GET['term'] ?? '';
    if (strlen($term) < 2) {
        echo json_encode([]);
        exit;
    }
    $modelo = new ModeloActividad();
    $amigos = $modelo->buscarAmigosParaInvitacion($_SESSION['usuario_id'], $term);
    // Agregar nombre_completo a cada resultado
    foreach ($amigos as &$a) {
        $a['nombre_completo'] = trim(($a['nombre'] ?? '') . ' ' . ($a['apellido_paterno'] ?? '') . ' ' . ($a['apellido_materno'] ?? ''));
    }
    header('Content-Type: application/json');
    echo json_encode($amigos);
    exit;
}

}
?>
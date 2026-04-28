<?php
class ModeloActividad {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    public function obtenerTodasVisibles($usuario_id = null) {
    // Si no se proporciona, intentar obtener de sesión
    if ($usuario_id === null && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    }

    $params = [];
    $sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
                   a.edad_minima, a.limite_participantes_max AS limite_personas,
                   a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
                   a.id_creador, ta.nombre_tipo AS categoria,
                   a.fecha_inicio AS fecha_proxima,
                   DATE_FORMAT(a.fecha_inicio, '%%H:%%i:%%s') AS hora_proxima
            FROM actividades a
            INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
            WHERE a.estado IN ('pendiente', 'en_curso')
              AND a.privacidad != 'privada'";

    if ($usuario_id) {
        $sql .= " AND a.id_creador != :usuario_id";
        $params[':usuario_id'] = $usuario_id;
    }

    // Aplicar filtro de bloqueo solo si hay usuario logueado
    if ($usuario_id) {
        $sql .= " AND NOT EXISTS (
                      SELECT 1 FROM amistades bl
                      WHERE bl.estado = 'bloqueado'
                        AND bl.id_solicitante = :usuario_actual
                        AND bl.id_receptor = a.id_creador
                  )";
        $params[':usuario_actual'] = $usuario_id;
    }

    $sql .= " ORDER BY a.fecha_inicio ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar fechas y tipos de acceso (igual que antes)
    foreach ($actividades as &$act) {
        $act['fecha'] = $act['fecha_proxima'] ? date('Y-m-d', strtotime($act['fecha_proxima'])) : null;
        $act['hora'] = $act['hora_proxima'] ?? null;

        switch ($act['tipo_acceso']) {
            case 'publica': $act['tipo_acceso'] = 'todos'; break;
            case 'privada': $act['tipo_acceso'] = 'invitacion'; break;
            case 'por_aprobacion': $act['tipo_acceso'] = 'aprobado'; break;
            default: $act['tipo_acceso'] = 'todos';
        }
    }

    return $actividades;
}

    // Actividades creadas por el usuario
    public function obtenerPorCreador($usuario_id) {
        $sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
                       a.edad_minima, a.limite_participantes_max AS limite_personas,
                       a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
                       ta.nombre_tipo AS categoria,
                       a.fecha_inicio AS fecha_proxima,
                       DATE_FORMAT(a.fecha_inicio, '%H:%i:%s') AS hora_proxima
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                WHERE a.id_creador = :id_creador
                ORDER BY a.fecha_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_creador' => $usuario_id]);
        $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($actividades as &$act) {
            $act['fecha'] = $act['fecha_proxima'] ? date('Y-m-d', strtotime($act['fecha_proxima'])) : null;
            $act['hora'] = $act['hora_proxima'] ?? null;
            switch ($act['tipo_acceso']) {
                case 'publica': $act['tipo_acceso'] = 'todos'; break;
                case 'privada': $act['tipo_acceso'] = 'invitacion'; break;
                case 'por_aprobacion': $act['tipo_acceso'] = 'aprobado'; break;
                default: $act['tipo_acceso'] = 'todos';
            }
        }
        return $actividades;
    }

    public function obtenerEstadisticas() {
        $sql = "SELECT COUNT(*) as total FROM actividades WHERE estado IN ('pendiente', 'en_curso')";
        $stmt = $this->db->query($sql);
        $total = $stmt->fetchColumn();

        $sqlCat = "SELECT ta.nombre_tipo, COUNT(*) as count 
                   FROM actividades a 
                   INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                   WHERE a.estado IN ('pendiente', 'en_curso')
                   GROUP BY ta.id_tipo";
        $stmtCat = $this->db->query($sqlCat);
        $porCategoria = $stmtCat->fetchAll(PDO::FETCH_KEY_PAIR);
        return ['total' => $total, 'por_categoria' => $porCategoria];
    }

    public function obtenerPorId($id_actividad) {
        $sql = "SELECT a.*, t.nombre_tipo,
                       u.nombre AS creador_nombre, u.apellido_paterno AS creador_apellido_paterno, 
                       u.apellido_materno AS creador_apellido_materno
                FROM actividades a
                LEFT JOIN tipos_actividad t ON a.id_tipo = t.id_tipo
                LEFT JOIN usuarios u ON a.id_creador = u.id_usuario
                WHERE a.id_actividad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($actividad && !empty($actividad['foto_actividad'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $actividad['foto_actividad']);
            finfo_close($finfo);
            $actividad['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($actividad['foto_actividad']);
        } else {
            $actividad['foto_base64'] = null;
        }
        return $actividad;
    }


    public function obtenerTiposActividad() {
        $sql = "SELECT id_tipo, nombre_tipo FROM tipos_actividad ORDER BY nombre_tipo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearActividad($datos, $foto_blob = null) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO actividades (id_tipo, id_creador, nombre, descripcion, requisitos, 
                     edad_minima, edad_maxima, limite_participantes_min, limite_participantes_max, 
                     latitud, longitud, fecha_inicio, fecha_fin, foto_actividad, privacidad, estado)
                    VALUES (:id_tipo, :id_creador, :nombre, :descripcion, :requisitos, 
                            :edad_minima, :edad_maxima, :limite_min, :limite_max, 
                            :latitud, :longitud, :fecha_inicio, :fecha_fin, :foto, :privacidad, 'pendiente')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_tipo', $datos['id_tipo']);
            $stmt->bindParam(':id_creador', $datos['id_creador']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':requisitos', $datos['requisitos']);
            $stmt->bindParam(':edad_minima', $datos['edad_minima']);
            $stmt->bindParam(':edad_maxima', $datos['edad_maxima']);
            $stmt->bindParam(':limite_min', $datos['limite_participantes_min']);
            $stmt->bindParam(':limite_max', $datos['limite_participantes_max']);
            $stmt->bindParam(':latitud', $datos['latitud']);
            $stmt->bindParam(':longitud', $datos['longitud']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            $stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
            $stmt->bindParam(':privacidad', $datos['privacidad']);
            $stmt->execute();

            $id_actividad = $this->db->lastInsertId();

            // Insertar al creador como participante
            $sqlParticipante = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado)
                                VALUES (:id_actividad, :id_usuario, 'creador', 'aceptado')";
            $stmtPart = $this->db->prepare($sqlParticipante);
            $stmtPart->execute([
                ':id_actividad' => $id_actividad,
                ':id_usuario' => $datos['id_creador']
            ]);

            $this->db->commit();
            return $id_actividad;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al crear actividad: " . $e->getMessage());
            $_SESSION['error_crear_actividad'] = "Error en la base de datos: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Obtiene todos los datos para la vista de detalle (usando fecha_inicio/fin)
     */
    public function obtenerDetalleCompleto($id_actividad) {
        // Se usa CONCAT_WS para omitir espacios si algún apellido está vacío o nulo
        $sql = "SELECT a.*, ta.nombre_tipo AS categoria, 
                       CONCAT_WS(' ', u.nombre, u.apellido_paterno, u.apellido_materno) AS organizador_nombre,
                       u.id_usuario AS organizador_id,
                       a.fecha_creacion AS fecha_publicacion
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                INNER JOIN usuarios u ON a.id_creador = u.id_usuario
                WHERE a.id_actividad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$actividad) return null;

        // Formatear fechas directamente de los campos de la actividad
        $actividad['fecha_inicio'] = date('Y-m-d', strtotime($actividad['fecha_inicio']));
        $actividad['hora_inicio'] = date('H:i', strtotime($actividad['fecha_inicio']));
        $actividad['fecha_fin'] = date('Y-m-d', strtotime($actividad['fecha_fin']));
        $actividad['hora_fin'] = date('H:i', strtotime($actividad['fecha_fin']));

        // Contar participantes confirmados
        $sqlConfirmados = "SELECT COUNT(*) as total 
                           FROM participantes 
                           WHERE id_actividad = :id AND estado = 'aceptado'";
        $stmtConf = $this->db->prepare($sqlConfirmados);
        $stmtConf->execute([':id' => $id_actividad]);
        $actividad['asistentes_confirmados'] = (int)$stmtConf->fetchColumn();

        // Capacidad
        $actividad['capacidad_max'] = $actividad['limite_participantes_max'] ?? 'Sin límite';
        $actividad['capacidad_min'] = $actividad['limite_participantes_min'] ?? 1;

        // Tipo de acceso legible
        switch ($actividad['privacidad']) {
            case 'publica': $actividad['tipo_acceso_legible'] = 'Pública (cualquiera puede unirse)'; break;
            case 'privada': $actividad['tipo_acceso_legible'] = 'Privada (solo invitados)'; break;
            case 'por_aprobacion': $actividad['tipo_acceso_legible'] = 'Por aprobación (requiere autorización)'; break;
            default: $actividad['tipo_acceso_legible'] = 'No especificado';
        }

        // Requisitos como array
        $actividad['requisitos_array'] = !empty($actividad['requisitos']) ? explode("\n", $actividad['requisitos']) : [];
        $actividad['incluye_array'] = [];

        // Participantes pendientes/invitados
        $sqlExtra = "SELECT COUNT(*) as extra 
                     FROM participantes 
                     WHERE id_actividad = :id AND estado IN ('pendiente', 'invitado')";
        $stmtExtra = $this->db->prepare($sqlExtra);
        $stmtExtra->execute([':id' => $id_actividad]);
        $actividad['asistentes_extra'] = (int)$stmtExtra->fetchColumn();

        // Fecha publicación
        $actividad['hora_publicacion'] = date('H:i', strtotime($actividad['fecha_publicacion']));
        $actividad['fecha_publicacion'] = date('Y-m-d', strtotime($actividad['fecha_publicacion']));

        // Coordenadas
        $actividad['lat'] = $actividad['latitud'];
        $actividad['lng'] = $actividad['longitud'];
        $actividad['direccion'] = "Coordenadas: {$actividad['latitud']}, {$actividad['longitud']}";

        return $actividad;
    }

    // Reseñas (con ajuste de nombre de usuario)
    public function obtenerResenas($id_actividad) {
        $sql = "SELECT r.*, 
                       CONCAT_WS(' ', u.nombre, u.apellido_paterno, u.apellido_materno) AS usuario_nombre, 
                       u.foto_perfil
                FROM resenas r
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_actividad = :id
                ORDER BY r.fecha_resena DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function puedeResenar($id_actividad, $id_usuario) {
        $sqlParticipante = "SELECT 1 FROM participantes 
                            WHERE id_actividad = :id_act AND id_usuario = :id_user AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sqlParticipante);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        if (!$stmt->fetchColumn()) return false;

        $sqlAct = "SELECT estado FROM actividades WHERE id_actividad = :id";
        $stmtAct = $this->db->prepare($sqlAct);
        $stmtAct->execute([':id' => $id_actividad]);
        $estado = $stmtAct->fetchColumn();
        if ($estado !== 'finalizada') return false;

        $sqlResena = "SELECT 1 FROM resenas WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmtRes = $this->db->prepare($sqlResena);
        $stmtRes->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        return !$stmtRes->fetchColumn();
    }

    public function guardarResena($id_actividad, $id_usuario, $calificacion, $comentario) {
        $sql = "INSERT INTO resenas (id_actividad, id_usuario, calificacion, comentario) 
                VALUES (:id_act, :id_user, :cal, :com)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_act' => $id_actividad,
            ':id_user' => $id_usuario,
            ':cal' => $calificacion,
            ':com' => $comentario
        ]);
    }

    // Obtener organizadores de una actividad (rol = 'organizador')
    public function obtenerOrganizadores($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.foto_perfil
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.rol = 'organizador' AND p.estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orgs as &$o) {
            $o['nombre_completo'] = trim($o['nombre'] . ' ' . $o['apellido_paterno'] . ' ' . $o['apellido_materno']);
            if (!empty($o['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $o['foto_perfil']);
                finfo_close($finfo);
                $o['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($o['foto_perfil']);
            }
        }
        return $orgs;
    }

    // Obtener solicitudes pendientes (estado = 'pendiente')
    public function obtenerSolicitudesPendientes($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, p.fecha_solicitud
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.estado = 'pendiente' AND p.rol = 'miembro'
                ORDER BY p.fecha_solicitud ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($solicitudes as &$s) {
            $s['nombre_completo'] = trim($s['nombre'] . ' ' . $s['apellido_paterno'] . ' ' . $s['apellido_materno']);
            if (!empty($s['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $s['foto_perfil']);
                finfo_close($finfo);
                $s['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($s['foto_perfil']);
            }
        }
        return $solicitudes;
    }

    // Obtener participantes aceptados (incluyendo creador y organizadores)
    public function obtenerParticipantesAceptados($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, p.rol
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.estado = 'aceptado'
                ORDER BY FIELD(p.rol, 'creador', 'organizador', 'miembro')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($participantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
            if (!empty($p['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $p['foto_perfil']);
                finfo_close($finfo);
                $p['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($p['foto_perfil']);
            }
        }
        return $participantes;
    }

    // Obtener invitaciones enviadas (para mostrar en edición)
    public function obtenerInvitaciones($id_actividad) {
        $sql = "SELECT i.*, 
                    CASE WHEN i.id_invitado_usuario IS NOT NULL THEN u.nombre ELSE NULL END as invitado_nombre,
                    CASE WHEN i.id_invitado_usuario IS NOT NULL THEN u.email ELSE i.email_invitado END as contacto
                FROM invitaciones i
                LEFT JOIN usuarios u ON i.id_invitado_usuario = u.id_usuario
                WHERE i.id_actividad = :id
                ORDER BY i.fecha_invitacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar participantes aceptados
    public function contarParticipantesAceptados($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }

    // Actualizar datos de actividad (respetando restricciones desde controlador)
    public function actualizarActividad($id, $datos, $foto_blob = null) {
        try {
            $sql = "UPDATE actividades SET 
                    nombre = :nombre,
                    id_tipo = :id_tipo,
                    descripcion = :descripcion,
                    requisitos = :requisitos,
                    edad_minima = :edad_minima,
                    edad_maxima = :edad_maxima,
                    limite_participantes_min = :limite_min,
                    limite_participantes_max = :limite_max,
                    latitud = :latitud,
                    longitud = :longitud,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    privacidad = :privacidad";
            if ($foto_blob !== null) {
                $sql .= ", foto_actividad = :foto";
            }
            $sql .= " WHERE id_actividad = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':id_tipo', $datos['id_tipo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':requisitos', $datos['requisitos']);
            $stmt->bindParam(':edad_minima', $datos['edad_minima']);
            $stmt->bindParam(':edad_maxima', $datos['edad_maxima']);
            $stmt->bindParam(':limite_min', $datos['limite_participantes_min']);
            $stmt->bindParam(':limite_max', $datos['limite_participantes_max']);
            $stmt->bindParam(':latitud', $datos['latitud']);
            $stmt->bindParam(':longitud', $datos['longitud']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            $stmt->bindParam(':privacidad', $datos['privacidad']);
            $stmt->bindParam(':id', $id);
            if ($foto_blob !== null) {
                $stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar actividad: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar actividad (solo si finalizada o cancelada)
    public function eliminarActividad($id) {
        $sql = "DELETE FROM actividades WHERE id_actividad = :id AND estado IN ('finalizada', 'cancelada')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Agregar organizador (usuario debe ser participante aceptado o se le agrega automáticamente)
    public function agregarOrganizador($id_actividad, $id_usuario) {
        // Verificar si ya es participante
        $sqlCheck = "SELECT 1 FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        if ($stmt->fetchColumn()) {
            $sql = "UPDATE participantes SET rol = 'organizador', estado = 'aceptado' 
                    WHERE id_actividad = :id_act AND id_usuario = :id_user";
        } else {
            $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) 
                    VALUES (:id_act, :id_user, 'organizador', 'aceptado')";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Quitar organizador (cambiar rol a 'miembro' si ya era participante, o eliminarlo si no)
    public function quitarOrganizador($id_actividad, $id_usuario) {
        $sql = "UPDATE participantes SET rol = 'miembro' 
                WHERE id_actividad = :id_act AND id_usuario = :id_user AND rol = 'organizador'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Cambiar estado de un participante (pendiente -> aceptado/rechazado)
    public function cambiarEstadoParticipante($id_actividad, $id_usuario, $nuevo_estado) {
        $sql = "UPDATE participantes SET estado = :estado 
                WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Expulsar participante (eliminar registro)
    public function expulsarParticipante($id_actividad, $id_usuario) {
        $sql = "DELETE FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user AND rol != 'creador'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Verificar si un usuario es creador u organizador de la actividad
    public function esOrganizadorOCreador($id_actividad, $id_usuario) {
        $sql = "SELECT 1 FROM participantes 
                WHERE id_actividad = :id_act AND id_usuario = :id_user 
                AND (rol = 'creador' OR rol = 'organizador') AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        return $stmt->fetchColumn() ? true : false;
    }

    // Invitar a usuario registrado
    public function invitarUsuario($id_actividad, $id_invitador, $id_invitado) {
        // Verificar que no sea ya participante
        $sqlCheck = "SELECT 1 FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_invitado]);
        if ($stmt->fetchColumn()) return false;
        
        // Insertar en participantes con estado 'invitado'
        $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) 
                VALUES (:id_act, :id_user, 'miembro', 'invitado')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_invitado]);
        // Guardar en invitaciones
        $sqlInv = "INSERT INTO invitaciones (id_actividad, id_invitador, id_invitado_usuario, estado) 
                VALUES (:id_act, :id_inv, :id_user, 'pendiente')";
        $stmtInv = $this->db->prepare($sqlInv);
        return $stmtInv->execute([':id_act' => $id_actividad, ':id_inv' => $id_invitador, ':id_user' => $id_invitado]);
    }

    // Invitar por correo electrónico (usuario no registrado)
    public function invitarEmail($id_actividad, $id_invitador, $email) {
        $sql = "INSERT INTO invitaciones (id_actividad, id_invitador, email_invitado, estado) 
                VALUES (:id_act, :id_inv, :email, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_inv' => $id_invitador, ':email' => $email]);
    }

    // Buscar amigos del creador/organizadores para invitarlos (opcional)
    public function buscarAmigosParaInvitacion($id_usuario, $termino) {
    $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email
            FROM amistades a
            INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
            WHERE (a.id_solicitante = :id_self OR a.id_receptor = :id_self)
              AND a.estado = 'aceptado'
              AND u.id_usuario != :id_self
              AND (u.nombre LIKE :term OR u.apellido_paterno LIKE :term OR u.email LIKE :term)
            LIMIT 10";
    $stmt = $this->db->prepare($sql);
    $term = "%$termino%";
    $stmt->execute([':id_self' => $id_usuario, ':term' => $term]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Contar miembros (participantes con rol 'miembro' y estado 'aceptado')
    public function contarMiembros($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND rol = 'miembro' AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }

    // Verificar si un usuario es participante aceptado (incluye creador/organizador)
    public function esParticipanteActivo($id_actividad, $id_usuario) {
        $sql = "SELECT 1 FROM participantes 
                WHERE id_actividad = :id_actividad AND id_usuario = :id_usuario 
                  AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad, ':id_usuario' => $id_usuario]);
        return (bool) $stmt->fetchColumn();
    }

    // Obtener participantes de la actividad (para mostrar en el chat)
    public function obtenerParticipantes($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil,
                       p.rol
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id_actividad AND p.estado = 'aceptado'
                ORDER BY FIELD(p.rol, 'creador', 'organizador', 'miembro'), u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad]);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($participantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
            if (!empty($p['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $p['foto_perfil']);
                finfo_close($finfo);
                $p['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($p['foto_perfil']);
            } else {
                $p['foto_base64'] = null;
            }
        }
        return $participantes;
    }

}
?>
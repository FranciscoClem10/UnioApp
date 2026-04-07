<?php
class ModeloActividad {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Obtener actividades visibles para el dashboard (usa fecha_inicio directamente)
    public function obtenerTodasVisibles($usuario_id) {
        $sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
                       a.edad_minima, a.limite_participantes_max AS limite_personas,
                       a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
                       a.id_creador, ta.nombre_tipo AS categoria,
                       a.fecha_inicio AS fecha_proxima,
                       DATE_FORMAT(a.fecha_inicio, '%H:%i:%s') AS hora_proxima
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                WHERE a.estado IN ('pendiente', 'en_curso')
                  AND a.privacidad != 'privada'
                ORDER BY a.fecha_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
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
        $sql = "SELECT a.*, ta.nombre_tipo AS categoria 
                FROM actividades a 
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                WHERE a.id_actividad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTiposActividad() {
        $sql = "SELECT id_tipo, nombre_tipo FROM tipos_actividad ORDER BY nombre_tipo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva actividad con fecha_inicio y fecha_fin
     */
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
}
?>
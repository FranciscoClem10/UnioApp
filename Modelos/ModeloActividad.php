<?php
class ModeloActividad {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    public function obtenerTodasVisibles($usuario_id) {
        $sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
                       a.edad_minima, a.limite_participantes_max AS limite_personas,
                       a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
                       a.id_creador, ta.nombre_tipo AS categoria,
                       (SELECT fecha_inicio FROM instancias_actividad 
                        WHERE id_actividad = a.id_actividad AND cancelada = 0 
                        ORDER BY fecha_inicio ASC LIMIT 1) AS fecha_proxima,
                       (SELECT DATE_FORMAT(fecha_inicio, '%H:%i:%s') FROM instancias_actividad 
                        WHERE id_actividad = a.id_actividad AND cancelada = 0 
                        ORDER BY fecha_inicio ASC LIMIT 1) AS hora_proxima
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                WHERE a.estado IN ('pendiente', 'en_curso')
                  AND a.privacidad != 'privada'
                ORDER BY fecha_proxima ASC";
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

    public function obtenerPorCreador($usuario_id) {
        $sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
                       a.edad_minima, a.limite_participantes_max AS limite_personas,
                       a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
                       ta.nombre_tipo AS categoria,
                       (SELECT fecha_inicio FROM instancias_actividad 
                        WHERE id_actividad = a.id_actividad AND cancelada = 0 
                        ORDER BY fecha_inicio ASC LIMIT 1) AS fecha_proxima,
                       (SELECT DATE_FORMAT(fecha_inicio, '%H:%i:%s') FROM instancias_actividad 
                        WHERE id_actividad = a.id_actividad AND cancelada = 0 
                        ORDER BY fecha_inicio ASC LIMIT 1) AS hora_proxima
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                WHERE a.id_creador = :id_creador
                ORDER BY fecha_proxima ASC";
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
     * Crea una nueva actividad y asigna al creador como participante con rol 'creador'
     * @param array $datos Datos del formulario (nombre, descripcion, id_tipo, requisitos, edad_minima, edad_maxima,
     *                     limite_participantes_min, limite_participantes_max, latitud, longitud, privacidad, id_creador)
     * @param string|null $foto_blob Contenido binario de la foto (opcional)
     * @return int|false ID de la actividad creada o false en caso de error
     */
    public function crearActividad($datos, $foto_blob = null) {
        try {
            // Iniciar transacción para asegurar integridad
            $this->db->beginTransaction();

            // 1. Insertar en actividades
            $sql = "INSERT INTO actividades (id_tipo, id_creador, nombre, descripcion, requisitos, 
                     edad_minima, edad_maxima, limite_participantes_min, limite_participantes_max, 
                     latitud, longitud, foto_actividad, privacidad, estado)
                    VALUES (:id_tipo, :id_creador, :nombre, :descripcion, :requisitos, 
                            :edad_minima, :edad_maxima, :limite_min, :limite_max, 
                            :latitud, :longitud, :foto, :privacidad, 'pendiente')";
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
            $stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
            $stmt->bindParam(':privacidad', $datos['privacidad']);
            $stmt->execute();

            $id_actividad = $this->db->lastInsertId();

            // 2. Insertar en participantes con rol 'creador' y estado 'aceptado'
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
}
?>
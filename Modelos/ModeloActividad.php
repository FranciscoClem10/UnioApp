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
}
?>
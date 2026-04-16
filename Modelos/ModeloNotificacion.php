<?php
class ModeloNotificacion {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    /**
     * Crea una nueva notificación para un usuario
     * @param int $id_usuario
     * @param string $tipo (ej: 'solicitud_amistad', 'respuesta_actividad', 'invitacion', 'mensaje', 'actividad_actualizada')
     * @param string $titulo
     * @param string $contenido
     * @param string|null $enlace URL relativa (ej: '?c=actividad&a=detalle&id=5')
     * @return bool
     */
    public function crear($id_usuario, $tipo, $titulo, $contenido, $enlace = null) {

        $sql = "INSERT INTO notificaciones (id_usuario, tipo, titulo, contenido, enlace) 
                VALUES (:id_usuario, :tipo, :titulo, :contenido, :enlace)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':tipo' => $tipo,
            ':titulo' => $titulo,
            ':contenido' => $contenido,
            ':enlace' => $enlace
        ]);

    }

    public function marcarLeidasPorContexto($id_usuario, $tipo, $referencia) {
        // Para mensajes de actividad, la referencia es el id_actividad; se guarda en el campo enlace
        // Buscamos notificaciones con ese tipo y cuyo enlace contenga el id
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_usuario = :id_user 
                AND tipo = :tipo 
                AND enlace LIKE :ref 
                AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $ref = '%' . $referencia . '%';
        return $stmt->execute([':id_user' => $id_usuario, ':tipo' => $tipo, ':ref' => $ref]);
    }

    /**
     * Obtiene las notificaciones de un usuario (todas, ordenadas por fecha descendente)
     * @param int $id_usuario
     * @param int $limite
     * @return array
     */
    public function obtenerTodas($id_usuario, $limite = 100) {
        $sql = "SELECT * FROM notificaciones 
                WHERE id_usuario = :id_usuario 
                ORDER BY fecha_creacion DESC 
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene solo las notificaciones no leídas de un usuario
     * @param int $id_usuario
     * @return array
     */
    public function obtenerNoLeidas($id_usuario) {
        $sql = "SELECT * FROM notificaciones 
                WHERE id_usuario = :id_usuario AND leida = 0 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marca una notificación como leída (solo si pertenece al usuario)
     * @param int $id_notificacion
     * @param int $id_usuario
     * @return bool
     */
    public function marcarLeida($id_notificacion, $id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_notificacion = :id_not AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_not' => $id_notificacion, ':id_user' => $id_usuario]);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas
     * @param int $id_usuario
     * @return bool
     */
    public function marcarTodasLeidas($id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_user' => $id_usuario]);
    }

    /**
     * Cuenta las notificaciones no leídas de un usuario
     * @param int $id_usuario
     * @return int
     */
    public function contarNoLeidas($id_usuario) {
        $sql = "SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :id_user AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario]);
        return (int)$stmt->fetchColumn();
    }
}
?>
<?php
class ModeloNotificacion {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

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

    public function marcarLeidasPorTipos($id_usuario, $tipos) {
        if (empty($tipos)) return true;
        $placeholders = implode(',', array_fill(0, count($tipos), '?'));
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_usuario = ? AND tipo IN ($placeholders) AND leida = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_merge([$id_usuario], $tipos));
    }

    public function marcarLeidasPorContexto($id_usuario, $tipo, $referencia) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_usuario = :id_user 
                AND tipo = :tipo 
                AND enlace LIKE :ref 
                AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $ref = '%' . $referencia . '%';
        return $stmt->execute([':id_user' => $id_usuario, ':tipo' => $tipo, ':ref' => $ref]);
    }

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

    public function obtenerNoLeidas($id_usuario) {
        $sql = "SELECT * FROM notificaciones 
                WHERE id_usuario = :id_usuario AND leida = 0 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarLeida($id_notificacion, $id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_notificacion = :id_not AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_not' => $id_notificacion, ':id_user' => $id_usuario]);
    }

    public function marcarTodasLeidas($id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_user' => $id_usuario]);
    }

    public function contarNoLeidas($id_usuario) {
        $sql = "SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :id_user AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario]);
        return (int)$stmt->fetchColumn();
    }
}
?>
<?php
class ModeloMensaje {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // ================== MENSAJES DE ACTIVIDAD (tabla mensajes) ==================

    public function obtenerConversacionesActividad($id_usuario) {
        // Obtener actividades donde el usuario es participante aceptado
        $sql = "SELECT DISTINCT a.id_actividad, a.nombre AS titulo, a.foto_actividad,
                       (SELECT COUNT(*) FROM mensajes m WHERE m.id_actividad = a.id_actividad AND m.fecha_envio > COALESCE(
                           (SELECT MAX(fecha_lectura) FROM mensajes WHERE id_actividad = a.id_actividad AND id_usuario = :id_user2), '1900-01-01')
                       ) AS no_leidos
                FROM participantes p
                INNER JOIN actividades a ON p.id_actividad = a.id_actividad
                WHERE p.id_usuario = :id_user AND p.estado = 'aceptado'
                ORDER BY (SELECT MAX(fecha_envio) FROM mensajes WHERE id_actividad = a.id_actividad) DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario, ':id_user2' => $id_usuario]);
        $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($conversaciones as &$c) {
            if (!empty($c['foto_actividad'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $c['foto_actividad']);
                finfo_close($finfo);
                $c['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($c['foto_actividad']);
            } else {
                $c['foto_base64'] = null;
            }
        }
        return $conversaciones;
    }

    public function obtenerMensajesActividad($id_actividad, $id_usuario) {
        // Marcar como leídos los mensajes de esta actividad para este usuario
        $sqlUpdate = "UPDATE mensajes SET leido = 1 WHERE id_actividad = :id_act AND id_usuario != :id_user";
        $stmtUp = $this->db->prepare($sqlUpdate);
        $stmtUp->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);

        $sql = "SELECT m.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes m
                INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_actividad = :id_act
                ORDER BY m.fecha_envio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad]);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($mensajes as &$m) {
            $m['nombre_completo'] = trim($m['nombre'] . ' ' . $m['apellido_paterno'] . ' ' . $m['apellido_materno']);
            if (!empty($m['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $m['foto_perfil']);
                finfo_close($finfo);
                $m['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($m['foto_perfil']);
            } else {
                $m['foto_base64'] = null;
            }
        }
        return $mensajes;
    }

    public function guardarMensajeActividad($id_actividad, $id_usuario, $contenido) {
        $sql = "INSERT INTO mensajes (id_actividad, id_usuario, contenido) VALUES (:id_act, :id_user, :cont)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_act' => $id_actividad,
            ':id_user' => $id_usuario,
            ':cont' => $contenido
        ]);
    }

    public function eliminarMensajeActividad($id_mensaje, $id_usuario) {
        // Solo el autor puede eliminar su propio mensaje (o admin, pero no implementado)
        $sql = "DELETE FROM mensajes WHERE id_mensaje = :id AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id_mensaje, ':id_user' => $id_usuario]);
    }

    // ================== MENSAJES PRIVADOS (tabla mensajes_privados) ==================

    public function obtenerConversacionesPrivadas($id_usuario) {
        // Obtener amigos con los que se ha chateado o simplemente todos los amigos
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil,
                       (SELECT COUNT(*) FROM mensajes_privados mp 
                        WHERE ((mp.id_remitente = u.id_usuario AND mp.id_destinatario = :id_user) 
                            OR (mp.id_remitente = :id_user AND mp.id_destinatario = u.id_usuario))
                          AND mp.leido = 0 
                          AND mp.id_destinatario = :id_user) AS no_leidos,
                       (SELECT MAX(fecha_envio) FROM mensajes_privados 
                        WHERE (id_remitente = u.id_usuario AND id_destinatario = :id_user) 
                           OR (id_remitente = :id_user AND id_destinatario = u.id_usuario)) AS ultimo_mensaje
                FROM amistades a
                INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
                WHERE (a.id_solicitante = :id_user OR a.id_receptor = :id_user)
                  AND a.estado = 'aceptado'
                  AND u.id_usuario != :id_user
                ORDER BY ultimo_mensaje DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario]);
        $amigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($amigos as &$a) {
            $a['nombre_completo'] = trim($a['nombre'] . ' ' . $a['apellido_paterno'] . ' ' . $a['apellido_materno']);
            if (!empty($a['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $a['foto_perfil']);
                finfo_close($finfo);
                $a['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($a['foto_perfil']);
            } else {
                $a['foto_base64'] = null;
            }
        }
        return $amigos;
    }

    public function obtenerMensajesPrivados($id_usuario, $id_amigo) {
        // Marcar como leídos los mensajes donde el destinatario es el usuario actual
        $sqlUpdate = "UPDATE mensajes_privados SET leido = 1, fecha_lectura = NOW()
                      WHERE id_remitente = :id_amigo AND id_destinatario = :id_user AND leido = 0";
        $stmtUp = $this->db->prepare($sqlUpdate);
        $stmtUp->execute([':id_amigo' => $id_amigo, ':id_user' => $id_usuario]);

        // Obtener mensajes (no eliminados para el usuario actual)
        $sql = "SELECT mp.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes_privados mp
                INNER JOIN usuarios u ON mp.id_remitente = u.id_usuario
                WHERE ((mp.id_remitente = :id_user AND mp.id_destinatario = :id_amigo AND mp.eliminado_remitente = 0)
                    OR (mp.id_remitente = :id_amigo AND mp.id_destinatario = :id_user AND mp.eliminado_destinatario = 0))
                ORDER BY mp.fecha_envio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario, ':id_amigo' => $id_amigo]);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($mensajes as &$m) {
            $m['nombre_completo'] = trim($m['nombre'] . ' ' . $m['apellido_paterno'] . ' ' . $m['apellido_materno']);
            if (!empty($m['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $m['foto_perfil']);
                finfo_close($finfo);
                $m['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($m['foto_perfil']);
            } else {
                $m['foto_base64'] = null;
            }
        }
        return $mensajes;
    }

    public function guardarMensajePrivado($id_remitente, $id_destinatario, $contenido) {
        // Verificar que son amigos (estado aceptado)
        $sqlCheck = "SELECT 1 FROM amistades WHERE (id_solicitante = :r AND id_receptor = :d AND estado = 'aceptado')
                     OR (id_solicitante = :d AND id_receptor = :r AND estado = 'aceptado')";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':r' => $id_remitente, ':d' => $id_destinatario]);
        if (!$stmtCheck->fetchColumn()) {
            return false;
        }
        $sql = "INSERT INTO mensajes_privados (id_remitente, id_destinatario, contenido) VALUES (:rem, :des, :cont)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':rem' => $id_remitente,
            ':des' => $id_destinatario,
            ':cont' => $contenido
        ]);
    }

    public function eliminarMensajePrivado($id_mensaje, $id_usuario) {
        // Marcar como eliminado según quien lo borra
        $sql = "SELECT id_remitente, id_destinatario FROM mensajes_privados WHERE id_mensaje = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_mensaje]);
        $msg = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$msg) return false;

        if ($msg['id_remitente'] == $id_usuario) {
            $sqlUp = "UPDATE mensajes_privados SET eliminado_remitente = 1 WHERE id_mensaje = :id";
        } elseif ($msg['id_destinatario'] == $id_usuario) {
            $sqlUp = "UPDATE mensajes_privados SET eliminado_destinatario = 1 WHERE id_mensaje = :id";
        } else {
            return false;
        }
        $stmtUp = $this->db->prepare($sqlUp);
        return $stmtUp->execute([':id' => $id_mensaje]);
    }
}
?>
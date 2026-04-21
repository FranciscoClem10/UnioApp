<?php
class ModeloMensajeGrupo {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }
	
	public function obtenerConversacionesActividad($id_usuario) {
    $sql = "SELECT 
                a.id_actividad,
                a.nombre AS nombre_actividad,
                a.foto_actividad,
                (SELECT contenido FROM mensajes m WHERE m.id_actividad = a.id_actividad ORDER BY m.fecha_envio DESC LIMIT 1) AS ultimo_mensaje,
                (SELECT MAX(fecha_envio) FROM mensajes WHERE id_actividad = a.id_actividad) AS ultima_fecha,
                (SELECT COUNT(*) FROM mensajes m WHERE m.id_actividad = a.id_actividad AND m.id_usuario != :id_usuario AND m.leido = 0) AS no_leidos
            FROM participantes p
            INNER JOIN actividades a ON p.id_actividad = a.id_actividad
            WHERE p.id_usuario = :id_usuario AND p.estado = 'aceptado'
            GROUP BY a.id_actividad
            ORDER BY ultima_fecha DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id_usuario' => $id_usuario]);
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
		


    // Obtener mensajes de una actividad (con datos del usuario)
    public function obtenerMensajesActividad($id_actividad, $limite = 50) {
        $sql = "SELECT m.id_mensaje, m.id_actividad, m.id_usuario, m.contenido, m.fecha_envio, m.leido,
                       u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes m
                INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_actividad = :id_actividad
                ORDER BY m.fecha_envio DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_actividad', $id_actividad, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Procesar fotos en base64 y nombres completos
        foreach ($mensajes as &$msg) {
            $msg['nombre_completo'] = trim($msg['nombre'] . ' ' . $msg['apellido_paterno'] . ' ' . $msg['apellido_materno']);
            if (!empty($msg['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $msg['foto_perfil']);
                finfo_close($finfo);
                $msg['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($msg['foto_perfil']);
            } else {
                $msg['foto_base64'] = null;
            }
            unset($msg['foto_perfil']);
        }
        return array_reverse($mensajes); // más recientes al final
    }

    // Obtener mensajes nuevos (a partir de un id)
    public function obtenerNuevosMensajes($id_actividad, $ultimo_id = 0) {
        $sql = "SELECT m.id_mensaje, m.id_actividad, m.id_usuario, m.contenido, m.fecha_envio, m.leido,
                       u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes m
                INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_actividad = :id_actividad AND m.id_mensaje > :ultimo_id
                ORDER BY m.fecha_envio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad, ':ultimo_id' => $ultimo_id]);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($mensajes as &$msg) {
            $msg['nombre_completo'] = trim($msg['nombre'] . ' ' . $msg['apellido_paterno'] . ' ' . $msg['apellido_materno']);
            if (!empty($msg['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $msg['foto_perfil']);
                finfo_close($finfo);
                $msg['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($msg['foto_perfil']);
            } else {
                $msg['foto_base64'] = null;
            }
            unset($msg['foto_perfil']);
        }
        return $mensajes;
    }

    // Enviar mensaje
    public function enviarMensaje($id_actividad, $id_usuario, $contenido) {
        $sql = "INSERT INTO mensajes (id_actividad, id_usuario, contenido, fecha_envio) 
                VALUES (:id_actividad, :id_usuario, :contenido, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_actividad' => $id_actividad,
            ':id_usuario'   => $id_usuario,
            ':contenido'    => trim($contenido)
        ]);
    }

    // Marcar mensajes como leídos (todos los de la actividad para el usuario actual)
    public function marcarLeidosActividad($id_actividad, $id_usuario) {
        $sql = "UPDATE mensajes SET leido = 1, fecha_lectura = NOW()
                WHERE id_actividad = :id_actividad AND id_usuario != :id_usuario AND leido = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_actividad' => $id_actividad, ':id_usuario' => $id_usuario]);
    }

    // Contar mensajes no leídos de una actividad (excepto los propios)
    public function contarNoLeidosActividad($id_actividad, $id_usuario) {
        $sql = "SELECT COUNT(*) FROM mensajes 
                WHERE id_actividad = :id_actividad AND id_usuario != :id_usuario AND leido = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad, ':id_usuario' => $id_usuario]);
        return (int) $stmt->fetchColumn();
    }
}
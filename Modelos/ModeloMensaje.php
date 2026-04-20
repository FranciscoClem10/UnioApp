<?php
require_once 'Modelos/Database.php';

class ModeloMensaje {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    public function obtenerConversaciones($id_usuario) {
        // Subconsulta para obtener el último mensaje de cada conversación
        $sql = "
            SELECT 
                u.id_usuario,
                u.nombre,
                u.apellido_paterno,
                u.apellido_materno,
                u.foto_perfil,
                u.ultima_conexion,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
                (SELECT contenido FROM mensajes_privados 
                 WHERE (id_remitente = u.id_usuario AND id_destinatario = :mi_id) 
                    OR (id_remitente = :mi_id AND id_destinatario = u.id_usuario)
                 ORDER BY fecha_envio DESC LIMIT 1) AS ultimo_mensaje,
                (SELECT COUNT(*) FROM mensajes_privados 
                 WHERE id_destinatario = :mi_id AND id_remitente = u.id_usuario AND leido = 0) AS no_leidos
            FROM usuarios u
            WHERE u.id_usuario IN (
                SELECT DISTINCT 
                    CASE 
                        WHEN id_remitente = :mi_id THEN id_destinatario 
                        ELSE id_remitente 
                    END AS otro_id
                FROM mensajes_privados
                WHERE id_remitente = :mi_id OR id_destinatario = :mi_id
            )
            AND u.activo = 1
            ORDER BY (SELECT MAX(fecha_envio) FROM mensajes_privados 
                      WHERE (id_remitente = u.id_usuario AND id_destinatario = :mi_id) 
                         OR (id_remitente = :mi_id AND id_destinatario = u.id_usuario)) DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':mi_id' => $id_usuario]);
        $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Procesar foto_perfil a base64 y determinar online
        $tiempo_limite = 300; // 5 minutos para considerar online
        foreach ($conversaciones as &$c) {
            // Foto base64
            if (!empty($c['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $c['foto_perfil']);
                finfo_close($finfo);
                $c['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($c['foto_perfil']);
            } else {
                $c['foto_base64'] = null;
            }

            // Online?
            if ($c['ultima_conexion']) {
                $ultima = strtotime($c['ultima_conexion']);
                $ahora = time();
                $c['online'] = ($ahora - $ultima) < $tiempo_limite;
            } else {
                $c['online'] = false;
            }
        }

        return $conversaciones;
    }

    public function obtenerMensajes($usuario1, $usuario2, $lastId = 0) {
        $stmt = $this->db->prepare("
            SELECT id_mensaje, id_remitente, id_destinatario, contenido, fecha_envio, leido 
            FROM mensajes_privados 
            WHERE ((id_remitente = ? AND id_destinatario = ?) OR (id_remitente = ? AND id_destinatario = ?))
              AND id_mensaje > ?
            ORDER BY fecha_envio ASC
        ");
        $stmt->execute([$usuario1, $usuario2, $usuario2, $usuario1, $lastId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoLeidos($idDestinatario, $idsMensajes) {
        if (empty($idsMensajes)) return;
        $in = implode(',', array_fill(0, count($idsMensajes), '?'));
        $stmt = $this->db->prepare("UPDATE mensajes_privados SET leido = 1, fecha_lectura = NOW() WHERE id_mensaje IN ($in) AND id_destinatario = ?");
        $params = array_merge($idsMensajes, [$idDestinatario]);
        $stmt->execute($params);
    }

    public function enviarMensaje($remitente, $destinatario, $contenido) {
        // Verificar que el destinatario existe
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ? AND activo = 1");
        $stmt->execute([$destinatario]);
        if (!$stmt->fetch()) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO mensajes_privados (id_remitente, id_destinatario, contenido, fecha_envio) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$remitente, $destinatario, $contenido]);
        $id = $this->db->lastInsertId();

        $stmt = $this->db->prepare("SELECT fecha_envio FROM mensajes_privados WHERE id_mensaje = ?");
        $stmt->execute([$id]);
        $fila = $stmt->fetch();

        return [
            'id_mensaje' => $id,
            'fecha_envio' => $fila['fecha_envio']
        ];
    }



    public function obtenerConversacionesActividad($id_usuario) {
    $sql = "
        SELECT 
            a.id_actividad,
            a.nombre AS nombre_actividad,
            a.foto_actividad,
            (SELECT contenido FROM mensajes 
             WHERE id_actividad = a.id_actividad 
             ORDER BY fecha_envio DESC LIMIT 1) AS ultimo_mensaje,
            (SELECT COUNT(*) FROM mensajes 
             WHERE id_actividad = a.id_actividad 
               AND id_usuario != :mi_id 
               AND leido = 0) AS no_leidos
        FROM participantes p
        INNER JOIN actividades a ON p.id_actividad = a.id_actividad
        WHERE p.id_usuario = :mi_id AND p.estado = 'aceptado'
        ORDER BY (SELECT MAX(fecha_envio) FROM mensajes WHERE id_actividad = a.id_actividad) DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':mi_id' => $id_usuario]);
    $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Procesar foto de actividad a base64
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

// Obtener mensajes de una actividad (con polling)
public function obtenerMensajesActividad($id_actividad, $id_usuario, $lastId = 0) {
    // Marcar como leídos los mensajes de esta actividad (para el usuario)
    $sqlUpdate = "UPDATE mensajes SET leido = 1, fecha_lectura = NOW() 
                  WHERE id_actividad = ? AND id_usuario != ? AND leido = 0";
    $stmtUp = $this->db->prepare($sqlUpdate);
    $stmtUp->execute([$id_actividad, $id_usuario]);

    // Obtener mensajes nuevos
    $sql = "
        SELECT m.id_mensaje, m.id_actividad, m.id_usuario, m.contenido, m.fecha_envio, m.leido,
               u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
        FROM mensajes m
        INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
        WHERE m.id_actividad = ? AND m.id_mensaje > ?
        ORDER BY m.fecha_envio ASC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id_actividad, $lastId]);
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($mensajes as &$m) {
        $m['nombre_completo'] = trim($m['nombre'] . ' ' . ($m['apellido_paterno'] ?? '') . ' ' . ($m['apellido_materno'] ?? ''));
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

// Guardar mensaje en actividad
public function enviarMensajeActividad($id_actividad, $id_usuario, $contenido) {
    // Verificar que el usuario es participante aceptado
    $stmtCheck = $this->db->prepare("SELECT 1 FROM participantes WHERE id_actividad = ? AND id_usuario = ? AND estado = 'aceptado'");
    $stmtCheck->execute([$id_actividad, $id_usuario]);
    if (!$stmtCheck->fetchColumn()) {
        return false;
    }
    $stmt = $this->db->prepare("INSERT INTO mensajes (id_actividad, id_usuario, contenido, fecha_envio) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$id_actividad, $id_usuario, $contenido]);
    $id = $this->db->lastInsertId();
    
    // Obtener fecha de envío
    $stmtDate = $this->db->prepare("SELECT fecha_envio FROM mensajes WHERE id_mensaje = ?");
    $stmtDate->execute([$id]);
    $fila = $stmtDate->fetch();
    return [
        'id_mensaje' => $id,
        'fecha_envio' => $fila['fecha_envio']
    ];
}

// ================== ELIMINAR MENSAJES ==================
// Eliminar mensaje privado (borrado físico o lógico según tu preferencia)
// Aquí haremos borrado físico para simplicidad
public function eliminarMensajePrivado($id_mensaje, $id_usuario) {
    // Solo puede eliminar el remitente o el destinatario
    $sql = "DELETE FROM mensajes_privados 
            WHERE id_mensaje = :id AND (id_remitente = :user OR id_destinatario = :user)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id_mensaje, ':user' => $id_usuario]);
}

// Eliminar mensaje de actividad (solo el autor puede eliminar)
public function eliminarMensajeActividad($id_mensaje, $id_usuario) {
    $sql = "DELETE FROM mensajes WHERE id_mensaje = :id AND id_usuario = :user";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id_mensaje, ':user' => $id_usuario]);
}
}
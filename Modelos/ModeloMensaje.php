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
}
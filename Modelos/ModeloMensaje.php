<?php
class ModeloMensaje {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Lista de amigos con última conversación (para el listado de chats)
    public function obtenerConversacionesPrivadas($id_usuario) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, u.ultima_conexion,
                       (SELECT COUNT(*) FROM mensajes_privados mp 
                        WHERE ((mp.id_remitente = u.id_usuario AND mp.id_destinatario = :id_user) 
                            OR (mp.id_remitente = :id_user AND mp.id_destinatario = u.id_usuario))
                          AND mp.leido = 0 
                          AND mp.id_destinatario = :id_user) AS no_leidos,
                       (SELECT contenido FROM mensajes_privados 
                        WHERE (id_remitente = u.id_usuario AND id_destinatario = :id_user) 
                           OR (id_remitente = :id_user AND id_destinatario = u.id_usuario)
                        ORDER BY fecha_envio DESC LIMIT 1) AS ultimo_mensaje,
                       (SELECT fecha_envio FROM mensajes_privados 
                        WHERE (id_remitente = u.id_usuario AND id_destinatario = :id_user) 
                           OR (id_remitente = :id_user AND id_destinatario = u.id_usuario)
                        ORDER BY fecha_envio DESC LIMIT 1) AS ultima_fecha
                FROM amistades a
                INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
                WHERE (a.id_solicitante = :id_user OR a.id_receptor = :id_user)
                  AND a.estado = 'aceptado'
                  AND u.id_usuario != :id_user
                ORDER BY ultima_fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario]);
        $amigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($amigos as &$a) {
            $a['nombre_completo'] = trim($a['nombre'] . ' ' . $a['apellido_paterno'] . ' ' . $a['apellido_materno']);
            if (!empty($a['foto_perfil'])) {
                // Conversión simple (asume JPEG, evita finfo)
                $a['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($a['foto_perfil']);
            } else {
                $a['foto_base64'] = null;
            }
            // Calcular estado de conexión (última conexión hace menos de 5 minutos)
            $a['online'] = (strtotime($a['ultima_conexion']) > time() - 300);
        }
        return $amigos;
    }

    // Obtener mensajes privados entre dos usuarios (y marcar como leídos los del destinatario)
    public function obtenerMensajesPrivados($id_usuario, $id_amigo) {
        // Marcar como leídos los mensajes enviados por el amigo hacia el usuario actual
        $sqlUpdate = "UPDATE mensajes_privados SET leido = 1, fecha_lectura = NOW()
                      WHERE id_remitente = :id_amigo AND id_destinatario = :id_user AND leido = 0";
        $stmtUp = $this->db->prepare($sqlUpdate);
        $stmtUp->execute([':id_amigo' => $id_amigo, ':id_user' => $id_usuario]);

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
                $m['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($m['foto_perfil']);
            } else {
                $m['foto_base64'] = null;
            }
        }
        return $mensajes;
    }

    // Obtener nuevos mensajes (polling) - SIN finfo
    public function obtenerNuevosMensajesPrivados($id_usuario, $id_amigo, $ultimo_id) {
        $sql = "SELECT mp.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes_privados mp
                INNER JOIN usuarios u ON mp.id_remitente = u.id_usuario
                WHERE ((mp.id_remitente = :id_user AND mp.id_destinatario = :id_amigo AND mp.eliminado_remitente = 0)
                    OR (mp.id_remitente = :id_amigo AND mp.id_destinatario = :id_user AND mp.eliminado_destinatario = 0))
                  AND mp.id_mensaje > :ultimo
                ORDER BY mp.fecha_envio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_user' => $id_usuario,
            ':id_amigo' => $id_amigo,
            ':ultimo' => $ultimo_id
        ]);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($mensajes as &$m) {
            $m['nombre_completo'] = trim($m['nombre'] . ' ' . $m['apellido_paterno'] . ' ' . $m['apellido_materno']);
            // Enviar solo la foto si existe (sin finfo, asumimos JPEG)
            if (!empty($m['foto_perfil'])) {
                $m['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($m['foto_perfil']);
            } else {
                $m['foto_base64'] = null;
            }
        }
        return $mensajes;
    }

    // Guardar un mensaje privado
    public function guardarMensajePrivado($id_remitente, $id_destinatario, $contenido) {
        // Verificar amistad
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

    // Obtener el último mensaje privado (para devolverlo tras enviar) - SIN finfo
    public function obtenerUltimoMensajePrivado($id_usuario, $id_amigo) {
        $sql = "SELECT mp.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM mensajes_privados mp
                INNER JOIN usuarios u ON mp.id_remitente = u.id_usuario
                WHERE (mp.id_remitente = :id_user AND mp.id_destinatario = :id_amigo)
                OR (mp.id_remitente = :id_amigo AND mp.id_destinatario = :id_user)
                ORDER BY mp.id_mensaje DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $id_usuario, ':id_amigo' => $id_amigo]);
        $mensaje = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($mensaje) {
            $mensaje['nombre_completo'] = trim($mensaje['nombre'] . ' ' . $mensaje['apellido_paterno'] . ' ' . $mensaje['apellido_materno']);
            if (!empty($mensaje['foto_perfil'])) {
                $mensaje['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($mensaje['foto_perfil']);
            } else {
                $mensaje['foto_base64'] = null;
            }
        }
        return $mensaje;
    }
}
?>
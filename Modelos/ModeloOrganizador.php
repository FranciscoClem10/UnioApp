<?php
class ModeloOrganizador {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Obtener organizadores de una actividad
    public function obtenerOrganizadores($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.foto_perfil
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.rol = 'organizador' AND p.estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orgs as &$o) {
            $o['nombre_completo'] = trim($o['nombre'] . ' ' . $o['apellido_paterno'] . ' ' . $o['apellido_materno']);
            if (!empty($o['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $o['foto_perfil']);
                finfo_close($finfo);
                $o['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($o['foto_perfil']);
            }
        }
        return $orgs;
    }

    // Solicitudes pendientes (estado = 'pendiente')
    public function obtenerSolicitudesPendientes($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, p.fecha_solicitud
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.estado = 'pendiente' AND p.rol = 'miembro'
                ORDER BY p.fecha_solicitud ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($solicitudes as &$s) {
            $s['nombre_completo'] = trim($s['nombre'] . ' ' . $s['apellido_paterno'] . ' ' . $s['apellido_materno']);
            if (!empty($s['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $s['foto_perfil']);
                finfo_close($finfo);
                $s['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($s['foto_perfil']);
            }
        }
        return $solicitudes;
    }

    // Participantes aceptados (incluye creador y organizadores)
    public function obtenerParticipantesAceptados($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, p.rol
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id AND p.estado = 'aceptado'
                ORDER BY FIELD(p.rol, 'creador', 'organizador', 'miembro')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($participantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
            if (!empty($p['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $p['foto_perfil']);
                finfo_close($finfo);
                $p['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($p['foto_perfil']);
            }
        }
        return $participantes;
    }

    // Invitaciones enviadas (para mostrar en edición)
    public function obtenerInvitaciones($id_actividad) {
        $sql = "SELECT i.*, 
                    CASE WHEN i.id_invitado_usuario IS NOT NULL THEN u.nombre ELSE NULL END as invitado_nombre,
                    CASE WHEN i.id_invitado_usuario IS NOT NULL THEN u.email ELSE i.email_invitado END as contacto
                FROM invitaciones i
                LEFT JOIN usuarios u ON i.id_invitado_usuario = u.id_usuario
                WHERE i.id_actividad = :id
                ORDER BY i.fecha_invitacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar participantes aceptados
    public function contarParticipantesAceptados($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }

    // Agregar organizador (si no existe, se inserta; si existe, se actualiza rol)
    public function agregarOrganizador($id_actividad, $id_usuario) {
        $sqlCheck = "SELECT 1 FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        if ($stmt->fetchColumn()) {
            $sql = "UPDATE participantes SET rol = 'organizador', estado = 'aceptado' 
                    WHERE id_actividad = :id_act AND id_usuario = :id_user";
        } else {
            $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) 
                    VALUES (:id_act, :id_user, 'organizador', 'aceptado')";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Quitar organizador (cambiar a 'miembro')
    public function quitarOrganizador($id_actividad, $id_usuario) {
        $sql = "UPDATE participantes SET rol = 'miembro' 
                WHERE id_actividad = :id_act AND id_usuario = :id_user AND rol = 'organizador'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Cambiar estado de un participante (pendiente -> aceptado/rechazado)
    public function cambiarEstadoParticipante($id_actividad, $id_usuario, $nuevo_estado) {
        $sql = "UPDATE participantes SET estado = :estado 
                WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Expulsar participante (eliminar registro, no se puede expulsar al creador)
    public function expulsarParticipante($id_actividad, $id_usuario) {
        $sql = "DELETE FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user AND rol != 'creador'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
    }

    // Verificar si un usuario es creador u organizador de la actividad
    public function esOrganizadorOCreador($id_actividad, $id_usuario) {
        $sql = "SELECT 1 FROM participantes 
                WHERE id_actividad = :id_act AND id_usuario = :id_user 
                AND (rol = 'creador' OR rol = 'organizador') AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        return $stmt->fetchColumn() ? true : false;
    }

    // Invitar a usuario registrado
    public function invitarUsuario($id_actividad, $id_invitador, $id_invitado) {
        $sqlCheck = "SELECT 1 FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_invitado]);
        if ($stmt->fetchColumn()) return false;
        
        $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) 
                VALUES (:id_act, :id_user, 'miembro', 'invitado')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_invitado]);
        
        $sqlInv = "INSERT INTO invitaciones (id_actividad, id_invitador, id_invitado_usuario, estado) 
                   VALUES (:id_act, :id_inv, :id_user, 'pendiente')";
        $stmtInv = $this->db->prepare($sqlInv);
        return $stmtInv->execute([':id_act' => $id_actividad, ':id_inv' => $id_invitador, ':id_user' => $id_invitado]);
    }

    // Invitar por correo electrónico (usuario no registrado)
    public function invitarEmail($id_actividad, $id_invitador, $email) {
        $sql = "INSERT INTO invitaciones (id_actividad, id_invitador, email_invitado, estado) 
                VALUES (:id_act, :id_inv, :email, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_act' => $id_actividad, ':id_inv' => $id_invitador, ':email' => $email]);
    }

    // Buscar amigos del usuario para invitarlos
    public function buscarAmigosParaInvitacion($id_usuario, $termino) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email
                FROM amistades a
                INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
                WHERE (a.id_solicitante = :id_self OR a.id_receptor = :id_self)
                  AND a.estado = 'aceptado'
                  AND u.id_usuario != :id_self
                  AND (u.nombre LIKE :term OR u.apellido_paterno LIKE :term OR u.email LIKE :term)
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $term = "%$termino%";
        $stmt->execute([':id_self' => $id_usuario, ':term' => $term]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar miembros (participantes con rol 'miembro' y estado 'aceptado')
    public function contarMiembros($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND rol = 'miembro' AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }

    // Verificar si un usuario es participante activo (aceptado)
    public function esParticipanteActivo($id_actividad, $id_usuario) {
        $sql = "SELECT 1 FROM participantes 
                WHERE id_actividad = :id_actividad AND id_usuario = :id_usuario 
                  AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad, ':id_usuario' => $id_usuario]);
        return (bool) $stmt->fetchColumn();
    }

    // Obtener todos los participantes (para chat, lista, etc.)
    public function obtenerParticipantes($id_actividad) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil,
                       p.rol
                FROM participantes p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_actividad = :id_actividad AND p.estado = 'aceptado'
                ORDER BY FIELD(p.rol, 'creador', 'organizador', 'miembro'), u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_actividad' => $id_actividad]);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($participantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
            if (!empty($p['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $p['foto_perfil']);
                finfo_close($finfo);
                $p['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($p['foto_perfil']);
            } else {
                $p['foto_base64'] = null;
            }
        }
        return $participantes;
    }
}
?>
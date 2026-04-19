<?php
class ModeloUsuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Obtener usuario por ID (con campos separados)
    public function obtenerPorId($id_usuario) {
        $sql = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, email, telefono, 
                       fecha_nacimiento, genero, latitud, longitud, biografia, foto_perfil, fecha_registro, ultima_conexion
                FROM usuarios WHERE id_usuario = :id AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            $usuario['nombre_completo'] = trim($usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
        }
        return $usuario;
    }

    // Obtener usuario por email
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar credenciales de login
    public function verificarCredenciales($email, $password) {
        $usuario = $this->obtenerPorEmail($email);
        if ($usuario && password_verify($password, $usuario['pass'])) {
            unset($usuario['pass']);
            return $usuario;
        }
        return false;
    }

    // Crear nuevo usuario
    public function crearUsuario($datos) {
        $sql = "INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, email, pass, telefono, fecha_nacimiento, genero, latitud, longitud, biografia, foto_perfil)
                VALUES (:nombre, :apellido_paterno, :apellido_materno, :email, :pass, :telefono, :fecha_nacimiento, :genero, :latitud, :longitud, :biografia, :foto_perfil)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':apellido_paterno' => $datos['apellido_paterno'],
                ':apellido_materno' => $datos['apellido_materno'] ?? '',
                ':email' => $datos['email'],
                ':pass' => $datos['password_hash'],
                ':telefono' => $datos['telefono'],
                ':fecha_nacimiento' => $datos['fecha_nacimiento'],
                ':genero' => $datos['genero'],
                ':latitud' => $datos['latitud'],
                ':longitud' => $datos['longitud'],
                ':biografia' => $datos['biografia'],
                ':foto_perfil' => $datos['foto_blob']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $_SESSION['debug_sql_error'] = $e->getMessage();
            error_log("Error SQL al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar perfil completo (incluyendo foto)
    public function actualizarPerfil($id_usuario, $datos, $foto_blob = null) {
        try {
            $sql = "UPDATE usuarios SET 
                    nombre = :nombre,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    telefono = :telefono,
                    fecha_nacimiento = :fecha_nacimiento,
                    genero = :genero,
                    biografia = :biografia,
                    latitud = :latitud,
                    longitud = :longitud";
            if ($foto_blob !== null) {
                $sql .= ", foto_perfil = :foto";
            }
            $sql .= " WHERE id_usuario = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellido_paterno', $datos['apellido_paterno']);
            $stmt->bindParam(':apellido_materno', $datos['apellido_materno']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':fecha_nacimiento', $datos['fecha_nacimiento']);
            $stmt->bindParam(':genero', $datos['genero']);
            $stmt->bindParam(':biografia', $datos['biografia']);
            $stmt->bindParam(':latitud', $datos['latitud']);
            $stmt->bindParam(':longitud', $datos['longitud']);
            $stmt->bindParam(':id', $id_usuario);
            if ($foto_blob !== null) {
                $stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar perfil: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar contraseña
    public function cambiarPassword($id_usuario, $nuevo_hash) {
        $sql = "UPDATE usuarios SET pass = :pass WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':pass' => $nuevo_hash, ':id' => $id_usuario]);
    }

    // Listar amigos aceptados
    public function obtenerAmigos($id_usuario) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, u.latitud, u.longitud
                FROM amistades a
                INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
                WHERE (a.id_solicitante = :id OR a.id_receptor = :id)
                  AND a.estado = 'aceptado'
                  AND u.id_usuario != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
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

    // Buscar usuarios por nombre, apellidos o email, incluyendo relación de amistad
    public function buscarUsuariosConRelacion($id_usuario, $termino) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.foto_perfil, u.latitud, u.longitud
                FROM usuarios u
                WHERE u.activo = 1 
                  AND u.id_usuario != :id_actual
                  AND (u.nombre LIKE :termino 
                       OR u.apellido_paterno LIKE :termino 
                       OR u.apellido_materno LIKE :termino 
                       OR u.email LIKE :termino 
                       OR CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) LIKE :termino)
                LIMIT 30";
        $stmt = $this->db->prepare($sql);
        $terminoParam = "%$termino%";
        $stmt->execute([':id_actual' => $id_usuario, ':termino' => $terminoParam]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sqlAmistad = "SELECT id_solicitante, id_receptor, estado FROM amistades 
                       WHERE (id_solicitante = :id1 AND id_receptor = :id2)
                          OR (id_solicitante = :id2 AND id_receptor = :id1)";
        $stmtAm = $this->db->prepare($sqlAmistad);
        foreach ($usuarios as &$u) {
            $stmtAm->execute([':id1' => $id_usuario, ':id2' => $u['id_usuario']]);
            $rel = $stmtAm->fetch(PDO::FETCH_ASSOC);
            if ($rel) {
                if ($rel['estado'] == 'aceptado') {
                    $u['relacion'] = 'amigo';
                } elseif ($rel['estado'] == 'pendiente') {
                    $u['relacion'] = ($rel['id_solicitante'] == $id_usuario) ? 'solicitud_enviada' : 'solicitud_recibida';
                } else {
                    $u['relacion'] = 'ninguna';
                }
            } else {
                $u['relacion'] = 'ninguna';
            }
            $u['nombre_completo'] = trim($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . $u['apellido_materno']);
            if (!empty($u['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $u['foto_perfil']);
                finfo_close($finfo);
                $u['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($u['foto_perfil']);
            } else {
                $u['foto_base64'] = null;
            }
        }
        return $usuarios;
    }

    // Enviar solicitud de amistad
    public function enviarSolicitudAmistad($id_solicitante, $id_receptor) {
        if ($id_solicitante == $id_receptor) return false;
        $sqlCheck = "SELECT 1 FROM amistades WHERE (id_solicitante = :s1 AND id_receptor = :r1) 
                     OR (id_solicitante = :s2 AND id_receptor = :r2)";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':s1' => $id_solicitante, ':r1' => $id_receptor, ':s2' => $id_receptor, ':r2' => $id_solicitante]);
        if ($stmt->fetchColumn()) return false;
        
        $sql = "INSERT INTO amistades (id_solicitante, id_receptor, estado) VALUES (:s, :r, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':s' => $id_solicitante, ':r' => $id_receptor]);
    }

    // Obtener solicitudes pendientes (recibidas)
    public function obtenerSolicitudesPendientes($id_usuario) {
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
                FROM amistades a
                INNER JOIN usuarios u ON a.id_solicitante = u.id_usuario
                WHERE a.id_receptor = :id AND a.estado = 'pendiente'
                ORDER BY a.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
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

    // Responder solicitud (aceptar o rechazar)
    public function responderSolicitud($id_solicitante, $id_receptor, $accion) {
        $estado = ($accion === 'aceptar') ? 'aceptado' : 'rechazado';
        $sql = "UPDATE amistades SET estado = :estado, fecha_respuesta = NOW()
                WHERE id_solicitante = :s AND id_receptor = :r AND estado = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $estado, ':s' => $id_solicitante, ':r' => $id_receptor]);
    }

     public function actualizarUltimaConexion($id_usuario) {
        $sql = "UPDATE usuarios SET ultima_conexion = NOW() WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id_usuario]);
    }

    // Obtener todos los usuarios activos excepto uno dado
    public function obtenerTodosExcepto($id_usuario) {
        $sql = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, email, foto_perfil 
                FROM usuarios 
                WHERE activo = 1 AND id_usuario != :id 
                ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($usuarios as &$u) {
            $u['nombre_completo'] = trim($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . $u['apellido_materno']);
            // Si queremos mostrar foto en base64 en el dashboard, podemos procesar aquí
            if (!empty($u['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $u['foto_perfil']);
                finfo_close($finfo);
                $u['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($u['foto_perfil']);
            } else {
                $u['foto_base64'] = null;
            }
        }
        return $usuarios;
    }
}
?>
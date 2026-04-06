<?php
class ModeloUsuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Obtener usuario por ID
    public function obtenerPorId($id_usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarCredenciales($email, $password) {
        $usuario = $this->obtenerPorEmail($email);
        if ($usuario && password_verify($password, $usuario['pass'])) {
            unset($usuario['pass']);
            return $usuario;
        }
        return false;
    }

    public function crearUsuario($datos) {
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, pass, telefono, fecha_nacimiento, genero, latitud, longitud, biografia, foto_perfil)
                VALUES (:nombre, :apellidos, :email, :pass, :telefono, :fecha_nacimiento, :genero, :latitud, :longitud, :biografia, :foto_perfil)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':apellidos' => $datos['apellidos'],
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

    // Actualizar perfil
    public function actualizarPerfil($id_usuario, $datos, $foto_blob = null) {
        try {
            $sql = "UPDATE usuarios SET 
                    nombre = :nombre,
                    apellidos = :apellidos,
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
            $stmt->bindParam(':apellidos', $datos['apellidos']);
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

    // Cambiar contraseña (opcional)
    public function cambiarPassword($id_usuario, $nuevo_hash) {
        $sql = "UPDATE usuarios SET pass = :pass WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':pass' => $nuevo_hash, ':id' => $id_usuario]);
    }

   // Listar amigos (aceptados) de un usuario
public function obtenerAmigos($id_usuario) {
    $sql = "SELECT u.id_usuario, u.nombre, u.apellidos, u.foto_perfil, u.latitud, u.longitud
            FROM amistades a
            INNER JOIN usuarios u ON (u.id_usuario = a.id_solicitante OR u.id_usuario = a.id_receptor)
            WHERE (a.id_solicitante = :id OR a.id_receptor = :id)
              AND a.estado = 'aceptado'
              AND u.id_usuario != :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $amigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($amigos as &$a) {
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

// Buscar usuarios por nombre/apellido (excluyendo a uno mismo y a los ya amigos)
public function buscarNuevosAmigos($id_usuario, $termino) {
    // Obtener IDs de amigos actuales (incluyendo pendientes para no mostrarlos)
    $sqlAmigos = "SELECT id_solicitante as id FROM amistades WHERE id_receptor = :id
                  UNION SELECT id_receptor FROM amistades WHERE id_solicitante = :id";
    $stmtAm = $this->db->prepare($sqlAmigos);
    $stmtAm->execute([':id' => $id_usuario]);
    $idsExcluir = $stmtAm->fetchAll(PDO::FETCH_COLUMN);
    $idsExcluir[] = $id_usuario;
    $placeholders = implode(',', array_fill(0, count($idsExcluir), '?'));
    
    $sql = "SELECT id_usuario, nombre, apellidos, email, foto_perfil, latitud, longitud
            FROM usuarios 
            WHERE activo = 1 
              AND id_usuario NOT IN ($placeholders)
              AND (nombre LIKE ? OR apellidos LIKE ? OR CONCAT(nombre, ' ', apellidos) LIKE ?)
            LIMIT 20";
    $parametros = array_merge($idsExcluir, ["%$termino%", "%$termino%", "%$termino%"]);
    $stmt = $this->db->prepare($sql);
    $stmt->execute($parametros);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($usuarios as &$u) {
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
    // Verificar si ya existe una solicitud previa
    $sqlCheck = "SELECT 1 FROM amistades WHERE (id_solicitante = :s1 AND id_receptor = :r1) 
                 OR (id_solicitante = :s2 AND id_receptor = :r2)";
    $stmt = $this->db->prepare($sqlCheck);
    $stmt->execute([':s1' => $id_solicitante, ':r1' => $id_receptor, ':s2' => $id_receptor, ':r2' => $id_solicitante]);
    if ($stmt->fetchColumn()) return false;
    
    $sql = "INSERT INTO amistades (id_solicitante, id_receptor, estado) VALUES (:s, :r, 'pendiente')";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':s' => $id_solicitante, ':r' => $id_receptor]);
}

// Obtener solicitudes de amistad pendientes (para el usuario logueado como receptor)
public function obtenerSolicitudesPendientes($id_usuario) {
    $sql = "SELECT a.*, u.nombre, u.apellidos, u.foto_perfil
            FROM amistades a
            INNER JOIN usuarios u ON a.id_solicitante = u.id_usuario
            WHERE a.id_receptor = :id AND a.estado = 'pendiente'
            ORDER BY a.fecha_solicitud DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($solicitudes as &$s) {
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
}
?>
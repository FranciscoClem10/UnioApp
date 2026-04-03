<?php
class ModeloUsuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
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
}
?>
<?php
class ModeloAjustes {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Obtiene los ajustes de un usuario; si no existen, crea un registro por defecto
    public function obtenerAjustes($idUsuario) {
        // Intentamos obtener el registro actual
        $stmt = $this->db->prepare("SELECT * FROM ajustes WHERE id_usuario = ?");
        $stmt->execute([$idUsuario]);
        $ajustes = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ajustes) {
            // Insertamos valores por defecto (los mismos que definiste en la tabla)
            $stmt = $this->db->prepare("INSERT INTO ajustes (id_usuario) VALUES (?)");
            $stmt->execute([$idUsuario]);
            // Volvemos a leer
            $stmt = $this->db->prepare("SELECT * FROM ajustes WHERE id_usuario = ?");
            $stmt->execute([$idUsuario]);
            $ajustes = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $ajustes;
    }

	  public function guardarAjustes($idUsuario, $datos) {
		// Campos que siempre son enviados (selects)
		$camposVisibilidad = [
			'ubicacion_visibilidad', 'correo_visibilidad', 'foto_visibilidad',
			'edad_visibilidad', 'perfil_visibilidad', 'actividades_visibilidad'
		];

		// Campos booleanos (checkboxes) – deben ser 1 o 0
		$camposBooleanos = [
			'modo_oscuro', 'notif_mensaje', 'notif_mensaje_actividad',
			'notif_mensaje_privado', 'notif_amistad', 'notif_solicitud_amistad',
			'notif_actividad'
		];

		$sets = [];
		$valores = [];

		// 1. Visibilidad (selects)
		foreach ($camposVisibilidad as $campo) {
			if (array_key_exists($campo, $datos)) {
				$sets[] = "$campo = :$campo";
				$valores[":$campo"] = $datos[$campo];
			}
		}

		// 2. Booleanos (checkboxes) – comparación explícita del valor
		foreach ($camposBooleanos as $campo) {
			$sets[] = "$campo = :$campo";
			$valores[":$campo"] = (isset($datos[$campo]) && $datos[$campo] == '1') ? 1 : 0;
		}

		if (empty($sets)) {
			return false;
		}

		$sql = "UPDATE ajustes SET " . implode(', ', $sets) . " WHERE id_usuario = :id_usuario";
		$valores[':id_usuario'] = $idUsuario;

		$stmt = $this->db->prepare($sql);
		return $stmt->execute($valores);
	}
}
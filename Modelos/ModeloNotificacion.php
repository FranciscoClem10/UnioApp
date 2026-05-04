<?php
require_once 'Modelos/ModeloAjustes.php';

class ModeloNotificacion {
    private $db;

    // Mapa de tipo de notificación → clave en la tabla ajustes
    private $mapaTipos = [
        'mensaje_grupo'       => 'notif_mensaje',
        'mensaje_actividad'   => 'notif_mensaje_actividad',
        'mensaje_privado'     => 'notif_mensaje_privado',
        'amistad'             => 'notif_amistad',
        'solicitud_amistad'   => 'notif_solicitud_amistad',
        'actividad'           => 'notif_actividad',
    ];

    public function __construct() {
        $this->db = Database::getConexion();
    }

    /**
     * Devuelve los tipos de notificación que el usuario tiene habilitados.
     */
    private function obtenerTiposPermitidos($id_usuario) {
        // Si el usuario es el de la sesión, usamos los ajustes de sesión
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
            if (isset($_SESSION['ajustes'])) {
                $ajustes = $_SESSION['ajustes'];
            } else {
                $modeloAj = new ModeloAjustes();
                $ajustes = $modeloAj->obtenerAjustes($id_usuario);
                $_SESSION['ajustes'] = $ajustes;
            }
        } else {
            // Para otro usuario consultamos directamente la BD
            $modeloAj = new ModeloAjustes();
            $ajustes = $modeloAj->obtenerAjustes($id_usuario);
        }

        $permitidos = [];
        foreach ($this->mapaTipos as $tipo => $clave) {
            if (($ajustes[$clave] ?? 1) == 1) {
                $permitidos[] = $tipo;
            }
        }
        return $permitidos;
    }

    /**
     * Crea una notificación (sin cambios, se inserta siempre para mantener historial).
     */
    public function crear($id_usuario, $tipo, $titulo, $contenido, $enlace = null) {
        $sql = "INSERT INTO notificaciones (id_usuario, tipo, titulo, contenido, enlace) 
                VALUES (:id_usuario, :tipo, :titulo, :contenido, :enlace)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':tipo' => $tipo,
            ':titulo' => $titulo,
            ':contenido' => $contenido,
            ':enlace' => $enlace
        ]);
    }

    /**
     * Obtiene todas las notificaciones, filtradas por los tipos permitidos.
     */
	public function obtenerTodas($id_usuario, $limite = 100) {
		$tiposPermitidos = $this->obtenerTiposPermitidos($id_usuario);
		if (empty($tiposPermitidos)) {
			return [];
		}
		$placeholders = implode(',', array_fill(0, count($tiposPermitidos), '?'));
		// Forzamos integer para LIMIT y lo insertamos directamente
		$limite = (int) $limite;
		$sql = "SELECT * FROM notificaciones 
				WHERE id_usuario = ? AND tipo IN ($placeholders)
				ORDER BY fecha_creacion DESC 
				LIMIT $limite";
		$stmt = $this->db->prepare($sql);
		$params = array_merge([$id_usuario], $tiposPermitidos);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

    /**
     * Obtiene solo las no leídas, filtradas por los tipos permitidos.
     */
    public function obtenerNoLeidas($id_usuario) {
        $tiposPermitidos = $this->obtenerTiposPermitidos($id_usuario);
        if (empty($tiposPermitidos)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($tiposPermitidos), '?'));
        $sql = "SELECT * FROM notificaciones 
                WHERE id_usuario = ? AND tipo IN ($placeholders) AND leida = 0 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $params = array_merge([$id_usuario], $tiposPermitidos);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las no leídas, filtradas por los tipos permitidos.
     */
    public function contarNoLeidas($id_usuario) {
        $tiposPermitidos = $this->obtenerTiposPermitidos($id_usuario);
        if (empty($tiposPermitidos)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($tiposPermitidos), '?'));
        $sql = "SELECT COUNT(*) FROM notificaciones 
                WHERE id_usuario = ? AND tipo IN ($placeholders) AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $params = array_merge([$id_usuario], $tiposPermitidos);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Marca una notificación como leída (sin cambios).
     */
    public function marcarLeida($id_notificacion, $id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_notificacion = :id_not AND id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_not' => $id_notificacion, ':id_user' => $id_usuario]);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas (sin cambios).
     */
    public function marcarTodasLeidas($id_usuario) {
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id_user";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_user' => $id_usuario]);
    }

    /**
     * Marca como leídas las notificaciones de ciertos tipos (útil si decides mantenerlo).
     */
    public function marcarLeidasPorTipos($id_usuario, $tipos) {
        if (empty($tipos)) return true;
        $placeholders = implode(',', array_fill(0, count($tipos), '?'));
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_usuario = ? AND tipo IN ($placeholders) AND leida = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_merge([$id_usuario], $tipos));
    }

    /**
     * Marca como leídas por contexto (sin cambios).
     */
    public function marcarLeidasPorContexto($id_usuario, $tipo, $referencia) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE id_usuario = :id_user 
                AND tipo = :tipo 
                AND enlace LIKE :ref 
                AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $ref = '%' . $referencia . '%';
        return $stmt->execute([':id_user' => $id_usuario, ':tipo' => $tipo, ':ref' => $ref]);
    }
}
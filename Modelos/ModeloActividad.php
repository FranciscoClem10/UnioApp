<?php
class ModeloActividad {
    private $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    public function obtenerTodasVisibles($usuario_id = null) {
		if ($usuario_id === null && isset($_SESSION['usuario_id'])) {
			$usuario_id = $_SESSION['usuario_id'];
		}

		$params = [];
		$sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
				a.edad_minima, a.limite_participantes_max AS limite_personas,
				a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
				a.id_creador, ta.nombre_tipo AS categoria,
				a.fecha_inicio AS fecha_proxima,
				a.direccion,
				DATE_FORMAT(a.fecha_inicio, '%H:%i:%s') AS hora_proxima
			FROM actividades a
			INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
			WHERE a.estado IN ('pendiente', 'en_curso')
			AND a.privacidad != 'privada'";

		if ($usuario_id) {
			$sql .= " AND a.id_creador != :usuario_id";
			$params[':usuario_id'] = $usuario_id;
		}

		if ($usuario_id) {
			$sql .= " AND NOT EXISTS (
						SELECT 1 FROM amistades bl
						WHERE bl.estado = 'bloqueado'
							AND bl.id_solicitante = :usuario_actual
							AND bl.id_receptor = a.id_creador
					)";
			$params[':usuario_actual'] = $usuario_id;
		}

		$sql .= " ORDER BY a.fecha_inicio ASC";

		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($actividades as &$act) {
			$act['fecha'] = $act['fecha_proxima'] ? date('Y-m-d', strtotime($act['fecha_proxima'])) : null;
			$act['hora'] = $act['hora_proxima'] ?? null;

			switch ($act['tipo_acceso']) {
				case 'publica': $act['tipo_acceso'] = 'todos'; break;
				case 'privada': $act['tipo_acceso'] = 'invitacion'; break;
				case 'por_aprobacion': $act['tipo_acceso'] = 'aprobado'; break;
				default: $act['tipo_acceso'] = 'todos';
			}
		}

		return $actividades;
	}

    // Actividades creadas por el usuario
    public function obtenerPorCreador($usuario_id) {
		$sql = "SELECT a.id_actividad, a.nombre AS titulo, a.descripcion, a.requisitos, 
					   a.edad_minima, a.limite_participantes_max AS limite_personas,
					   a.latitud, a.longitud, a.privacidad AS tipo_acceso, a.estado,
					   ta.nombre_tipo AS categoria,
					   a.fecha_inicio AS fecha_proxima,
					   a.direccion,
					   DATE_FORMAT(a.fecha_inicio, '%H:%i:%s') AS hora_proxima
				FROM actividades a
				INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
				WHERE a.id_creador = :id_creador
				ORDER BY a.fecha_inicio ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_creador' => $usuario_id]);
		$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($actividades as &$act) {
			$act['fecha'] = $act['fecha_proxima'] ? date('Y-m-d', strtotime($act['fecha_proxima'])) : null;
			$act['hora'] = $act['hora_proxima'] ?? null;
			switch ($act['tipo_acceso']) {
				case 'publica': $act['tipo_acceso'] = 'todos'; break;
				case 'privada': $act['tipo_acceso'] = 'invitacion'; break;
				case 'por_aprobacion': $act['tipo_acceso'] = 'aprobado'; break;
				default: $act['tipo_acceso'] = 'todos';
			}
		}
		return $actividades;
	}

    public function obtenerEstadisticas() {
        $sql = "SELECT COUNT(*) as total FROM actividades WHERE estado IN ('pendiente', 'en_curso')";
        $stmt = $this->db->query($sql);
        $total = $stmt->fetchColumn();

        $sqlCat = "SELECT ta.nombre_tipo, COUNT(*) as count 
                   FROM actividades a 
                   INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                   WHERE a.estado IN ('pendiente', 'en_curso')
                   GROUP BY ta.id_tipo";
        $stmtCat = $this->db->query($sqlCat);
        $porCategoria = $stmtCat->fetchAll(PDO::FETCH_KEY_PAIR);
        return ['total' => $total, 'por_categoria' => $porCategoria];
    }

    public function obtenerPorId($id_actividad) {
        $sql = "SELECT a.*, t.nombre_tipo,
                       u.nombre AS creador_nombre, u.apellido_paterno AS creador_apellido_paterno, 
                       u.apellido_materno AS creador_apellido_materno
                FROM actividades a
                LEFT JOIN tipos_actividad t ON a.id_tipo = t.id_tipo
                LEFT JOIN usuarios u ON a.id_creador = u.id_usuario
                WHERE a.id_actividad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($actividad && !empty($actividad['foto_actividad'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $actividad['foto_actividad']);
            finfo_close($finfo);
            $actividad['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($actividad['foto_actividad']);
        } else {
            $actividad['foto_base64'] = null;
        }
        return $actividad;
    }


    public function obtenerTiposActividad() {
        $sql = "SELECT id_tipo, nombre_tipo FROM tipos_actividad ORDER BY nombre_tipo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

	public function crearActividad($datos, $foto_blob = null) {
		try {
			$this->db->beginTransaction();

			// Establecer límite mínimo por defecto a 5 si no existe o es menor
			$limite_min = isset($datos['limite_participantes_min']) 
				? max(5, (int)$datos['limite_participantes_min']) 
				: 5;

			$sql = "INSERT INTO actividades (id_tipo, id_creador, nombre, descripcion, requisitos, 
					edad_minima, edad_maxima, limite_participantes_min, limite_participantes_max, 
					latitud, longitud, direccion, fecha_inicio, fecha_fin, foto_actividad, privacidad, estado)
					VALUES (:id_tipo, :id_creador, :nombre, :descripcion, :requisitos, 
							:edad_minima, :edad_maxima, :limite_min, :limite_max, 
							:latitud, :longitud, :direccion, :fecha_inicio, :fecha_fin, :foto, :privacidad, 'pendiente')";
							
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':id_tipo', $datos['id_tipo']);
			$stmt->bindParam(':id_creador', $datos['id_creador']);
			$stmt->bindParam(':nombre', $datos['nombre']);
			$stmt->bindParam(':descripcion', $datos['descripcion']);
			$stmt->bindParam(':requisitos', $datos['requisitos']);
			$stmt->bindParam(':edad_minima', $datos['edad_minima']);
			$stmt->bindParam(':edad_maxima', $datos['edad_maxima']);
			$stmt->bindParam(':limite_min', $limite_min);  // Usamos la variable con el valor por defecto
			$stmt->bindParam(':limite_max', $datos['limite_participantes_max']);
			$stmt->bindParam(':latitud', $datos['latitud']);
			$stmt->bindParam(':longitud', $datos['longitud']);
			$stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
			$stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
			$stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
			$stmt->bindParam(':privacidad', $datos['privacidad']);
			$stmt->bindParam(':direccion', $datos['direccion']);
			$stmt->execute();

			$id_actividad = $this->db->lastInsertId();

			// Insertar al creador como participante
			$sqlParticipante = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado)
								VALUES (:id_actividad, :id_usuario, 'creador', 'aceptado')";
			$stmtPart = $this->db->prepare($sqlParticipante);
			$stmtPart->execute([
				':id_actividad' => $id_actividad,
				':id_usuario' => $datos['id_creador']
			]);

			$this->db->commit();
			return $id_actividad;
		} catch (PDOException $e) {
			$this->db->rollBack();
			error_log("Error al crear actividad: " . $e->getMessage());
			$_SESSION['error_crear_actividad'] = "Error en la base de datos: " . $e->getMessage();
			return false;
		}
	}

    public function obtenerDetalleCompleto($id_actividad) {
        // Se usa CONCAT_WS para omitir espacios si algún apellido está vacío o nulo
        $sql = "SELECT a.*, ta.nombre_tipo AS categoria, 
                       CONCAT_WS(' ', u.nombre, u.apellido_paterno, u.apellido_materno) AS organizador_nombre,
                       u.id_usuario AS organizador_id,
                       a.fecha_creacion AS fecha_publicacion
                FROM actividades a
                INNER JOIN tipos_actividad ta ON a.id_tipo = ta.id_tipo
                INNER JOIN usuarios u ON a.id_creador = u.id_usuario
                WHERE a.id_actividad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$actividad) return null;

        // Formatear fechas directamente de los campos de la actividad
        $actividad['fecha_inicio'] = date('Y-m-d', strtotime($actividad['fecha_inicio']));
        $actividad['hora_inicio'] = date('H:i', strtotime($actividad['fecha_inicio']));
        $actividad['fecha_fin'] = date('Y-m-d', strtotime($actividad['fecha_fin']));
        $actividad['hora_fin'] = date('H:i', strtotime($actividad['fecha_fin']));

        // Contar participantes confirmados
        $sqlConfirmados = "SELECT COUNT(*) as total 
                           FROM participantes 
                           WHERE id_actividad = :id AND estado = 'aceptado'";
        $stmtConf = $this->db->prepare($sqlConfirmados);
        $stmtConf->execute([':id' => $id_actividad]);
        $actividad['asistentes_confirmados'] = (int)$stmtConf->fetchColumn();

        // Capacidad
        $actividad['capacidad_max'] = $actividad['limite_participantes_max'] ?? 'Sin límite';
        $actividad['capacidad_min'] = $actividad['limite_participantes_min'] ?? 1;

        // Tipo de acceso legible
        switch ($actividad['privacidad']) {
            case 'publica': $actividad['tipo_acceso_legible'] = 'Pública (cualquiera puede unirse)'; break;
            case 'privada': $actividad['tipo_acceso_legible'] = 'Privada (solo invitados)'; break;
            case 'por_aprobacion': $actividad['tipo_acceso_legible'] = 'Por aprobación (requiere autorización)'; break;
            default: $actividad['tipo_acceso_legible'] = 'No especificado';
        }

        // Requisitos como array
        $actividad['requisitos_array'] = !empty($actividad['requisitos']) ? explode("\n", $actividad['requisitos']) : [];
        $actividad['incluye_array'] = [];

        // Participantes pendientes/invitados
        $sqlExtra = "SELECT COUNT(*) as extra 
                     FROM participantes 
                     WHERE id_actividad = :id AND estado IN ('pendiente', 'invitado')";
        $stmtExtra = $this->db->prepare($sqlExtra);
        $stmtExtra->execute([':id' => $id_actividad]);
        $actividad['asistentes_extra'] = (int)$stmtExtra->fetchColumn();

        // Fecha publicación
        $actividad['hora_publicacion'] = date('H:i', strtotime($actividad['fecha_publicacion']));
        $actividad['fecha_publicacion'] = date('Y-m-d', strtotime($actividad['fecha_publicacion']));

        // Coordenadas
        $actividad['lat'] = $actividad['latitud'];
        $actividad['lng'] = $actividad['longitud'];
        $actividad['direccion'] = "Coordenadas: {$actividad['latitud']}, {$actividad['longitud']}";

        return $actividad;
    }

    // Verificar si un usuario es participante aceptado (incluye creador/organizador)
    public function esParticipanteActivo($id_actividad, $id_usuario) {
        $sql = "
            SELECT 1 FROM participantes
            WHERE id_actividad = :id_actividad 
            AND id_usuario = :id_usuario
            AND estado = 'aceptado'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_actividad' => $id_actividad,
            ':id_usuario'   => $id_usuario
        ]);

        return (bool) $stmt->fetchColumn();
    }

        // Obtener todos los participantes (para chat, lista, etc.) obtenerActividadesPorParticipante
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

    // Reseñas (con ajuste de nombre de usuario)
    public function obtenerResenas($id_actividad) {
        $sql = "SELECT r.*, 
                       CONCAT_WS(' ', u.nombre, u.apellido_paterno, u.apellido_materno) AS usuario_nombre, 
                       u.foto_perfil
                FROM resenas r
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_actividad = :id
                ORDER BY r.fecha_resena DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function puedeResenar($id_actividad, $id_usuario) {
        $sqlParticipante = "SELECT 1 FROM participantes 
                            WHERE id_actividad = :id_act AND id_usuario = :id_user AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sqlParticipante);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        if (!$stmt->fetchColumn()) return false;

        $sqlAct = "SELECT estado FROM actividades WHERE id_actividad = :id";
        $stmtAct = $this->db->prepare($sqlAct);
        $stmtAct->execute([':id' => $id_actividad]);
        $estado = $stmtAct->fetchColumn();
        if ($estado !== 'finalizada') return false;

        $sqlResena = "SELECT 1 FROM resenas WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $stmtRes = $this->db->prepare($sqlResena);
        $stmtRes->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        return !$stmtRes->fetchColumn();
    }

    public function guardarResena($id_actividad, $id_usuario, $calificacion, $comentario) {
        $sql = "INSERT INTO resenas (id_actividad, id_usuario, calificacion, comentario) 
                VALUES (:id_act, :id_user, :cal, :com)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_act' => $id_actividad,
            ':id_user' => $id_usuario,
            ':cal' => $calificacion,
            ':com' => $comentario
        ]);
    }

    // Actualizar datos de actividad (respetando restricciones desde controlador)
    public function actualizarActividad($id, $datos, $foto_blob = null) {
        try {
            $sql = "UPDATE actividades SET 
                    nombre = :nombre,
                    id_tipo = :id_tipo,
                    descripcion = :descripcion,
                    requisitos = :requisitos,
                    edad_minima = :edad_minima,
                    edad_maxima = :edad_maxima,
                    limite_participantes_min = :limite_min,
                    limite_participantes_max = :limite_max,
                    latitud = :latitud,
                    longitud = :longitud,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    privacidad = :privacidad";
            if ($foto_blob !== null) {
                $sql .= ", foto_actividad = :foto";
            }
            $sql .= " WHERE id_actividad = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':id_tipo', $datos['id_tipo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':requisitos', $datos['requisitos']);
            $stmt->bindParam(':edad_minima', $datos['edad_minima']);
            $stmt->bindParam(':edad_maxima', $datos['edad_maxima']);
            $stmt->bindParam(':limite_min', $datos['limite_participantes_min']);
            $stmt->bindParam(':limite_max', $datos['limite_participantes_max']);
            $stmt->bindParam(':latitud', $datos['latitud']);
            $stmt->bindParam(':longitud', $datos['longitud']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            $stmt->bindParam(':privacidad', $datos['privacidad']);
            $stmt->bindParam(':id', $id);
            if ($foto_blob !== null) {
                $stmt->bindParam(':foto', $foto_blob, PDO::PARAM_LOB);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar actividad: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar actividad (solo si finalizada o cancelada)
    public function eliminarActividad($id) {
        $sql = "DELETE FROM actividades WHERE id_actividad = :id AND estado IN ('finalizada', 'cancelada')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

        // Contar miembros (participantes con rol 'miembro' y estado 'aceptado')
    public function contarMiembros($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND rol = 'miembro' AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }

        // Contar participantes aceptados
    public function contarParticipantesAceptados($id_actividad) {
        $sql = "SELECT COUNT(*) FROM participantes WHERE id_actividad = :id AND estado = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_actividad]);
        return (int)$stmt->fetchColumn();
    }
	
	public function contarActividadesParticipante($id_usuario) {
		$sql = "SELECT COUNT(DISTINCT p.id_actividad)
				FROM participantes p
				INNER JOIN actividades a ON p.id_actividad = a.id_actividad
				WHERE p.id_usuario = :id 
				  AND p.estado = 'aceptado'
				  AND p.rol = 'miembro'
				  AND a.estado NOT IN ('cancelada')";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id' => $id_usuario]);
		return (int)$stmt->fetchColumn();
	}

	public function obtenerActividadesPorParticipante($id_usuario, $tipo = 'todas') {
		$params = [':usuario_id' => $id_usuario];

		$sqlBase = "
			SELECT DISTINCT 
				a.id_actividad,
				a.nombre,
				a.descripcion,
				a.limite_participantes_min,
				a.limite_participantes_max,
				a.fecha_inicio,
				a.fecha_fin,
				a.privacidad,
				a.estado,
				a.foto_actividad,
				a.id_creador,
				ta.nombre_tipo AS categoria,
				CONCAT_WS(' ', u.nombre, u.apellido_paterno, u.apellido_materno) AS creador_nombre,

				CASE 
					WHEN a.id_creador = :usuario_id THEN 'creador'
					ELSE p.rol
				END AS rol_usuario

			FROM actividades a

			INNER JOIN tipos_actividad ta 
				ON a.id_tipo = ta.id_tipo

			INNER JOIN usuarios u 
				ON a.id_creador = u.id_usuario

			LEFT JOIN participantes p 
				ON a.id_actividad = p.id_actividad
				AND p.id_usuario = :usuario_id
				AND p.estado = 'aceptado'

			WHERE (
				a.id_creador = :usuario_id
				OR (
					p.id_usuario = :usuario_id
					AND p.rol IN ('miembro', 'organizador', 'creador')
				)
			)
		";

		// filtro próximas/pasadas
		if ($tipo === 'proximas') {
			$sqlBase .= "
				AND (
					a.estado IN ('pendiente', 'en_curso')
					AND a.fecha_fin >= NOW()
				)
				ORDER BY a.fecha_inicio ASC
			";

		} elseif ($tipo === 'pasadas') {
			$sqlBase .= "
				AND (
					a.estado = 'finalizada'
					OR a.fecha_fin < NOW()
				)
				ORDER BY a.fecha_fin DESC
			";

		} else {
			$sqlBase .= "
				ORDER BY a.fecha_inicio DESC
			";
		}

		$stmt = $this->db->prepare($sqlBase);
		$stmt->execute($params);

		$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($actividades as &$act) {
			$act['fecha_inicio_formateada'] = date(
				'd M, Y · H:i',
				strtotime($act['fecha_inicio'])
			);

			$act['fecha_fin_formateada'] = date(
				'd M, Y · H:i',
				strtotime($act['fecha_fin'])
			);
		}

		return $actividades;
	}
	
	public function obtenerAsistentesLimitados($id_actividad, $limite = 4) {
		$sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil
				FROM participantes p
				JOIN usuarios u ON p.id_usuario = u.id_usuario
				WHERE p.id_actividad = :id_act AND p.estado = 'aceptado'
				ORDER BY p.fecha_solicitud ASC
				LIMIT :limite";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id_act', $id_actividad, PDO::PARAM_INT);
		$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
		$stmt->execute();
		$asistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($asistentes as &$a) {
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
		return $asistentes;
	}
	
	public function obtenerEstadisticasResenas($id_actividad) {
		$sql = "SELECT 
					ROUND(AVG(calificacion), 1) AS promedio,
					SUM(CASE WHEN calificacion = 5 THEN 1 ELSE 0 END) AS estrellas_5,
					SUM(CASE WHEN calificacion = 4 THEN 1 ELSE 0 END) AS estrellas_4,
					SUM(CASE WHEN calificacion = 3 THEN 1 ELSE 0 END) AS estrellas_3,
					SUM(CASE WHEN calificacion = 2 THEN 1 ELSE 0 END) AS estrellas_2,
					SUM(CASE WHEN calificacion = 1 THEN 1 ELSE 0 END) AS estrellas_1,
					COUNT(*) AS total
				FROM resenas
				WHERE id_actividad = :id_act";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad]);
		$stats = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$stats['total']) {
			$stats['promedio'] = 0;
			$stats['estrellas_5'] = $stats['estrellas_4'] = $stats['estrellas_3'] = $stats['estrellas_2'] = $stats['estrellas_1'] = 0;
		}
		// Calcular porcentajes para las barras
		$total = $stats['total'] ?: 1;
		$stats['porcentaje_5'] = round(($stats['estrellas_5'] / $total) * 100);
		$stats['porcentaje_4'] = round(($stats['estrellas_4'] / $total) * 100);
		$stats['porcentaje_3'] = round(($stats['estrellas_3'] / $total) * 100);
		$stats['porcentaje_2'] = round(($stats['estrellas_2'] / $total) * 100);
		$stats['porcentaje_1'] = round(($stats['estrellas_1'] / $total) * 100);
		return $stats;
	}
	
	public function obtenerEstadoParticipacion($id_actividad, $id_usuario) {
		$sql = "SELECT estado, rol FROM participantes 
				WHERE id_actividad = :id_act AND id_usuario = :id_user";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	// Obtener solicitudes de participación pendientes (para actividades con privacidad 'por_aprobacion')
	public function obtenerSolicitudesPendientesParticipacion($id_actividad) {
		$sql = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.foto_perfil,
					   TIMESTAMPDIFF(YEAR, u.fecha_nacimiento, CURDATE()) AS edad
				FROM participantes p
				INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
				WHERE p.id_actividad = :id_act AND p.estado = 'pendiente'
				ORDER BY p.fecha_solicitud ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad]);
		$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($solicitudes as &$s) {
			$s['nombre_completo'] = trim($s['nombre'] . ' ' . $s['apellido_paterno'] . ' ' . $s['apellido_materno']);
			if (!empty($s['foto_perfil'])) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_buffer($finfo, $s['foto_perfil']);
				finfo_close($finfo);
				$s['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($s['foto_perfil']);
			} else {
				$s['foto_base64'] = null;
			}
		}
		return $solicitudes;
	}

	// Cancelar todas las solicitudes/invitaciones pendientes de una actividad (cambiar a 'rechazado' o eliminar)
	public function cancelarSolicitudesPendientes($id_actividad) {
		$sql = "UPDATE participantes SET estado = 'rechazado' WHERE id_actividad = :id_act AND estado IN ('pendiente', 'invitado')";
		$stmt = $this->db->prepare($sql);
		return $stmt->execute([':id_act' => $id_actividad]);
	}

	// Obtener participantes aceptados excluyendo al creador (para cálculos de límites)
	public function contarMiembrosExcluyendoCreador($id_actividad) {
		$sql = "SELECT COUNT(*) FROM participantes 
				WHERE id_actividad = :id_act AND estado = 'aceptado' AND rol != 'creador'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad]);
		return (int)$stmt->fetchColumn();
	}

	// Obtener la edad de un usuario
	public function obtenerEdadUsuario($id_usuario) {
		$sql = "SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM usuarios WHERE id_usuario = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id' => $id_usuario]);
		return $stmt->fetchColumn();
	}

	// Obtener el participante más joven y más viejo (para restricciones de edad)
	public function obtenerExtremosEdadParticipantes($id_actividad) {
		$sql = "SELECT MIN(TIMESTAMPDIFF(YEAR, u.fecha_nacimiento, CURDATE())) AS min_edad,
					   MAX(TIMESTAMPDIFF(YEAR, u.fecha_nacimiento, CURDATE())) AS max_edad
				FROM participantes p
				INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
				WHERE p.id_actividad = :id_act AND p.estado = 'aceptado' AND p.rol != 'creador'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return ['min' => $result['min_edad'] ?? null, 'max' => $result['max_edad'] ?? null];
	}

	// Verificar si un usuario tiene conflicto de horario con otras actividades (creador u organizador)
	public function tieneConflictoHorario($id_usuario, $fecha_inicio, $fecha_fin, $id_actividad_excluir = null) {
		$sql = "SELECT a.id_actividad
				FROM actividades a
				INNER JOIN participantes p ON a.id_actividad = p.id_actividad
				WHERE p.id_usuario = :id_user 
				  AND p.estado = 'aceptado'
				  AND (p.rol = 'creador' OR p.rol = 'organizador')
				  AND a.estado IN ('pendiente', 'en_curso')
				  AND ( (a.fecha_inicio < :fin AND a.fecha_fin > :inicio) )
				  AND a.id_actividad != :excluir";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([
			':id_user' => $id_usuario,
			':inicio' => $fecha_inicio,
			':fin' => $fecha_fin,
			':excluir' => $id_actividad_excluir
		]);
		return $stmt->fetchColumn() !== false;
	}

	// Actualizar el estado de una solicitud de participación (aceptar/rechazar)
	public function responderSolicitudParticipacion($id_actividad, $id_usuario, $accion) {
		$nuevo_estado = ($accion === 'aceptar') ? 'aceptado' : 'rechazado';
		$sql = "UPDATE participantes SET estado = :estado 
				WHERE id_actividad = :id_act AND id_usuario = :id_user AND estado = 'pendiente'";
		$stmt = $this->db->prepare($sql);
		return $stmt->execute([':estado' => $nuevo_estado, ':id_act' => $id_actividad, ':id_user' => $id_usuario]);
	}

	// Obtener todos los participantes (incluyendo creador) con su rol y email
	public function obtenerTodosParticipantesConEmail($id_actividad) {
		$sql = "SELECT u.id_usuario, u.email, p.rol
				FROM participantes p
				INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
				WHERE p.id_actividad = :id_act AND p.estado = 'aceptado'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute([':id_act' => $id_actividad]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}
?>
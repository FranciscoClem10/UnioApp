<?php
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloNotificacion.php';
class ControladorAmigos {
    public function nuevosAmigos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $resultados = [];
        $termino = '';
        if (isset($_GET['buscar']) && strlen(trim($_GET['buscar'])) > 2) {
            $termino = trim($_GET['buscar']);
            $resultados = $modelo->buscarUsuariosConRelacion($_SESSION['usuario_id'], $termino);
            // Añadir campo 'apellidos' para compatibilidad con vistas antiguas
            foreach ($resultados as &$usuario) {
                $usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
            }
        }
        require_once 'Vistas/Amigos/nuevosAmigos.php';
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $modelo = new ModeloUsuario();
        $id_user = $_SESSION['usuario_id'];
        $modeloNotif = new ModeloNotificacion();
        $modeloNotif->marcarLeidasPorTipos($_SESSION['usuario_id'], ['solicitud_amistad', 'amistad']);

        $amigos = $modelo->obtenerAmigos($id_user);
        $solicitudes = $modelo->obtenerSolicitudesPendientes($id_user);
        $sugerencias = $modelo->obtenerUsuugeridos($id_user, 15);
        $rechazados = $modelo->obtenerRechazados($id_user);
        $bloqueados = $modelo->obtenerBloqueados($id_user);

        require_once __DIR__ . '/../Vistas/Amigos/index.php';
    }

    public function desrechazar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_rechazado = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id_rechazado <= 0) {
            $_SESSION['error_amigos'] = "ID inválido.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->desrechazarUsuario($_SESSION['usuario_id'], $id_rechazado)) {
            $_SESSION['mensaje_amigos'] = "Usuario desrechazado. Puedes enviarle solicitud nuevamente.";
        } else {
            $_SESSION['error_amigos'] = "Error al desrechazar.";
        }
        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function bloquear() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_bloquear = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id_bloquear <= 0) {
            $_SESSION['error_amigos'] = "ID inválido.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->bloquearUsuario($_SESSION['usuario_id'], $id_bloquear)) {
            $_SESSION['mensaje_amigos'] = "Usuario bloqueado.";
        } else {
            $_SESSION['error_amigos'] = "Error al bloquear.";
        }
        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function desbloquear() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_bloqueado = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id_bloqueado <= 0) {
            $_SESSION['error_amigos'] = "ID inválido.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->desbloquearUsuario($_SESSION['usuario_id'], $id_bloqueado)) {
            $_SESSION['mensaje_amigos'] = "Usuario desbloqueado. Ahora puedes enviarle solicitud.";
        } else {
            $_SESSION['error_amigos'] = "Error al desbloquear.";
        }
        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function enviarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_receptor = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id_receptor <= 0) {
            $_SESSION['error_amigos'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->enviarSolicitudAmistad($_SESSION['usuario_id'], $id_receptor)) {
            $_SESSION['mensaje_amigos'] = "Solicitud enviada correctamente.";
            $modeloNotif = new ModeloNotificacion();
            $nombreRemitente = $_SESSION['usuario_nombre'] ?? 'Un usuario';
            $modeloNotif->crear(
                $id_receptor,
                'solicitud_amistad',
                'Nueva solicitud de amistad',
                "$nombreRemitente te ha enviado una solicitud de amistad.",
                '?c=amigos'
            );
        } else {
            $_SESSION['error_amigos'] = "No se pudo enviar la solicitud (quizás ya existe).";
        }
        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function responder() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_solicitante = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
        if ($id_solicitante <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
            $_SESSION['error_amigos'] = "Datos inválidos.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $accion)) {
            $_SESSION['mensaje_amigos'] = "Solicitud $accion exitosamente.";
        } else {
            $_SESSION['error_amigos'] = "Error al procesar la solicitud.";
        }

        $modeloNotif = new ModeloNotificacion();
        if ($accion === 'aceptar') {
            $modeloNotif->crear(
                $id_solicitante,
                'amistad',
                'Solicitud de amistad aceptada',
                $_SESSION['usuario_nombre'] . ' ha aceptado tu solicitud de amistad.',
                '?c=amigos'
            );
        } else {
            $modeloNotif->crear(
                $id_solicitante,
                'amistad',
                'Solicitud de amistad rechazada',
                $_SESSION['usuario_nombre'] . ' ha rechazado tu solicitud de amistad.',
                null
            );
        }

        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function eliminarAmigo() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_amigo = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id_amigo <= 0) {
            $_SESSION['error_amigos'] = "ID de amigo inválido.";
            header('Location: ' . BASE_URL . '?c=amigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        $db = Database::getConexion();
        $sql = "DELETE FROM amistades 
                WHERE (id_solicitante = :id1 AND id_receptor = :id2 AND estado = 'aceptado')
                   OR (id_solicitante = :id2 AND id_receptor = :id1 AND estado = 'aceptado')";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([':id1' => $_SESSION['usuario_id'], ':id2' => $id_amigo])) {
            $_SESSION['mensaje_amigos'] = "Amigo eliminado correctamente.";
        } else {
            $_SESSION['error_amigos'] = "No se pudo eliminar al amigo.";
        }
        header('Location: ' . BASE_URL . '?c=amigos');
        exit;
    }

    public function verPerfil() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }
        $modelo = new ModeloUsuario();
        $usuario = $modelo->obtenerPorId($id);
        if (!$usuario) {
            die("Usuario no encontrado");
        }
        // Añadir campo 'apellidos' para compatibilidad
        $usuario['apellidos'] = trim($usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
        // Convertir foto
        if (!empty($usuario['foto_perfil'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $usuario['foto_perfil']);
            finfo_close($finfo);
            $usuario['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($usuario['foto_perfil']);
        }
        // También podemos mostrar amigos comunes, etc.
        require_once 'Vistas/Amigos/verPerfil.php';
    }

    public function buscarUsuariosJson() {
        // Deshabilitar vistas de errores que puedan romper el JSON
        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode([]);
                exit;
            }

            $id_actual = $_SESSION['usuario_id'];
            $termino = trim($_GET['q'] ?? '');
            if (strlen($termino) < 2) {
                echo json_encode([]);
                exit;
            }

            $db = Database::getConexion();

            // Consulta que incluye la relación de amistad/solicitud
            $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.foto_perfil,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
                        CASE 
                            WHEN a.estado = 'aceptado' THEN 'amigo'
                            WHEN a.estado = 'pendiente' AND a.id_solicitante = :id_actual THEN 'solicitud_enviada'
                            WHEN a.estado = 'pendiente' AND a.id_receptor = :id_actual THEN 'solicitud_recibida'
                            ELSE 'ninguna'
                        END AS relacion
                    FROM usuarios u
                    LEFT JOIN amistades a ON (a.id_solicitante = u.id_usuario AND a.id_receptor = :id_actual)
                                        OR (a.id_solicitante = :id_actual AND a.id_receptor = u.id_usuario)
                    WHERE u.activo = 1
                    AND u.id_usuario != :id_actual
                    AND ( u.nombre LIKE :term 
                            OR u.apellido_paterno LIKE :term 
                            OR u.apellido_materno LIKE :term 
                            OR u.email LIKE :term 
                            OR CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) LIKE :term )
                    AND NOT EXISTS (
                        SELECT 1 FROM amistades b
                        WHERE b.estado = 'bloqueado'
                            AND (
                                (b.id_solicitante = :id_actual AND b.id_receptor = u.id_usuario)
                                OR (b.id_solicitante = u.id_usuario AND b.id_receptor = :id_actual)
                            )
                    )
                    LIMIT 15";

            $stmt = $db->prepare($sql);
            $termParam = "%$termino%";
            $stmt->bindParam(':id_actual', $id_actual, PDO::PARAM_INT);
            $stmt->bindParam(':term', $termParam);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usuarios as &$u) {
                // Procesar foto a base64
                if (!empty($u['foto_perfil'])) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_buffer($finfo, $u['foto_perfil']);
                    finfo_close($finfo);
                    $u['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($u['foto_perfil']);
                } else {
                    $u['foto_base64'] = null;
                }
                unset($u['foto_perfil']); // no mandar el blob al frontend

                // (Opcional) Agregar un flag booleano para saber si se puede enviar solicitud
                $u['puede_enviar_solicitud'] = ($u['relacion'] == 'ninguna');
            }

            echo json_encode($usuarios);
        } catch (Exception $e) {
            error_log("Error en buscarUsuariosJson: " . $e->getMessage());
            echo json_encode([]);
        }
        exit;
    }
}
?>
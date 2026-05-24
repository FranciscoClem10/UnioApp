<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Modelos/Database.php';
require_once __DIR__ . '/../Modelos/ModeloActividad.php';
require_once __DIR__ . '/../Modelos/ModeloUsuario.php';
require_once __DIR__ . '/../Modelos/ModeloNotificacion.php';
require_once __DIR__ . '/../Modelos/ModeloOrganizador.php';

class ControladorGestionActividad {

    private $modeloActividad;
    private $modeloUsuario;
    private $modeloNotificacion;
    private $modeloOrganizador;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->modeloActividad = new ModeloActividad();
        $this->modeloUsuario = new ModeloUsuario();
        $this->modeloNotificacion = new ModeloNotificacion();
        $this->modeloOrganizador = new ModeloOrganizador();
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_actividad = (int)($_GET['id'] ?? 0);
        if ($id_actividad <= 0) die("ID de actividad no válido");

        if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
            die("No tienes permisos para gestionar esta actividad");
        }

        $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
        if (!$actividad) die("Actividad no encontrada");

        $participantes = $this->obtenerParticipantesConAsistencia($id_actividad);
        $solicitudes = $this->modeloOrganizador->obtenerSolicitudesPendientes($id_actividad);
        $organizadores = array_values(array_filter($participantes, fn($p) => in_array($p['rol'], ['creador', 'organizador'])));
        $estadisticas = $this->calcularEstadisticas($id_actividad, $participantes, $solicitudes);
        $amigos = $this->modeloUsuario->obtenerAmigos($_SESSION['usuario_id']);
        $esCreador = $this->esCreador($id_actividad, $_SESSION['usuario_id']);
        $idsParticipantes = array_column($participantes, 'id_usuario');
        $amigosDisponibles = array_values(array_filter($amigos, fn($a) => !in_array($a['id_usuario'], $idsParticipantes)));

        $datos = [
            'actividad' => $actividad,
            'participantes' => $participantes,
            'solicitudes' => $solicitudes,
            'organizadores' => $organizadores,
            'estadisticas' => $estadisticas,
            'amigos_disponibles' => $amigosDisponibles,
            'permisos' => [
                'es_creador' => $esCreador,
                'es_organizador' => !$esCreador && $this->esOrganizador($id_actividad, $_SESSION['usuario_id'])
            ]
        ];

        require_once 'Vistas/Actividad/gestion.php';
    }

    public function obtenerDatosGestion() {
        $this->verificarSesion();
        $id_actividad = (int)($_GET['id'] ?? 0);
        if (!$id_actividad) $this->respuestaJson(false, 'ID de actividad requerido');

        try {
            if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'No tienes permisos');
            }

            $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
            if (!$actividad) $this->respuestaJson(false, 'Actividad no encontrada');

            $participantes = $this->obtenerParticipantesConAsistencia($id_actividad);
            $solicitudes = $this->modeloOrganizador->obtenerSolicitudesPendientes($id_actividad);
            $organizadores = array_values(array_filter($participantes, fn($p) => in_array($p['rol'], ['creador', 'organizador'])));
            $estadisticas = $this->calcularEstadisticas($id_actividad, $participantes, $solicitudes);
            $amigos = $this->modeloUsuario->obtenerAmigos($_SESSION['usuario_id']);
            $esCreador = $this->esCreador($id_actividad, $_SESSION['usuario_id']);
            $idsParticipantes = array_column($participantes, 'id_usuario');
            $amigosDisponibles = array_values(array_filter($amigos, fn($a) => !in_array($a['id_usuario'], $idsParticipantes)));

            $this->respuestaJson(true, 'Datos cargados', [
                'actividad' => $actividad,
                'participantes' => $participantes,
                'solicitudes' => $solicitudes,
                'organizadores' => $organizadores,
                'estadisticas' => $estadisticas,
                'amigos_disponibles' => $amigosDisponibles,
                'permisos' => [
                    'es_creador' => $esCreador,
                    'es_organizador' => !$esCreador && $this->esOrganizador($id_actividad, $_SESSION['usuario_id'])
                ]
            ]);
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function aceptarSolicitud() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'No autorizado');
            }
            $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
            if (!$actividad) $this->respuestaJson(false, 'Actividad no existe');

            $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacion || $participacion['estado'] !== 'pendiente') {
                $this->respuestaJson(false, 'No hay solicitud pendiente');
            }
            $totalAceptados = $this->modeloActividad->contarParticipantesAceptados($id_actividad);
            $limiteMax = $actividad['limite_participantes_max'];
            if ($limiteMax !== null && $totalAceptados >= $limiteMax) {
                $this->respuestaJson(false, 'Actividad llena');
            }

            $ok = $this->modeloActividad->responderSolicitudParticipacion($id_actividad, $id_usuario, 'aceptar');
            if ($ok) {
                $this->modeloNotificacion->crear($id_usuario, 'actividad', 'Solicitud aceptada', "Tu solicitud para '{$actividad['nombre']}' ha sido aceptada.", "?c=actividad&a=detalle&id=$id_actividad");
                $this->respuestaJson(true, 'Solicitud aceptada');
            } else {
                $this->respuestaJson(false, 'Error al aceptar');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function rechazarSolicitud() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'No autorizado');
            }
            $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
            $ok = $this->modeloActividad->responderSolicitudParticipacion($id_actividad, $id_usuario, 'rechazar');
            if ($ok) {
                $this->modeloNotificacion->crear($id_usuario, 'actividad', 'Solicitud rechazada', "Tu solicitud para '{$actividad['nombre']}' ha sido rechazada.", null);
                $this->respuestaJson(true, 'Solicitud rechazada');
            } else {
                $this->respuestaJson(false, 'Error al rechazar');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function agregarOrganizador() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'Solo el creador puede agregar organizadores');
            }
            if (!$this->sonAmigos($_SESSION['usuario_id'], $id_usuario)) {
                $this->respuestaJson(false, 'Solo puedes agregar a tus amigos');
            }
            $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if ($participacion && in_array($participacion['estado'], ['aceptado', 'pendiente'])) {
                $this->respuestaJson(false, 'Ya es participante o tiene solicitud pendiente');
            }
            $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
            $db = Database::getConexion();
            $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) VALUES (:id_act, :id_user, 'organizador', 'pendiente') ON DUPLICATE KEY UPDATE rol = 'organizador', estado = 'pendiente'";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);

            if ($ok) {
                $this->modeloNotificacion->crear($id_usuario, 'actividad', 'Invitación a organizador', "Has sido invitado a ser organizador de '{$actividad['nombre']}'. Acepta o rechaza.", "?c=gestionActividad&a=responderInvitacionOrganizador&id_actividad=$id_actividad&token=" . md5($id_usuario . $id_actividad . 'secret'));
                $this->respuestaJson(true, 'Invitación enviada');
            } else {
                $this->respuestaJson(false, 'Error al enviar invitación');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function invitarAmigo() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_amigo = (int)($_POST['id_amigo'] ?? 0);
        if (!$id_actividad || !$id_amigo) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'No autorizado');
            }
            if (!$this->sonAmigos($_SESSION['usuario_id'], $id_amigo)) {
                $this->respuestaJson(false, 'Solo puedes invitar a tus amigos');
            }
            $existe = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_amigo);
            if ($existe) $this->respuestaJson(false, 'Ya es participante o tiene invitación');

            $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
            $totalAceptados = $this->modeloActividad->contarParticipantesAceptados($id_actividad);
            $limiteMax = $actividad['limite_participantes_max'];
            if ($limiteMax !== null && $totalAceptados >= $limiteMax) {
                $this->respuestaJson(false, 'Actividad llena');
            }
            $db = Database::getConexion();
            $sql = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) VALUES (:id_act, :id_user, 'miembro', 'invitado')";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_amigo]);

            if ($ok) {
                $invitadoPor = $_SESSION['usuario_nombre'] ?? 'Un usuario';
                $this->modeloNotificacion->crear($id_amigo, 'actividad', 'Invitación a actividad', "{$invitadoPor} te ha invitado a '{$actividad['nombre']}'. Acepta o rechaza.", "?c=gestionActividad&a=responderInvitacion&id_actividad=$id_actividad");
                $this->respuestaJson(true, 'Invitación enviada');
            } else {
                $this->respuestaJson(false, 'Error al enviar invitación');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function responderInvitacionOrganizador() {
        $this->verificarSesion();
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $accion = $_GET['accion'] ?? '';
        if (!$id_actividad || !in_array($accion, ['aceptar', 'rechazar'])) {
            $this->respuestaJson(false, 'Parámetros inválidos');
        }

        try {
            $id_usuario = $_SESSION['usuario_id'];
            $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacion || $participacion['rol'] !== 'organizador' || $participacion['estado'] !== 'pendiente') {
                $this->respuestaJson(false, 'No tienes invitación pendiente');
            }
            $db = Database::getConexion();
            if ($accion === 'aceptar') {
                $sql = "UPDATE participantes SET estado = 'aceptado' WHERE id_actividad = :id_act AND id_usuario = :id_user";
                $stmt = $db->prepare($sql);
                $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
                if ($ok) {
                    $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
                    $nombre = $_SESSION['usuario_nombre'] ?? 'Un usuario';
                    $this->modeloNotificacion->crear($actividad['id_creador'], 'actividad', 'Organizador aceptado', "$nombre ha aceptado ser organizador.", "?c=gestionActividad&a=index&id=$id_actividad");
                    $this->respuestaJson(true, 'Ahora eres organizador');
                } else {
                    $this->respuestaJson(false, 'Error al aceptar');
                }
            } else {
                $sql = "DELETE FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
                $stmt = $db->prepare($sql);
                $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
                $this->respuestaJson(true, 'Has rechazado ser organizador');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function responderInvitacion() {
        $this->verificarSesion();
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $accion = $_GET['accion'] ?? '';
        if (!$id_actividad || !in_array($accion, ['aceptar', 'rechazar'])) {
            $this->respuestaJson(false, 'Parámetros inválidos');
        }

        try {
            $id_usuario = $_SESSION['usuario_id'];
            $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacion || $participacion['estado'] !== 'invitado') {
                $this->respuestaJson(false, 'No tienes invitación pendiente');
            }
            $db = Database::getConexion();
            if ($accion === 'aceptar') {
                $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
                $totalAceptados = $this->modeloActividad->contarParticipantesAceptados($id_actividad);
                $limiteMax = $actividad['limite_participantes_max'];
                if ($limiteMax !== null && $totalAceptados >= $limiteMax) {
                    $this->respuestaJson(false, 'Actividad llena');
                }
                $sql = "UPDATE participantes SET estado = 'aceptado' WHERE id_actividad = :id_act AND id_usuario = :id_user";
                $stmt = $db->prepare($sql);
                $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
                if ($ok) {
                    $this->respuestaJson(true, 'Te has unido a la actividad');
                } else {
                    $this->respuestaJson(false, 'Error al aceptar');
                }
            } else {
                $sql = "DELETE FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
                $stmt = $db->prepare($sql);
                $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
                $this->respuestaJson(true, 'Has rechazado la invitación');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function marcarAsistencia() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            $esCreador = $this->esCreador($id_actividad, $_SESSION['usuario_id']);
            $esOrganizador = $this->esOrganizador($id_actividad, $_SESSION['usuario_id']);
            if (!$esCreador && !$esOrganizador) {
                $this->respuestaJson(false, 'No autorizado');
            }

            $participacionObjetivo = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacionObjetivo || $participacionObjetivo['estado'] !== 'aceptado') {
                $this->respuestaJson(false, 'El usuario no es participante aceptado');
            }
            if (!$esCreador && $esOrganizador && in_array($participacionObjetivo['rol'], ['creador', 'organizador'])) {
                $this->respuestaJson(false, 'Los organizadores solo pueden marcar asistencia a miembros');
            }

            $db = Database::getConexion();
            $sql = "INSERT INTO asistencia (id_instancia, id_usuario, asistio) VALUES (:id_act, :id_user, 1) ON DUPLICATE KEY UPDATE asistio = 1";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            if ($ok) $this->respuestaJson(true, 'Asistencia marcada');
            else $this->respuestaJson(false, 'Error');
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function quitarAsistencia() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            $esCreador = $this->esCreador($id_actividad, $_SESSION['usuario_id']);
            $esOrganizador = $this->esOrganizador($id_actividad, $_SESSION['usuario_id']);
            if (!$esCreador && !$esOrganizador) {
                $this->respuestaJson(false, 'No autorizado');
            }

            $participacionObjetivo = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacionObjetivo || $participacionObjetivo['estado'] !== 'aceptado') {
                $this->respuestaJson(false, 'El usuario no es participante aceptado');
            }
            if (!$esCreador && $esOrganizador && in_array($participacionObjetivo['rol'], ['creador', 'organizador'])) {
                $this->respuestaJson(false, 'Los organizadores solo pueden quitar asistencia a miembros');
            }

            $db = Database::getConexion();
            $sql = "DELETE FROM asistencia WHERE id_instancia = :id_act AND id_usuario = :id_user";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            if ($ok) $this->respuestaJson(true, 'Asistencia eliminada');
            else $this->respuestaJson(false, 'Error');
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function ascenderMiembro() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'Solo el creador puede ascender miembros');
            }
            $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
            if (!$participacion || $participacion['rol'] !== 'miembro' || $participacion['estado'] !== 'aceptado') {
                $this->respuestaJson(false, 'El usuario debe ser un miembro aceptado');
            }
            if (!$this->sonAmigos($_SESSION['usuario_id'], $id_usuario)) {
                $this->respuestaJson(false, 'Solo puedes ascender a tus amigos');
            }
            $db = Database::getConexion();
            $sql = "UPDATE participantes SET rol = 'organizador' WHERE id_actividad = :id_act AND id_usuario = :id_user";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);

            if ($ok) {
                $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
                $this->modeloNotificacion->crear($id_usuario, 'actividad', 'Ascenso a organizador', "Has sido ascendido a organizador de '{$actividad['nombre']}'.", "?c=gestionActividad&a=index&id=$id_actividad");
                $this->respuestaJson(true, 'Usuario ascendido a organizador');
            } else {
                $this->respuestaJson(false, 'Error al ascender');
            }
        } catch (Exception $e) {
            $this->respuestaJson(false, 'Error interno: ' . $e->getMessage(), null, 500);
        }
    }

    public function pasarListaManual() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $asistencias = $_POST['asistencias'] ?? [];
        if (!$id_actividad || !is_array($asistencias)) $this->respuestaJson(false, 'Datos inválidos');

        try {
            if (!$this->esCreadorOOrganizador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'No autorizado');
            }
            $db = Database::getConexion();
            $db->beginTransaction();
            $stmtDel = $db->prepare("DELETE FROM asistencia WHERE id_instancia = :id_act");
            $stmtDel->execute([':id_act' => $id_actividad]);

            $stmtIns = $db->prepare("INSERT INTO asistencia (id_instancia, id_usuario, asistio) VALUES (:id_act, :id_user, 1)");
            foreach ($asistencias as $id_user => $asistio) {
                if ($asistio) $stmtIns->execute([':id_act' => $id_actividad, ':id_user' => $id_user]);
            }
            $db->commit();
            $this->respuestaJson(true, 'Lista guardada');
        } catch (Exception $e) {
            if (isset($db)) $db->rollBack();
            $this->respuestaJson(false, 'Error: ' . $e->getMessage(), null, 500);
        }
    }

    public function eliminarParticipante() {
        $this->verificarSesion();
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        if (!$id_actividad || !$id_usuario) $this->respuestaJson(false, 'Datos incompletos');

        try {
            if (!$this->esCreador($id_actividad, $_SESSION['usuario_id'])) {
                $this->respuestaJson(false, 'Solo el creador puede eliminar participantes');
            }
            $db = Database::getConexion();
            $db->beginTransaction();
            $stmt1 = $db->prepare("DELETE FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user AND rol != 'creador'");
            $stmt1->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            $stmt2 = $db->prepare("DELETE FROM asistencia WHERE id_instancia = :id_act AND id_usuario = :id_user");
            $stmt2->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            $db->commit();
            $this->respuestaJson(true, 'Participante eliminado');
        } catch (Exception $e) {
            if (isset($db)) $db->rollBack();
            $this->respuestaJson(false, 'Error: ' . $e->getMessage(), null, 500);
        }
    }

    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->respuestaJson(false, 'No autorizado', null, 401);
            exit;
        }
    }

    private function esCreador($id_actividad, $id_usuario) {
        $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
        return $actividad && $actividad['id_creador'] == $id_usuario;
    }

    private function esOrganizador($id_actividad, $id_usuario) {
        $participacion = $this->modeloActividad->obtenerEstadoParticipacion($id_actividad, $id_usuario);
        return $participacion && $participacion['rol'] === 'organizador' && $participacion['estado'] === 'aceptado';
    }

    private function esCreadorOOrganizador($id_actividad, $id_usuario) {
        return $this->esCreador($id_actividad, $id_usuario) || $this->esOrganizador($id_actividad, $id_usuario);
    }

    private function sonAmigos($id1, $id2) {
        $db = Database::getConexion();
        $sql = "SELECT 1 FROM amistades WHERE estado = 'aceptado' AND ((id_solicitante = :id1 AND id_receptor = :id2) OR (id_solicitante = :id2 AND id_receptor = :id1))";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id1' => $id1, ':id2' => $id2]);
        return $stmt->fetchColumn() ? true : false;
    }

    private function obtenerParticipantesConAsistencia($id_actividad) {
        $db = Database::getConexion();
        $sql = "SELECT p.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno, u.foto_perfil, p.rol, p.estado, IFNULL(a.asistio, 0) as asistio
                FROM participantes p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN asistencia a ON a.id_instancia = p.id_actividad AND a.id_usuario = p.id_usuario
                WHERE p.id_actividad = :id_act AND p.estado = 'aceptado'
                ORDER BY FIELD(p.rol, 'creador', 'organizador', 'miembro'), u.nombre";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id_act' => $id_actividad]);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($participantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . ($p['apellido_paterno'] ?? '') . ' ' . ($p['apellido_materno'] ?? ''));
            if (!empty($p['foto_perfil'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $p['foto_perfil']);
                finfo_close($finfo);
                $p['foto_base64'] = 'data:' . $mime . ';base64,' . base64_encode($p['foto_perfil']);
            }
        }
        return $participantes;
    }

    private function calcularEstadisticas($id_actividad, $participantes, $solicitudes) {
        $totalAceptados = count($participantes);
        $totalSolicitudes = count($solicitudes);
        $asistentesPresentes = array_reduce($participantes, fn($carry, $p) => $carry + ($p['asistio'] ? 1 : 0), 0);
        $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
        $capacidadMax = $actividad['limite_participantes_max'] ?? 0;
        $porcentajeOcupacion = $capacidadMax > 0 ? round(($totalAceptados / $capacidadMax) * 100) : 0;
        $organizadoresCount = count(array_filter($participantes, fn($p) => in_array($p['rol'], ['organizador', 'creador'])));
        $statsResenas = $this->modeloActividad->obtenerEstadisticasResenas($id_actividad);
        $porcentajeAsistencia = $totalAceptados > 0 ? round(($asistentesPresentes / $totalAceptados) * 100) : 0;
        $actividadLlena = ($capacidadMax > 0 && $totalAceptados >= $capacidadMax);

        return [
            'total_aceptados' => $totalAceptados,
            'total_solicitudes' => $totalSolicitudes,
            'porcentaje_ocupacion' => $porcentajeOcupacion,
            'organizadores_count' => $organizadoresCount,
            'asistentes_presentes' => $asistentesPresentes,
            'promedio_resenas' => $statsResenas['promedio'] ?? 0,
            'total_resenas' => $statsResenas['total'] ?? 0,
            'porcentaje_asistencia' => $porcentajeAsistencia,
            'actividad_llena' => $actividadLlena,
            'limite_maximo' => $capacidadMax,
            'limite_minimo' => $actividad['limite_participantes_min'] ?? 1
        ];
    }

    private function respuestaJson($success, $message, $data = null, $code = 200) {
        if (ob_get_level()) ob_clean();
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }
}
?>
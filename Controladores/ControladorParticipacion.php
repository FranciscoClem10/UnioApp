<?php
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloUsuario.php';
require_once 'Modelos/ModeloNotificacion.php';
class ControladorParticipacion {

    public function solicitar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        if ($id_actividad <= 0) {
            $_SESSION['error_participacion'] = "Actividad inválida.";
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }

        $modeloAct = new ModeloActividad();
        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if (!$actividad) {
            $_SESSION['error_participacion'] = "Actividad no encontrada.";
            header('Location: ' . BASE_URL . '?c=dashboard');
            exit;
        }

        $id_usuario = $_SESSION['usuario_id'];
        $modeloUs = new ModeloUsuario();
        $usuario = $modeloUs->obtenerPorId($id_usuario);
        if (!$usuario) {
            session_destroy();
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        // 1. Verificar estado de la actividad
        if (in_array($actividad['estado'], ['finalizada', 'cancelada', 'en_curso'])) {
            $_SESSION['error_participacion'] = "Esta actividad ya no acepta participantes.";
            header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
            exit;
        }

        // 2. Verificar edad del usuario
        $fecha_nac = new DateTime($usuario['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;
        if ($edad < $actividad['edad_minima'] || $edad > $actividad['edad_maxima']) {
            $_SESSION['error_participacion'] = "No cumples con el rango de edad requerido ({$actividad['edad_minima']} - {$actividad['edad_maxima']} años).";
            header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
            exit;
        }

        // 3. Verificar si ya es participante (evitar duplicados)
        $sqlCheck = "SELECT estado, rol FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
        $db = Database::getConexion();
        $stmt = $db->prepare($sqlCheck);
        $stmt->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existente) {
            if ($existente['estado'] == 'aceptado') {
                $_SESSION['error_participacion'] = "Ya eres participante de esta actividad.";
            } elseif ($existente['estado'] == 'pendiente') {
                $_SESSION['error_participacion'] = "Ya tienes una solicitud pendiente.";
            } elseif ($existente['estado'] == 'invitado') {
                $_SESSION['error_participacion'] = "Has sido invitado. Revisa tus notificaciones para aceptar.";
            } else {
                $_SESSION['error_participacion'] = "No puedes volver a solicitarlo.";
            }
            header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
            exit;
        }

        // 4. Verificar capacidad máxima
        $participantesActuales = $modeloAct->contarParticipantesAceptados($id_actividad);
        if ($actividad['limite_participantes_max'] !== null && $participantesActuales >= $actividad['limite_participantes_max']) {
            $_SESSION['error_participacion'] = "La actividad ha alcanzado su límite de participantes.";
            header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
            exit;
        }

        // 5. Determinar el estado inicial según privacidad
        $nuevoEstado = '';
        $privacidad = $actividad['privacidad'];
        if ($privacidad == 'publica') {
            $nuevoEstado = 'aceptado';
        } elseif ($privacidad == 'por_aprobacion') {
            $nuevoEstado = 'pendiente';
        } elseif ($privacidad == 'privada') {
            // Verificar si existe invitación pendiente para este usuario
            $sqlInv = "SELECT 1 FROM invitaciones WHERE id_actividad = :id_act AND id_invitado_usuario = :id_user AND estado = 'pendiente'";
            $stmtInv = $db->prepare($sqlInv);
            $stmtInv->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            if ($stmtInv->fetchColumn()) {
                // Si tiene invitación, se une automáticamente como aceptado
                $nuevoEstado = 'aceptado';
                // Marcar invitación como aceptada
                $sqlUpd = "UPDATE invitaciones SET estado = 'aceptada', fecha_respuesta = NOW() WHERE id_actividad = :id_act AND id_invitado_usuario = :id_user";
                $db->prepare($sqlUpd)->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario]);
            } else {
                $_SESSION['error_participacion'] = "Esta actividad es privada. Solo puedes unirte si recibes una invitación.";
                header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
                exit;
            }
        }

        // 6. Insertar en participantes
        $sqlInsert = "INSERT INTO participantes (id_actividad, id_usuario, rol, estado) VALUES (:id_act, :id_user, 'miembro', :estado)";
        $stmtIns = $db->prepare($sqlInsert);
        $ok = $stmtIns->execute([':id_act' => $id_actividad, ':id_user' => $id_usuario, ':estado' => $nuevoEstado]);

        if ($ok) {
            if ($nuevoEstado == 'aceptado') {
                $_SESSION['exito_participacion'] = "¡Te has unido a la actividad!";
            } else {
                $_SESSION['exito_participacion'] = "Solicitud enviada. Espera la aprobación del organizador.";
            }
        } else {
            $_SESSION['error_participacion'] = "Error al procesar tu solicitud.";
        }

        // Notificar al creador de la actividad (y organizadores) según el caso
        $modeloNotif = new ModeloNotificacion();
        if ($nuevoEstado === 'pendiente') {
            // Notificar a creador y organizadores
            $sqlOrg = "SELECT id_usuario FROM participantes WHERE id_actividad = :id_act AND (rol = 'creador' OR rol = 'organizador') AND estado = 'aceptado'";
            $stmtOrg = $db->prepare($sqlOrg);
            $stmtOrg->execute([':id_act' => $id_actividad]);
            $organizadores = $stmtOrg->fetchAll(PDO::FETCH_COLUMN);
            foreach ($organizadores as $id_org) {
                $modeloNotif->crear(
                    $id_org,
                    'actividad',
                    'Nueva solicitud de unión',
                    $usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' quiere unirse a tu actividad "' . $actividad['nombre'] . '".',
                    '?c=actividad&a=editar&id=' . $id_actividad
                );
            }
        } elseif ($nuevoEstado === 'aceptado') {
            // Notificar al usuario que se unió (si se une directamente)
            $modeloNotif->crear(
                $id_usuario,
                'actividad',
                'Te has unido a la actividad',
                'Ahora eres participante de "' . $actividad['nombre'] . '".',
                '?c=actividad&a=detalle&id=' . $id_actividad
            );
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=detalle&id=' . $id_actividad);
        exit;
    }
}
?>
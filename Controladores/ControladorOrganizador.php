<?php
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloOrganizador.php';
require_once 'Modelos/ModeloNotificacion.php';

class ControladorOrganizador {

    // --- Gestión de organizadores ---
    public function agregarOrganizador() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);

        $modeloAct = new ModeloActividad();
        $modeloOrg = new ModeloOrganizador();

        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if ($actividad['id_creador'] != $_SESSION['usuario_id']) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($id_usuario <= 0) {
            $_SESSION['error_edicion'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modeloOrg->agregarOrganizador($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Organizador agregado.";
        } else {
            $_SESSION['error_edicion'] = "Error al agregar organizador.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function quitarOrganizador() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);

        $modeloAct = new ModeloActividad();
        $modeloOrg = new ModeloOrganizador();

        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if ($actividad['id_creador'] != $_SESSION['usuario_id']) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modeloOrg->quitarOrganizador($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Organizador removido.";
        } else {
            $_SESSION['error_edicion'] = "Error al remover organizador.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    // --- Gestión de solicitudes de unión ---
    public function aceptarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);

        $modeloAct = new ModeloActividad();
        $modeloOrg = new ModeloOrganizador();

        // Verificar permisos
        if (!$modeloOrg->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        // Obtener datos de la actividad (para el nombre en la notificación)
        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if (!$actividad) {
            $_SESSION['error_edicion'] = "Actividad no encontrada.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        // Cambiar estado
        if ($modeloOrg->cambiarEstadoParticipante($id_actividad, $id_usuario, 'aceptado')) {
            $_SESSION['exito_edicion'] = "Solicitud aceptada.";

            // Notificación de ACEPTACIÓN
            $modeloNotif = new ModeloNotificacion();
            $modeloNotif->crear(
                $id_usuario,
                'actividad',
                'Solicitud aceptada',
                'Tu solicitud para la actividad "' . htmlspecialchars($actividad['nombre']) . '" ha sido ACEPTADA.',
                '?c=actividad&a=detalle&id=' . $id_actividad
            );
        } else {
            $_SESSION['error_edicion'] = "Error al aceptar.";
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function rechazarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);

        $modeloAct = new ModeloActividad();
        $modeloOrg = new ModeloOrganizador();

        if (!$modeloOrg->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        $actividad = $modeloAct->obtenerPorId($id_actividad);
        if (!$actividad) {
            $_SESSION['error_edicion'] = "Actividad no encontrada.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        if ($modeloOrg->cambiarEstadoParticipante($id_actividad, $id_usuario, 'rechazado')) {
            $_SESSION['exito_edicion'] = "Solicitud rechazada.";

            // Notificación de RECHAZO
            $modeloNotif = new ModeloNotificacion();
            $modeloNotif->crear(
                $id_usuario,
                'actividad',
                'Solicitud rechazada',
                'Tu solicitud para la actividad "' . htmlspecialchars($actividad['nombre']) . '" ha sido RECHAZADA.',
                null
            );
        } else {
            $_SESSION['error_edicion'] = "Error al rechazar.";
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function expulsarParticipante() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_GET['id_actividad'] ?? 0);
        $id_usuario = (int)($_GET['id_usuario'] ?? 0);

        $modeloOrg = new ModeloOrganizador();

        if (!$modeloOrg->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }
        if ($modeloOrg->expulsarParticipante($id_actividad, $id_usuario)) {
            $_SESSION['exito_edicion'] = "Participante expulsado.";
        } else {
            $_SESSION['error_edicion'] = "Error al expulsar.";
        }
        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    // --- Invitaciones ---
    public function enviarInvitacion() {
        if (!isset($_SESSION['usuario_id'])) exit;
        $id_actividad = (int)($_POST['id_actividad'] ?? 0);
        $tipo = $_POST['tipo_invitacion'] ?? '';
        $destinatario = trim($_POST['destinatario'] ?? '');

        $modeloAct = new ModeloActividad();
        $modeloOrg = new ModeloOrganizador();

        if (!$modeloOrg->esOrganizadorOCreador($id_actividad, $_SESSION['usuario_id'])) {
            $_SESSION['error_edicion'] = "No autorizado.";
            header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
            exit;
        }

        $resultado = false;
        $id_invitado = null;
        if ($tipo === 'usuario') {
            $id_usuario = (int)$destinatario;
            $resultado = $modeloOrg->invitarUsuario($id_actividad, $_SESSION['usuario_id'], $id_usuario);
            $id_invitado = $id_usuario;
        } elseif ($tipo === 'email') {
            $resultado = $modeloOrg->invitarEmail($id_actividad, $_SESSION['usuario_id'], $destinatario);
            $id_invitado = null; // No hay notificación para email externo
        }

        if ($resultado) {
            $_SESSION['exito_edicion'] = "Invitación enviada.";

            // Si la invitación fue a un usuario registrado, enviar notificación
            if ($tipo === 'usuario' && $id_invitado) {
                $actividad = $modeloAct->obtenerPorId($id_actividad);
                $modeloNotif = new ModeloNotificacion();
                $modeloNotif->crear(
                    $id_invitado,
                    'invitacion',
                    'Invitación a actividad',
                    'Has sido invitado a la actividad "' . $actividad['nombre'] . '".',
                    '?c=actividad&a=detalle&id=' . $id_actividad
                );
            }
        } else {
            $_SESSION['error_edicion'] = "Error al enviar invitación.";
        }

        header('Location: ' . BASE_URL . '?c=actividad&a=editar&id=' . $id_actividad);
        exit;
    }

    public function buscarAmigos() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $term = $_GET['term'] ?? '';
        if (strlen($term) < 2) {
            echo json_encode([]);
            exit;
        }
        $modeloOrg = new ModeloOrganizador();
        $amigos = $modeloOrg->buscarAmigosParaInvitacion($_SESSION['usuario_id'], $term);
        // Agregar nombre_completo a cada resultado
        foreach ($amigos as &$a) {
            $a['nombre_completo'] = trim(($a['nombre'] ?? '') . ' ' . ($a['apellido_paterno'] ?? '') . ' ' . ($a['apellido_materno'] ?? ''));
        }
        header('Content-Type: application/json');
        echo json_encode($amigos);
        exit;
    }
}
?>
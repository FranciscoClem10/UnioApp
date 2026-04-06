<?php
require_once 'Modelos/ModeloUsuario.php';
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
            $resultados = $modelo->buscarNuevosAmigos($_SESSION['usuario_id'], $termino);
        }
        require_once 'Vistas/Amigos/nuevosAmigos.php';
    }

    public function enviarSolicitud() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_receptor = (int)($_GET['id'] ?? 0);
        if ($id_receptor <= 0) {
            $_SESSION['error_amigos'] = "Usuario inválido.";
            header('Location: ' . BASE_URL . '?c=amigos&a=nuevosAmigos');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->enviarSolicitudAmistad($_SESSION['usuario_id'], $id_receptor)) {
            $_SESSION['mensaje_amigos'] = "Solicitud enviada correctamente.";
        } else {
            $_SESSION['error_amigos'] = "No se pudo enviar la solicitud (quizás ya existe).";
        }
        header('Location: ' . BASE_URL . '?c=amigos&a=nuevosAmigos');
        exit;
    }

    public function responder() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_solicitante = (int)($_GET['id'] ?? 0);
        $accion = $_GET['accion'] ?? '';
        if ($id_solicitante <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
            $_SESSION['error_amigos'] = "Datos inválidos.";
            header('Location: ' . BASE_URL . '?c=perfil');
            exit;
        }
        $modelo = new ModeloUsuario();
        if ($modelo->responderSolicitud($id_solicitante, $_SESSION['usuario_id'], $accion)) {
            $_SESSION['mensaje_amigos'] = "Solicitud $accion exitosamente.";
        } else {
            $_SESSION['error_amigos'] = "Error al procesar la solicitud.";
        }
        header('Location: ' . BASE_URL . '?c=perfil');
        exit;
    }
}
?>
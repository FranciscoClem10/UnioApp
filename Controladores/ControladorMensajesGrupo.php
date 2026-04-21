<?php
require_once 'Modelos/ModeloMensajeGrupo.php';
require_once 'Modelos/ModeloActividad.php';
require_once 'Modelos/ModeloUsuario.php';

class ControladorMensajesGrupo {
    private $modeloMensaje;
    private $modeloActividad;
    private $modeloUsuario;

    public function __construct() {
        $this->modeloMensaje   = new ModeloMensajeGrupo();
        $this->modeloActividad = new ModeloActividad();
        $this->modeloUsuario   = new ModeloUsuario();
    }

    // Página de lista de conversaciones (chats)
    public function chats() {
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . '?c=login');
			exit;
		}
		$id_usuario = $_SESSION['usuario_id'];

		// Cargar modelos necesarios
		require_once 'Modelos/ModeloMensajeGrupo.php';
		require_once 'Modelos/ModeloUsuario.php';
		$modeloMensaje = new ModeloMensajeGrupo();
		$modeloUsuario = new ModeloUsuario();

		// Obtener conversaciones de actividades
		$conversacionesActividad = $modeloMensaje->obtenerConversacionesActividad($id_usuario);

		// Obtener conversaciones privadas (implementa similar si es necesario)
		//$conversacionesPrivadas = $this->obtenerConversacionesPrivadas($id_usuario);

		require_once 'Vistas/Mensajes/chats.php';
	}

    // Ver chat de actividad
    public function verActividad() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }
        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_GET['id'] ?? 0;

        if (!$id_actividad) {
            header('Location: ' . BASE_URL . '?c=mensajes&a=chats');
            exit;
        }

        // Verificar que el usuario es participante aceptado
        if (!$this->modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            die("No tienes permiso para acceder a este chat.");
        }

        $actividad = $this->modeloActividad->obtenerPorId($id_actividad);
        $participantes = $this->modeloActividad->obtenerParticipantes($id_actividad);
        $mensajes = $this->modeloMensaje->obtenerMensajesActividad($id_actividad);

        // Marcar mensajes como leídos al entrar
        $this->modeloMensaje->marcarLeidosActividad($id_actividad, $id_usuario);

        require_once 'Vistas/Mensajes/verActividad.php';
    }

    // API: Obtener nuevos mensajes (JSON)
    public function obtenerNuevos() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_GET['id_actividad'] ?? 0;
        $ultimo_id = $_GET['ultimo_id'] ?? 0;

        if (!$id_actividad || !$this->modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        $nuevos = $this->modeloMensaje->obtenerNuevosMensajes($id_actividad, $ultimo_id);
        echo json_encode(['mensajes' => $nuevos]);
    }

    // API: Enviar mensaje (POST)
    public function enviar() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id_usuario = $_SESSION['usuario_id'];
        $id_actividad = $_POST['id_actividad'] ?? 0;
        $contenido = trim($_POST['contenido'] ?? '');

        if (!$id_actividad || !$contenido) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        if (!$this->modeloActividad->esParticipanteActivo($id_actividad, $id_usuario)) {
            echo json_encode(['error' => 'No puedes enviar mensajes a esta actividad']);
            exit;
        }

        $exito = $this->modeloMensaje->enviarMensaje($id_actividad, $id_usuario, $contenido);
        if ($exito) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error al guardar el mensaje']);
        }
    }

}
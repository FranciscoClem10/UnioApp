<?php
require_once 'Modelos/ModeloAjustes.php';
class ControladorAjustes {

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?c=login');
            exit;
        }

        // Intentar usar la sesión; si no existe, cargar de BD y guardar
        if (isset($_SESSION['ajustes'])) {
            $ajustes = $_SESSION['ajustes'];
        } else {
            $modeloAj = new ModeloAjustes();
            $ajustes = $modeloAj->obtenerAjustes($_SESSION['usuario_id']);
            $_SESSION['ajustes'] = $ajustes;
            // También guardar modo_oscuro aparte para fácil acceso
            $_SESSION['modo_oscuro'] = $ajustes['modo_oscuro'] ?? 0;
        }

        require_once 'Modelos/ModeloUsuario.php';
        $modeloUs = new ModeloUsuario();
        $usuario = $modeloUs->obtenerPorId($_SESSION['usuario_id']);

        require_once 'Vistas/Ajustes/index.php';
    }

    public function guardar() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->responderJSON(false, 'No autorizado');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(false, 'Método no permitido');
            return;
        }

        $modeloAj = new ModeloAjustes();
        $exito = $modeloAj->guardarAjustes($_SESSION['usuario_id'], $_POST);

        if ($exito) {
            // Actualizar los ajustes en sesión con los datos recién guardados
            $nuevosAjustes = $modeloAj->obtenerAjustes($_SESSION['usuario_id']);
            $_SESSION['ajustes'] = $nuevosAjustes;
            $_SESSION['modo_oscuro'] = $nuevosAjustes['modo_oscuro'] ?? 0;
            $this->responderJSON(true, 'Ajustes guardados');
        } else {
            $this->responderJSON(false, 'Error al guardar');
        }
    }

    public function restaurar() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->responderJSON(false, 'No autorizado');
            return;
        }

        $modeloAj = new ModeloAjustes();
        // Borra el registro para que se recree con valores por defecto
        $db = Database::getConexion();
        $stmt = $db->prepare("DELETE FROM ajustes WHERE id_usuario = ?");
        $stmt->execute([$_SESSION['usuario_id']]);

        // Volver a crear los ajustes por defecto y guardarlos en sesión
        $nuevosAjustes = $modeloAj->obtenerAjustes($_SESSION['usuario_id']);
        $_SESSION['ajustes'] = $nuevosAjustes;
        $_SESSION['modo_oscuro'] = $nuevosAjustes['modo_oscuro'] ?? 0;

        $this->responderJSON(true, 'Configuración restaurada');
    }

    private function responderJSON($exito, $mensaje) {
        header('Content-Type: application/json');
        echo json_encode(['exito' => $exito, 'mensaje' => $mensaje]);
        exit;
    }
}
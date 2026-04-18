<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require_once 'Config/config.php';
    require_once 'Modelos/Database.php';

    $controlador = $_GET['c'] ?? 'login';
    $accion     = $_GET['a'] ?? 'index';

    $claseControlador = 'Controlador' . ucfirst($controlador);
    $archivoControlador = "Controladores/{$claseControlador}.php";

    if (file_exists($archivoControlador)) {
        require_once $archivoControlador;
        $obj = new $claseControlador();
        if (method_exists($obj, $accion)) {
            $obj->$accion();
        } else {
            die("Método no encontrado ". $claseControlador. " ". $archivoControlador. " ".$accion);
        }
    } else {
        die("Controlador no encontrado ". $claseControlador. " ". $archivoControlador. " ".$accion);
    }
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejar toggle de modo vía GET (para evitar dependencias JS complejas)
if (isset($_GET['toggle_modo'])) {
    // Obtener estado actual
    $current = $_SESSION['modo_oscuro'] ?? ($_SESSION['ajustes']['modo_oscuro'] ?? 0);
    $newMode = $current ? 0 : 1;
    $_SESSION['modo_oscuro'] = $newMode;
    $_SESSION['ajustes']['modo_oscuro'] = $newMode;
    // Redirigir limpiando el parámetro GET
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Determinar modo oscuro: prioridad sesión directa, luego ajustes, por defecto claro
if (isset($_SESSION['modo_oscuro'])) {
    $modoOscuro = $_SESSION['modo_oscuro'];
} elseif (isset($_SESSION['ajustes']['modo_oscuro'])) {
    $modoOscuro = $_SESSION['ajustes']['modo_oscuro'];
} else {
    $modoOscuro = 0; // claro por defecto
}

$modoTexto = $modoOscuro ? 'Oscuro' : 'Claro';
$modoOpuesto = $modoOscuro ? 'Claro' : 'Oscuro';
?>
<!DOCTYPE html>
<html class="<?= $modoOscuro ? 'dark' : 'light' ?>" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unio | Ajustes</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "outline": "#767777",
                        "inverse-surface": "#0c0f0f",
                        "primary-fixed-dim": "#9581ff",
                        "on-secondary": "#121113",
                        "tertiary-dim": "#8c2a5b",
                        "on-secondary-container": "#563098",
                        "on-error": "#ffefef",
                        "on-secondary-fixed-variant": "#603aa2",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#510017",
                        "background": "#f6f6f6",
                        "surface-tint": "#5a2af7",
                        "surface-container-high": "#e1e3e3",
                        "error-dim": "#a70138",
                        "primary-fixed": "#a292ff",
                        "outline-variant": "#acadad",
                        "primary": "#5a2af7",
                        "on-background": "#2d2f2f",
                        "secondary-fixed": "#ddc8ff",
                        "surface-container": "#e7e8e8",
                        "on-surface-variant": "#5a5c5c",
                        "on-tertiary": "#ffeff2",
                        "error-container": "#f74b6d",
                        "secondary-dim": "#5f39a1",
                        "surface-bright": "#f6f6f6",
                        "on-surface": "#2d2f2f",
                        "primary-dim": "#4e0bec",
                        "secondary-container": "#ddc8ff",
                        "error": "#b41340",
                        "secondary-fixed-dim": "#d2b8ff",
                        "surface-variant": "#dbdddd",
                        "on-primary-container": "#220076",
                        "on-primary-fixed": "#000000",
                        "on-tertiary-fixed": "#37001e",
                        "on-primary-fixed-variant": "#2b0090",
                        "on-tertiary-container": "#63033b",
                        "secondary": "#6b46ae",
                        "tertiary": "#9b3667",
                        "on-tertiary-fixed-variant": "#6f1044",
                        "surface-container-low": "#f0f1f1",
                        "tertiary-fixed-dim": "#f27db0",
                        "on-primary": "#f6f0ff",
                        "inverse-on-surface": "#9c9d9d",
                        "tertiary-container": "#ff8cbd",
                        "tertiary-fixed": "#ff8cbd",
                        "surface": "#f6f6f6",
                        "on-secondary-fixed": "#431783",
                        "inverse-primary": "#927dff",
                        "surface-container-highest": "#dbdddd",
                        "surface-dim": "#d3d5d5",
                        "primary-container": "#a292ff"
                    },
                    fontFamily: {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Plus Jakarta Sans"],
                        "label": ["Plus Jakarta Sans"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    }
                }
            }
        }
    </script>
    <style>
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        #toast {
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform: translateY(20px);
            opacity: 0;
        }
        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* ========== MODO OSCURO REAL | SOBRESCRITURA DE CLASES TAILWIND ========== */
        .dark,
        .dark body {
            background-color: #121212 !important;
        }

        /* Colores de superficie y fondo */
        .dark .bg-background { background-color: #121212 !important; }
        .dark .bg-surface { background-color: #1E1E1E !important; }
        .dark .bg-surface-container { background-color: #2A2A2A !important; }
        .dark .bg-surface-container-low { background-color: #1E1E1E !important; }
        .dark .bg-surface-container-high { background-color: #2C2C2C !important; }
        .dark .bg-surface-container-highest { background-color: #353535 !important; }
        .dark .bg-surface-container-lowest { background-color: #0F0F0F !important; }
        .dark .bg-surface-bright { background-color: #3A3A3A !important; }
        .dark .bg-surface-dim { background-color: #121212 !important; }
        .dark .bg-surface-variant { background-color: #2D2D2D !important; }
		
		/* ========== SOPORTE PARA OPACIDAD EN MODO OSCURO ========== */
		.dark .bg-background\/80 {
			background-color: rgba(18, 18, 18, 0.8) !important;
		}
		.dark .bg-surface-container\/90 {
			background-color: rgba(42, 42, 42, 0.9) !important;
		}
		.dark .border-outline-variant\/10 {
			border-color: rgba(68, 64, 79, 0.1) !important;
		}
		.dark .border-outline-variant\/20 {
			border-color: rgba(68, 64, 79, 0.2) !important;
		}
        
        /* Textos sobre superficies */
        .dark .text-on-background { color: #EDEDED !important; }
        .dark .text-on-surface { color: #FFFFFF !important; }
        .dark .text-on-surface-variant { color: #C4C4C4 !important; }
        .dark .text-on-primary { color: #000000 !important; }
        .dark .text-on-secondary { color: #FFFFFF !important; }
        .dark .text-on-tertiary { color: #FFFFFF !important; }
        .dark .text-on-error { color: #FFFFFF !important; }
        
        /* Colores primarios (vibrantes en oscuro) */
        .dark .bg-primary { background-color: #BB86FC !important; }
        .dark .text-primary { color: #BB86FC !important; }
        .dark .border-primary { border-color: #BB86FC !important; }
        .dark .bg-primary-container { background-color: #3700B3 !important; }
        .dark .text-on-primary-container { color: #E5D9FF !important; }
        .dark .bg-primary-fixed { background-color: #D0BCFF !important; }
        .dark .text-on-primary-fixed { color: #000000 !important; }
        .dark .bg-primary-fixed-dim { background-color: #BB86FC !important; }
        .dark .text-primary-dim { color: #BB86FC !important; }
        .dark .bg-primary-dim { background-color: #9A67EA !important; }
        .dark .text-on-primary-fixed-variant { color: #4A2C7A !important; }
        
        /* Colores secundarios */
        .dark .bg-secondary { background-color: #CF9EFF !important; }
        .dark .text-secondary { color: #CF9EFF !important; }
        .dark .border-secondary { border-color: #CF9EFF !important; }
        .dark .bg-secondary-container { background-color: #4A2A7A !important; }
        .dark .text-on-secondary-container { color: #E5D9FF !important; }
        .dark .bg-secondary-fixed { background-color: #E5D9FF !important; }
        .dark .text-secondary-fixed { color: #1C1B1F !important; }
        .dark .bg-secondary-fixed-dim { background-color: #CF9EFF !important; }
        
        /* Colores terciarios */
        .dark .bg-tertiary { background-color: #FF88B2 !important; }
        .dark .text-tertiary { color: #FF88B2 !important; }
        .dark .bg-tertiary-container { background-color: #7A2E52 !important; }
        .dark .text-on-tertiary-container { color: #FFDAE6 !important; }
        .dark .bg-tertiary-fixed { background-color: #FFD8E4 !important; }
        .dark .text-tertiary-fixed { color: #1C1B1F !important; }
        
        /* Errores */
        .dark .bg-error { background-color: #FF5449 !important; }
        .dark .text-error { color: #FF5449 !important; }
        .dark .bg-error-container { background-color: #930017 !important; }
        .dark .text-on-error-container { color: #FFDAD6 !important; }
        
        /* Outline y variantes */
        .dark .border-outline { border-color: #827F8A !important; }
        .dark .text-outline { color: #827F8A !important; }
        .dark .border-outline-variant { border-color: #44404F !important; }
        .dark .text-outline-variant { color: #44404F !important; }
        
        /* Inverse (para elementos elevados) */
        .dark .bg-inverse-surface { background-color: #E6E1E5 !important; }
        .dark .text-inverse-on-surface { color: #1C1B1F !important; }
        .dark .bg-inverse-primary { background-color: #6750A4 !important; }
        
        /* Ajustes de surface tint y misc */
        .dark .bg-surface-tint { background-color: #BB86FC !important; }
        
        /* Fondo de tarjetas y contenedores elevados */
        .dark .shadow-md, .dark .shadow-lg, .dark .shadow-xl {
            --tw-shadow-color: rgba(0, 0, 0, 0.6);
            --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
        }
        
        /* Estilos base para body y contenedores */
        .dark body {
            background-color: #121212;
            color: #EDEDED;
        }
        
        .dark .bg-white, .dark .bg-gray-50, .dark .bg-gray-100 {
            background-color: #1E1E1E !important;
        }
        
        .dark .text-gray-600, .dark .text-gray-500 {
            color: #B0B0B0 !important;
        }
        
        .dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #2C2C2C !important;
        }
        
        /* Ajuste específico para inputs */
        .dark input, .dark select, .dark textarea {
            background-color: #2C2C2C !important;
            border-color: #44404F !important;
            color: #FFFFFF !important;
        }
        .dark input::placeholder {
            color: #9E9E9E !important;
        }
    </style>
</head>
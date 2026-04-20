<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>UnioApp</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "outline": "#767777", "inverse-surface": "#0c0f0f", "primary-fixed-dim": "#9581ff",
                        "on-secondary": "#f9efff", "tertiary-dim": "#8c2a5b", "on-secondary-container": "#563098",
                        "on-error": "#ffefef", "on-secondary-fixed-variant": "#603aa2", "surface-container-lowest": "#ffffff",
                        "on-error-container": "#510017", "background": "#f6f6f6", "surface-tint": "#5a2af7",
                        "surface-container-high": "#e1e3e3", "error-dim": "#a70138", "primary-fixed": "#a292ff",
                        "outline-variant": "#acadad", "primary": "#5a2af7", "on-background": "#2d2f2f",
                        "secondary-fixed": "#ddc8ff", "surface-container": "#e7e8e8", "on-surface-variant": "#5a5c5c",
                        "on-tertiary": "#ffeff2", "error-container": "#f74b6d", "secondary-dim": "#5f39a1",
                        "surface-bright": "#f6f6f6", "on-surface": "#2d2f2f", "primary-dim": "#4e0bec",
                        "secondary-container": "#ddc8ff", "error": "#b41340", "secondary-fixed-dim": "#d2b8ff",
                        "surface-variant": "#dbdddd", "on-primary-container": "#220076", "on-primary-fixed": "#000000",
                        "on-tertiary-fixed": "#37001e", "on-primary-fixed-variant": "#2b0090", "on-tertiary-container": "#63033b",
                        "secondary": "#6b46ae", "tertiary": "#9b3667", "on-tertiary-fixed-variant": "#6f1044",
                        "surface-container-low": "#f0f1f1", "tertiary-fixed-dim": "#f27db0", "on-primary": "#f6f0ff",
                        "inverse-on-surface": "#9c9d9d", "tertiary-container": "#ff8cbd", "tertiary-fixed": "#ff8cbd",
                        "surface": "#f6f6f6", "on-secondary-fixed": "#431783", "inverse-primary": "#927dff",
                        "surface-container-highest": "#dbdddd", "surface-dim": "#d3d5d5", "primary-container": "#a292ff"
                    },
                    fontFamily: { "headline": ["Plus Jakarta Sans"], "body": ["Plus Jakarta Sans"], "label": ["Plus Jakarta Sans"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; }
        .glass-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e7e8e8; border-radius: 10px; }
        textarea { scrollbar-width: thin; }
    </style>
</head>
<body class="text-on-surface bg-background overflow-hidden h-screen flex flex-col">
    <!-- Contenido principal: ocupa todo el espacio restante -->
    <main class="flex-1 pt-16 flex flex-col overflow-hidden pb-20 md:pb-0">
<!DOCTYPE html>

<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Centro de Ayuda UNIO</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary": "#6b46ae",
                        "surface-container-lowest": "#ffffff",
                        "primary-dim": "#4e0bec",
                        "surface-container-highest": "#dbdddd",
                        "on-primary-fixed": "#000000",
                        "background": "#f6f6f6",
                        "on-primary-fixed-variant": "#2b0090",
                        "surface": "#f6f6f6",
                        "tertiary": "#9b3667",
                        "on-tertiary-fixed-variant": "#6f1044",
                        "on-primary-container": "#220076",
                        "outline": "#767777",
                        "secondary-fixed": "#ddc8ff",
                        "error-dim": "#a70138",
                        "primary-container": "#a292ff",
                        "surface-container-low": "#f0f1f1",
                        "outline-variant": "#acadad",
                        "tertiary-dim": "#8c2a5b",
                        "surface-container-high": "#e1e3e3",
                        "on-surface": "#2d2f2f",
                        "error-container": "#f74b6d",
                        "primary": "#5a2af7",
                        "on-secondary-fixed-variant": "#603aa2",
                        "inverse-primary": "#927dff",
                        "on-error-container": "#510017",
                        "tertiary-fixed-dim": "#f27db0",
                        "on-background": "#2d2f2f",
                        "tertiary-fixed": "#ff8cbd",
                        "inverse-surface": "#0c0f0f",
                        "secondary-container": "#ddc8ff",
                        "error": "#b41340",
                        "tertiary-container": "#ff8cbd",
                        "secondary-fixed-dim": "#d2b8ff",
                        "on-secondary": "#f9efff",
                        "surface-container": "#e7e8e8",
                        "on-tertiary": "#ffeff2",
                        "inverse-on-surface": "#9c9d9d",
                        "on-tertiary-fixed": "#37001e",
                        "primary-fixed-dim": "#9581ff",
                        "on-tertiary-container": "#63033b",
                        "primary-fixed": "#a292ff",
                        "surface-tint": "#5a2af7",
                        "on-primary": "#f6f0ff",
                        "surface-dim": "#d3d5d5",
                        "on-secondary-fixed": "#431783",
                        "secondary-dim": "#5f39a1",
                        "surface-bright": "#f6f6f6",
                        "on-surface-variant": "#5a5c5c",
                        "on-secondary-container": "#563098",
                        "surface-variant": "#dbdddd",
                        "on-error": "#ffefef"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Plus Jakarta Sans"],
                        "display": ["Plus Jakarta Sans"],
                        "body": ["Plus Jakarta Sans"],
                        "label": ["Plus Jakarta Sans"]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; color: #2d2f2f; }
        .glass-nav { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .kinetic-gradient { background: linear-gradient(135deg, #5a2af7 0%, #a292ff 100%); }
    </style>
</head>
<body class="antialiased">
<!-- Shell de navegación superior -->
    <nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-md shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16">
        <div class="flex justify-between items-center px-8 h-full w-full max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-2">
                <img alt="Unio Logo" class="h-10 w-auto object-contain" alt="Unio Logo" src="../Assets/imgs/logo.png"/>
            </div>
            <div class="flex items-center gap-4">
                <a href="../index.php" class="block bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm transition-transform active:scale-95 shadow-sm">
                    <span class="material-symbols-outlined">
                        login
                    </span>
                </a>
            </div>
        </div>
        <div class="bg-slate-100 h-[1px] w-full"></div>
    </nav>

        <main class="pt-32 pb-20 px-8 max-w-7xl mx-auto">
            <!-- Hero Section -->
            <header class="mb-16 text-center">
                <h1 class="text-5xl font-extrabold text-on-surface tracking-tight mb-6">
                    Centro de privacidad
                </h1>
                <p class="text-xl text-on-surface-variant max-w-2xl mx-auto leading-relaxed">
                    Documentación técnica, guías de comunidad y marcos legales detallados para potenciar tu experiencia en el ecosistema UNIO.
                </p>
            </header>

            <div class="flex flex-col gap-10">
                <!-- PRODUCTO -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] mb-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl kinetic-gradient flex items-center justify-center text-white">
                            <span class="material-symbols-outlined" data-icon="inventory_2">
                                inventory_2
                            </span>
                        </div>
                        <h2 id="producto" class="text-2xl font-bold tracking-tight">
                            PRODUCTO
                        </h2>
                    </div> 
                    <div class="space-y-4">
                        <!-- Accordion Item 1 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="informacion_general" class="font-semibold text-lg">Información General</span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Sobre UNIO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Historia de UNIO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Descripción general</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Funcionalidades implementadas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Arquitectura del sistema</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Versión de la plataforma</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        <!-- Accordion Item 2 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="cuenta_del_usuario" class="font-semibold text-lg">
                                    Cuenta del usuario
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Registro de usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Inicio de sesión</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de la cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Perfil de usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Fotografía de perfil</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cancelación de cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Gestión de cuenta</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        <!-- Accordion Item 3 -->
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="tecnologias_y_servicios_externos" class="font-semibold text-lg">
                                    Tecnologías y servicios externos
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Stack tecnológico</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Frontend</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Backend</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>APIs y servicios externos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Entorno de desarrollo y despliegue</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>OpenStreetMap</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>CartoDB</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Leaflet.js</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 9-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>InfinityFree</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 10-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Google Analytics</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        
                        <!-- Accordion Item 4 -->
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-primary transition-colors">
                                <span id="seguridad" class="font-semibold text-lg">Seguridad</span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-primary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>A</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                </section>

                <!-- COMUNIDAD -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] mb-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-white">
                        <span class="material-symbols-outlined" data-icon="groups">
                            groups
                        </span>
                        </div>
                        <h2 id="comunidad" class="text-2xl font-bold tracking-tight">
                            COMUNIDAD
                        </h2>
                    </div>
                    <div class="space-y-4">
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="comunidad_y_uso_responsable" class="font-semibold text-lg">
                                    Comunidad y uso responsable
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Normas de la comunidad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Comportamientos esperados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Conductas prohibidas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Uso responsable de la plataforma</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar usuario</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar actividad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Mecanismo de reporte</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Recomendaciones de seguridad para encuentros presenciales</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="soporte_y_contacto" class="font-semibold text-lg">
                                    Soporte y contacto
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Centro de ayuda</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Preguntas frecuentes (FAQ)</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Soporte técnico</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar problemas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Reportar vulnerabilidades</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Contacto general</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Atención a usuarios</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-secondary transition-colors">
                                <span id="redes_y_comunidad_institucional" class="font-semibold text-lg">
                                    Redes y comunidad institucional
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-secondary/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Tecnológico Nacional de México</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>TecNM Campus Tierra Blanca</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Ingeniería en Sistemas Computacionales</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Proyecto académico</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Equipo desarrollador</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                </section>
                    
                <!-- LEGAL -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.04)]">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-on-surface flex items-center justify-center text-white">
                            <span class="material-symbols-outlined" data-icon="gavel">
                                gavel
                            </span>
                        </div>
                        <h2 id="legal" class="text-2xl font-bold tracking-tight">
                            LEGAL
                        </h2>
                    </div>
                    <div class="space-y-4">
                        <details class="group border-b border-outline-variant/20 pb-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-on-surface transition-colors">
                                <span id="privacidad_y_seguridad" class="font-semibold text-lg">
                                    Privacidad y Seguridad
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-on-surface/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Política de privacidad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Datos personales recopilados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Finalidad del tratamiento de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Tratamiento de geolocalización</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de los datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Compartición de datos con terceros</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Derechos ARCO</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Protección de menores</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 9-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Restricción de edad</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 10-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Eliminación de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 11-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Seguridad de contraseñas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 12-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Conexión segura HTTPS</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 13-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Protección contra accesos no autorizados</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer list-none py-2 hover:text-on-surface transition-colors">
                                <span id="cookies" class="font-semibold text-lg">
                                    Cookies
                                </span>
                                <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 px-4 text-on-surface-variant space-y-3 border-l-2 border-on-surface/20 ml-1">
                                <!--Subtema 1-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Política de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 2-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Aviso de uso de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 3-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies necesarias</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 4-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies analíticas</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 5-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies de preferencias</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 6-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Cookies de marketing</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 7-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Configuración de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 8-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Gestión de consentimiento</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 9-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Transferencia internacional de datos</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 10-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Actualizaciones de la política de cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                                <!--Subtema 11-->
                                <details class="group">
                                    <summary class="flex justify-between items-center cursor-pointer list-none py-2 font-medium hover:text-primary">
                                        <span>Legislación aplicable sobre cookies</span>
                                        <span class="material-symbols-outlined group-open:rotate-180 transition-transform">
                                            expand_more
                                        </span>
                                    </summary>
                                    <div class="pt-2 pl-4 text-on-surface-variant leading-relaxed">
                                        A
                                    </div>
                                </details>
                            </div>
                        </details>
                    </div>
                    <div class="mt-12 p-6 rounded-2xl bg-surface-container-low border border-outline-variant/20">
                        <p class="text-sm font-medium text-on-surface-variant mb-4">
                            Última actualización legal:
                        </p>
                        <div class="flex items-center gap-2 text-primary font-bold">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                                calendar_month
                            </span>
                            <span>
                                27 de Abril, 2026
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
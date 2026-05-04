<?php
// Asegurar que la sesión esté iniciada (si no, iniciarla)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Definir BASE_URL si no existe
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unio | Registro</title>
    <!-- Tailwind + fuentes -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-primary-fixed-variant": "#2b0090",
                        "on-tertiary-fixed": "#37001e",
                        "on-primary-fixed": "#000000",
                        "surface-container-lowest": "#ffffff",
                        "surface-tint": "#5a2af7",
                        "on-tertiary": "#ffeff2",
                        "inverse-on-surface": "#9c9d9d",
                        "on-secondary-container": "#563098",
                        "tertiary-container": "#ff8cbd",
                        "on-secondary-fixed": "#431783",
                        "primary-fixed": "#a292ff",
                        "on-background": "#2d2f2f",
                        "outline-variant": "#acadad",
                        "inverse-surface": "#0c0f0f",
                        "secondary-fixed-dim": "#d2b8ff",
                        "primary-fixed-dim": "#9581ff",
                        "on-primary": "#f6f0ff",
                        "on-error": "#ffefef",
                        "tertiary": "#9b3667",
                        "on-surface": "#2d2f2f",
                        "on-primary-container": "#220076",
                        "surface-container-low": "#f0f1f1",
                        "error-container": "#f74b6d",
                        "error": "#b41340",
                        "surface-variant": "#dbdddd",
                        "error-dim": "#a70138",
                        "surface-dim": "#d3d5d5",
                        "secondary-container": "#ddc8ff",
                        "on-secondary": "#f9efff",
                        "background": "#f6f6f6",
                        "primary": "#5a2af7",
                        "on-tertiary-fixed-variant": "#6f1044",
                        "primary-dim": "#4e0bec",
                        "primary-container": "#a292ff",
                        "secondary-fixed": "#ddc8ff",
                        "surface-container-highest": "#dbdddd",
                        "tertiary-dim": "#8c2a5b",
                        "tertiary-fixed-dim": "#f27db0",
                        "surface-container": "#e7e8e8",
                        "outline": "#767777",
                        "surface-container-high": "#e1e3e3",
                        "tertiary-fixed": "#ff8cbd",
                        "on-surface-variant": "#5a5c5c",
                        "secondary-dim": "#5f39a1",
                        "surface-bright": "#f6f6f6",
                        "surface": "#f6f6f6",
                        "on-tertiary-container": "#63033b",
                        "inverse-primary": "#927dff",
                        "on-error-container": "#510017",
                        "secondary": "#6b46ae",
                        "on-secondary-fixed-variant": "#603aa2"
                    },
                    fontFamily: {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Plus Jakarta Sans"],
                        "label": ["Plus Jakarta Sans"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .kinetic-gradient {
            background: linear-gradient(135deg, #5a2af7 0%, #a292ff 100%);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        }
        /* Estilos para el mapa y el botón geo personalizado */
        #map { height: 250px; border-radius: 0.75rem; margin-top: 0.5rem; border: 1px solid #e0e0e0; }
        .coord-info { font-size: 0.75rem; color: #5a5c5c; margin-top: 0.5rem; }
    </style>
</head>
<body class="bg-background text-on-surface min-h-screen flex flex-col">
    <main class="flex-grow flex items-center justify-center p-6 md:p-12 relative overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-tertiary/5 rounded-full blur-[100px]"></div>

        <!-- Contenedor principal más ancho en escritorio: cambiado de max-w-4xl a max-w-6xl -->
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-0 shadow-2xl shadow-on-surface/5 rounded-3xl overflow-hidden bg-surface-container-lowest">
            <!-- Lado izquierdo: branding (solo escritorio) -->
            <div class="hidden lg:flex lg:col-span-5 kinetic-gradient p-12 flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <img alt="UNIO Logo" class="h-10 w-auto mb-8 brightness-0 invert mx-auto block" src="Assets\imgs\logo.png">
                    <h2 class="text-white text-4xl font-extrabold tracking-tighter leading-tight">
                        Únete a la nueva era de eventos.
                    </h2>
                    <p class="text-white/80 mt-6 text-lg font-light leading-relaxed">
                        Gestiona, descubre y conecta en un solo lugar diseñado para la fluidez y la eficiencia.
                    </p>
                </div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Lado derecho: formulario de registro -->
            <div class="lg:col-span-7 p-8 md:p-12 lg:p-16">
                <!-- Logo móvil -->
                <div class="lg:hidden flex justify-center mb-8">
                    <img alt="UNIO Logo" class="h-8 w-auto" src="Assets\imgs\logo.png">
                </div>
                <div class="mb-8 text-center lg:text-left">
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-on-surface mb-2">
                        Crea tu cuenta
                    </h1>
                    <p class="text-on-surface-variant text-lg">
                        Completa los datos para comenzar tu experiencia.
                    </p>
                </div>

                <!-- Mostrar errores de sesión -->
                <?php if (isset($_SESSION['error_registro'])): ?>
                    <div class="mb-6 p-4 rounded-xl bg-error-container/20 text-error border-l-4 border-error text-sm">
                        <?= htmlspecialchars($_SESSION['error_registro']) ?>
                    </div>
                    <?php unset($_SESSION['error_registro']); ?>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>?c=registro&a=registrar" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Nombres -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="Ej. Juan">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Apellido paterno *</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="Pérez">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Apellido materno</label>
                            <input type="text" name="apellido_materno" id="apellido_materno"
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="Gómez">
                        </div>
                    </div>

                    <!-- Email y teléfono -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Correo electrónico *</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="tu@correo.com">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono"
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="+52 288 000 00 00">
                        </div>
                    </div>

                    <!-- Contraseña y confirmación -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Contraseña *</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="••••••••">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Confirmar contraseña *</label>
                            <input type="password" name="confirm_password" id="confirm_password" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Fecha de nacimiento y género -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Fecha de nacimiento *</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Género</label>
                            <div class="relative">
                                <select name="genero" id="genero"
                                        class="w-full appearance-none px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface pr-10 cursor-pointer">
                                    <option value="Prefiero no decir">Prefiero no decir</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">
                                    <span class="material-symbols-outlined text-xl">expand_more</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biografía y foto de perfil - CORREGIDO: ahora se apilan verticalmente en escritorio para evitar desalineación -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Biografía</label>
                            <textarea name="biografia" id="biografia" rows="3"
                                      class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/50"
                                      placeholder="Cuéntanos algo sobre ti..."></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1">Foto de perfil</label>
                            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/jpeg,image/png"
                                   class="w-full px-5 py-4 rounded-xl border-none bg-surface-container-low text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            <p class="text-xs text-outline mt-1">JPG o PNG, máximo 2MB</p>
                        </div>
                    </div>

                    <!-- Sección de ubicación con mapa -->
                    <div class="space-y-3 pt-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant ml-1 block">Ubicación (marca en el mapa)</label>
                        <div id="map"></div>
                        <button type="button" id="btn_geo"
                                class="w-full md:w-auto bg-primary/10 hover:bg-primary/20 text-primary font-semibold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">my_location</span>
                            Usar mi ubicación actual
                        </button>
                        <div class="coord-info flex items-center gap-1 text-sm flex-wrap">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                            <span id="direccion_text" class="text-on-surface-variant">Ninguna ubicación seleccionada</span>
                        </div>
                        <input type="hidden" name="latitud" id="latitud" value="">
                        <input type="hidden" name="longitud" id="longitud" value="">
                    </div>

                    <!-- Botón de envío -->
                    <div class="pt-4">
                        <button type="submit"
                                class="w-full kinetic-gradient text-white font-bold py-5 rounded-xl shadow-lg shadow-primary/20 hover:shadow-xl hover:scale-[1.01] transition-all active:scale-[0.98] text-lg">
                            Crear cuenta
                        </button>
                    </div>

                    <div class="text-center pt-6">
                        <p class="text-on-surface-variant font-medium">
                            ¿Ya tienes una cuenta?
                            <a href="<?= BASE_URL ?>?c=login" class="text-primary font-bold hover:underline ml-1">Iniciar Sesión</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="w-full border-t border-outline-variant/10 py-6 px-12 flex flex-col md:flex-row justify-between items-center gap-4 bg-surface-container-lowest">
        <div class="text-sm text-slate-500">
            © 2026 UNIO - Conectando con la realidad. Todos los derechos reservados.
        </div>
        <div class="flex gap-6">
            <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Privacidad</a>
            <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Términos</a>
            <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Ayuda</a>
        </div>
    </footer>

    <?php require_once __DIR__ . '/../../Scripts/registroS.php'; ?>
</body>
</html>
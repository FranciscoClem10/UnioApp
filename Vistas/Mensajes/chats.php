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
    <title>Mis conversaciones | UnioApp</title>
    
    <!-- Tailwind + Plugins -->
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
                        "outline": "#767777",
                        "inverse-surface": "#0c0f0f",
                        "primary-fixed-dim": "#9581ff",
                        "on-secondary": "#f9efff",
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
        
        /* Transición suave para items hover */
        .conversation-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="text-on-surface bg-background antialiased">

    <!-- Header fijo (igual al diseño original) -->
    <header class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16 px-4 md:px-8 flex justify-between items-center">
        <div class="flex items-center gap-8">
            <img src="Assets\imgs\logo.png" alt="UNIO Logo" class="h-8 md:h-10 w-auto object-contain"/>
            <nav class="hidden md:flex gap-6">
                <a href="<?= BASE_URL ?>?c=mapa" class="text-slate-500 font-medium hover:bg-slate-50 transition-colors duration-300 px-3 py-2 rounded-lg">Explorar</a>
                <a href="<?= BASE_URL ?>?c=crearActividad" class="text-slate-500 font-medium hover:bg-slate-50 transition-colors duration-300 px-3 py-2 rounded-lg">Crear</a>
                <a href="<?= BASE_URL ?>?c=mensajes&a=conversaciones" class="p-2 rounded-full text-[#5a2af7] font-bold transition-transform active:scale-95 inline-block">Mis grupos</a>
            </nav>
        </div>
        <div class="relative group">
            <button class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span id="username" class="text-sm font-medium"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></span>
            </button>
            <div class="absolute right-0 top-full mt-2 w-56 bg-white/90 backdrop-blur-xl border border-outline-variant/10 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60] py-2">
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors" href="<?= BASE_URL ?>?c=perfil">
                    <span class="material-symbols-outlined">person</span>
                    <span>Mi perfil</span>
                </a>
                <a href="<?= BASE_URL ?>?c=notificaciones" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span>Notificaciones</span>
                </a>
                <a href="<?= BASE_URL ?>?c=ajustes" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
                <a href="<?= BASE_URL ?>?c=login&a=logout" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-100 hover:text-red-600 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">logout</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>     
    </header>

    <!-- Contenido principal: solo el menú de chats (lista de conversaciones) con el estilo moderno -->
    <main class="pt-24 pb-20 md:pt-28 md:pb-8 px-4 md:px-6 max-w-5xl mx-auto">
        
        <!-- Tarjeta contenedora estilo sidebar del diseño -->
        <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm border border-surface-container-low overflow-hidden">
            <!-- Cabecera similar a "Mis grupos" del diseño -->
            <div class="p-5 md:p-6 border-b border-surface-container-low">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">forum</span>
                            Mis conversaciones
                        </h2>
                        <p class="text-sm text-on-surface-variant opacity-70">
                            Chatea con tus amigos y contactos
                        </p>
                    </div>
                    <a href="<?= BASE_URL ?>?c=dashboard" class="hidden sm:flex items-center gap-2 text-sm text-primary-dim hover:text-primary font-medium transition-colors">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        Volver al panel
                    </a>
                </div>
            </div>

            <!-- Buscador de conversaciones (igual al diseño) -->
            <div class="p-4 md:p-6 border-b border-surface-container-low">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant text-base">search</span>
                    <input type="text" id="searchConversacion" class="w-full bg-surface-container-low border-none rounded-xl py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-outline-variant transition-all" placeholder="Buscar por nombre o mensaje...">
                </div>
            </div>

            <!-- Lista de conversaciones estilo menú de chats -->
            <div class="divide-y divide-surface-container-low max-h-[60vh] md:max-h-[65vh] overflow-y-auto">
                <?php if (empty($conversaciones)): ?>
                    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                        <span class="material-symbols-outlined text-6xl text-outline-variant mb-3">chat_bubble_outline</span>
                        <h3 class="text-lg font-semibold text-on-surface">Sin conversaciones aún</h3>
                        <p class="text-sm text-on-surface-variant mt-1 max-w-xs">No tienes chats activos. ¡Conecta con otros usuarios!</p>
                        <a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos" class="mt-5 inline-flex items-center gap-2 bg-primary text-white px-5 py-2 rounded-full text-sm font-semibold shadow-md shadow-primary/20 hover:bg-primary-dim transition-all">
                            <span class="material-symbols-outlined text-base">person_add</span>
                            Buscar amigos
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversaciones as $c): ?>
                        <a href="<?= BASE_URL ?>?c=mensajes&a=verPrivado&id=<?= $c['id_usuario'] ?>" class="conversation-item flex items-center gap-3 p-4 md:p-5 hover:bg-surface-container-low transition-all duration-200 group relative">
                            <!-- Avatar con estado online/offline -->
                            <div class="relative flex-shrink-0">
                                <?php if (!empty($c['foto_base64'])): ?>
                                    <img class="w-12 h-12 rounded-xl object-cover bg-surface-container-high" src="<?= htmlspecialchars($c['foto_base64']) ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-xl bg-primary-container/30 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined text-2xl">person</span>
                                    </div>
                                <?php endif; ?>
                                <!-- Indicador de conexión (online/offline) -->
                                <?php if (isset($c['online']) && $c['online']): ?>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                                <?php else: ?>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-gray-400 border-2 border-white rounded-full"></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Información del chat -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline gap-2">
                                    <h3 class="text-[15px] font-bold text-on-surface truncate"><?= htmlspecialchars($c['nombre_completo']) ?></h3>
                                    <?php if (!empty($c['ultimo_mensaje']) && isset($c['fecha_ultimo'])): ?>
                                        <span class="text-[10px] text-outline-variant flex-shrink-0"><?= date('H:i', strtotime($c['fecha_ultimo'])) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-0.5">
                                    <p class="text-xs text-on-surface-variant truncate max-w-[180px] md:max-w-xs">
                                        <?php if (!empty($c['ultimo_mensaje'])): ?>
                                            <?= htmlspecialchars(mb_substr($c['ultimo_mensaje'], 0, 45)) ?>
                                        <?php else: ?>
                                            <span class="italic opacity-60">Sin mensajes aún</span>
                                        <?php endif; ?>
                                    </p>
                                    <!-- Badge de mensajes no leídos -->
                                    <?php if (isset($c['no_leidos']) && $c['no_leidos'] > 0): ?>
                                        <span class="bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center shadow-sm flex-shrink-0">
                                            <?= $c['no_leidos'] > 9 ? '9+' : $c['no_leidos'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <!-- Micro texto de estado online (opcional) -->
                                <?php if (isset($c['online']) && $c['online']): ?>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        <span class="text-[10px] text-green-600 font-medium">En línea</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Icono de flecha (detalle visual) -->
                            <span class="material-symbols-outlined text-outline-variant opacity-0 group-hover:opacity-100 transition-opacity text-xl">chevron_right</span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Botón volver al dashboard visible en móvil (dentro de la tarjeta) -->
            <div class="p-4 border-t border-surface-container-low block sm:hidden">
                <a href="<?= BASE_URL ?>?c=dashboard" class="flex items-center justify-center gap-2 w-full py-2.5 bg-surface-container-low text-on-surface-variant font-medium rounded-xl hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    Volver al panel
                </a>
            </div>
        </div>
    </main>

    <!-- Bottom Navigation para móviles (exactamente igual al diseño original) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full flex justify-around items-center px-4 pt-3 pb-6 h-20 bg-white/70 backdrop-blur-xl border-t border-[#acadad]/20 shadow-[0_-8px_32px_rgba(45,47,47,0.06)] z-50 rounded-t-[1.5rem]">
        <a href="<?= BASE_URL ?>?c=mapa" class="flex flex-col items-center justify-center text-[#2d2f2f] opacity-60 transition-all">
            <span class="material-symbols-outlined">explore</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Explorar</span>
        </a>
        <a href="<?= BASE_URL ?>?c=crearActividad" class="flex flex-col items-center justify-center text-[#2d2f2f] opacity-60 transition-all">
            <span class="material-symbols-outlined">add_circle</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Crear</span>
        </a>
        <a href="<?= BASE_URL ?>?c=mensajes&a=conversaciones" class="flex flex-col items-center justify-center bg-[#a292ff]/10 text-[#5a2af7] rounded-2xl px-4 py-1 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Mis grupos</span>
        </a>
    </nav>

    <!-- Filtro de conversaciones en tiempo real (buscador) -->
    <script>
        (function() {
            const searchInput = document.getElementById('searchConversacion');
            if (!searchInput) return;
            
            const conversationItems = document.querySelectorAll('.conversation-item');
            
            function filterConversations() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                conversationItems.forEach(item => {
                    // Extraemos el nombre y el último mensaje (dentro del bloque .flex-1)
                    const nameElement = item.querySelector('h3');
                    const messageElement = item.querySelector('p.text-xs');
                    const name = nameElement ? nameElement.innerText.toLowerCase() : '';
                    const message = messageElement ? messageElement.innerText.toLowerCase() : '';
                    
                    const matches = searchTerm === '' || name.includes(searchTerm) || message.includes(searchTerm);
                    item.style.display = matches ? 'flex' : 'none';
                });
            }
            
            searchInput.addEventListener('input', filterConversations);
        })();
    </script>
</body>
</html>
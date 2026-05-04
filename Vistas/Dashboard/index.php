<?php
require_once 'Modelos/ModeloNotificacion.php';
$modeloNotif = new ModeloNotificacion();
$notificacionesNoLeidas = $modeloNotif->contarNoLeidas($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <title>Unio | Explorar - Conecta con eventos reales</title>
    
    <!-- Tailwind + Google Fonts + Material Icons -->

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <!-- Leaflet CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
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
                    fontFamily: { "headline": ["Plus Jakarta Sans"], "body": ["Plus Jakarta Sans"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; overflow: hidden; height: 100vh; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); }
        
        /* Sidebar colapsable */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s ease; }
        #sidebar.collapsed { width: 0; padding-left: 0; padding-right: 0; overflow: hidden; border-right-width: 0; }
        #sidebar.collapsed > div { opacity: 0; pointer-events: none; }
        #sidebar:not(.collapsed) > div { opacity: 1; transition: opacity 0.3s ease-in-out; }
        #revealButton { transition: opacity 0.3s ease-in-out, transform 0.3s ease; pointer-events: none; opacity: 0; transform: translateX(-100%); }
        #sidebar.collapsed ~ #revealButton { pointer-events: auto; opacity: 1; transform: translateX(0); }
        
        /* Scroll suave en sidebar */
        .sidebar-scroll { overflow-y: auto; scrollbar-width: thin; }
        .user-marker { background: none; font-size: 26px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    </style>
</head>
<body class="bg-background text-on-surface overflow-hidden h-screen">

    <!-- Top Navigation Bar -->
    <header class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16 px-4 md:px-8 flex justify-between items-center">
        <div class="flex items-center gap-4 md:gap-8">
            <img src="Assets/imgs/logo.png" class="h-10 w-auto object-contain" />
            <nav class="hidden md:flex gap-6">
                <a class="p-2 rounded-full text-primary font-bold transition-transform active:scale-95" href="<?= BASE_URL ?>?c=dashboard">Explorar</a>
                <a class="text-slate-500 font-medium hover:bg-slate-50 transition-colors px-3 py-2 rounded-lg" href="<?= BASE_URL ?>?c=actividad&a=crear">Crear</a>
                <a class="text-slate-500 font-medium hover:bg-slate-50 transition-colors px-3 py-2 rounded-lg" href="<?= BASE_URL ?>?c=mensajes&a=chats">Mis grupos</a>
            </nav>
        </div>
        <div class="relative group">
            <button class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm font-medium hidden sm:inline"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
            </button>
            <div class="absolute right-0 top-full mt-2 w-56 bg-white/90 backdrop-blur-xl border border-outline-variant/10 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60] py-2">
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors" href="<?= BASE_URL ?>?c=perfil&a=index">
                    <span class="material-symbols-outlined">person</span> Mi perfil
                </a>
                <a href="<?= BASE_URL ?>?c=notificacion" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">notifications</span> Notificaciones
                    <?php if ($notificacionesNoLeidas > 0): ?>
                        <span class="ml-auto bg-error text-white text-xs rounded-full px-2 py-0.5"><?= $notificacionesNoLeidas ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>?c=perfil&a=ajustes" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface hover:bg-primary/5 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">settings</span> Ajustes
                </a>
                <a href="<?= BASE_URL ?>?c=login&a=logout" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-100 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                </a>
            </div>
        </div>
    </header>

    <main class="relative pt-16 flex overflow-hidden h-full">
        <!-- Sidebar colapsable con estadísticas y lista de actividades -->
        <aside class="relative h-full w-80 bg-[#f6f6f6] flex flex-col p-4 md:p-6 space-y-6 z-40 shrink-0 border-r border-outline-variant/20 sidebar-scroll" id="sidebar">
            <div class="flex flex-col space-y-6 flex-1 min-w-[260px]">
                <!-- Cabecera -->
                <div>
                    <h1 class="text-[1.75rem] font-bold text-on-surface">Mi Comunidad</h1>
                    <p class="text-on-surface-variant text-sm">Explora y Conecta</p>
                </div>

                <!-- Estadísticas rápidas (cards compactas) -->
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-surface-container-lowest p-2 rounded-xl text-center shadow-sm">
                        <span class="material-symbols-outlined text-primary text-xl">celebration</span>
                        <p class="text-xs font-bold"><?= $totalActividades ?></p>
                        <p class="text-[10px] text-outline">Eventos</p>
                    </div>
                    <div class="bg-surface-container-lowest p-2 rounded-xl text-center shadow-sm">
                        <span class="material-symbols-outlined text-secondary text-xl">category</span>
                        <p class="text-xs font-bold"><?= count($actividadesPorCategoria) ?></p>
                        <p class="text-[10px] text-outline">Categorías</p>
                    </div>
                    <a class="text-[10px] text-outline" href="<?= BASE_URL ?>?c=actividad&a=edicion" class="btn btn-success"><div class="bg-surface-container-lowest p-2 rounded-xl text-center shadow-sm">
                        <span class="material-symbols-outlined text-tertiary text-xl">star</span>
                        <p class="text-xs font-bold"><?= $totalMisActividades ?></p>
                        <p class="text-[10px] text-outline">Mis eventos</p>        
                    </div></a> 
                </div>

                <!-- Filtros de intereses (categorías dinámicas desde la BD) -->
                <section class="space-y-3">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-outline">Intereses Cercanos</h2>
                    <div class="flex flex-wrap gap-2" id="filtrosCategorias">
                        <?php 
                        $categoriasUnicas = array_keys($actividadesPorCategoria);
                        foreach ($categoriasUnicas as $cat): ?>
                            <span class="filtro-categoria px-4 py-2 rounded-full bg-surface-container-highest text-on-surface text-xs font-medium cursor-pointer hover:bg-primary/20 transition-colors" data-categoria="<?= htmlspecialchars($cat) ?>">
                                <?= htmlspecialchars($cat) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Lista de actividades dinámicas + búsqueda -->
                <section class="flex-1 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-outline">Eventos para Ti</h2>
                        <button id="resetFiltros" class="text-[10px] text-primary font-semibold bg-primary/10 px-2 py-1 rounded-full">Limpiar</button>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">search</span>
                        <input type="text" id="busquedaEventos" placeholder="Buscar evento..." class="w-full bg-surface-container-lowest border border-surface-container-high rounded-xl py-2 pl-9 pr-3 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                    </div>
                    <div id="listaActividades" class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                        <!-- Aquí se cargarán dinámicamente las actividades (JS) -->
                        <div class="text-center text-on-surface-variant py-6 text-sm">Cargando actividades...</div>
                    </div>
                </section>
            </div>

            <!-- Botón para colapsar sidebar -->
            <button class="absolute -right-5 top-1/2 -translate-y-1/2 w-10 h-16 bg-white border-2 border-primary/30 rounded-full shadow-lg flex items-center justify-center cursor-pointer z-50 hover:bg-primary transition-colors" id="sidebarToggle" onclick="toggleSidebar()">
                <span class="material-symbols-outlined text-2xl font-bold" id="toggleIcon">chevron_left</span>
            </button>
        </aside>

        <!-- Botón flotante para mostrar sidebar en móvil cuando está colapsado -->
        <button class="fixed left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 backdrop-blur-md rounded-xl shadow-xl border border-outline-variant/30 flex items-center justify-center z-50 text-primary hover:bg-primary hover:text-on-primary transition-all active:scale-95 md:hidden" id="revealButton" onclick="toggleSidebar()">
            <span class="material-symbols-outlined text-2xl">chevron_right</span>
        </button>

        <!-- Contenedor principal del mapa -->
        <div class="flex-1 relative bg-surface-container overflow-hidden">
            <div id="map" class="absolute inset-0 z-0"></div>
            <!-- Capa de UI flotante sobre el mapa -->
            <div class="absolute inset-0 z-10 pointer-events-none p-4 md:p-8 flex flex-col justify-between">
                <div class="flex justify-between items-start pointer-events-auto">
                    <!-- El input de búsqueda ya está en el sidebar, pero podemos dejarlo también aquí (opcional) -->
                    <div></div>
                    <div class="flex flex-col gap-2">
                        <button id="btnMiUbicacion" class="w-12 h-12 glass-card rounded-xl shadow-lg flex items-center justify-center text-on-surface active:scale-95 transition-colors hover:bg-primary/20">
                            <span class="material-symbols-outlined">my_location</span>
                        </button>
                    </div>
                </div>
                <!-- Botón flotante para crear actividad (solo escritorio, en móvil está en bottom nav) -->
                <div class="hidden md:flex justify-end pointer-events-auto">
                    <a href="<?= BASE_URL ?>?c=actividad&a=crear" class="flex items-center gap-3 bg-primary text-on-primary px-6 py-3 rounded-full shadow-lg hover:shadow-xl active:scale-90 transition-all">
                        <span class="font-bold">Crear actividad</span>
                        <span class="material-symbols-outlined">add</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full flex justify-around items-center px-4 pt-3 pb-6 h-20 bg-white/70 backdrop-blur-xl border-t border-outline-variant/20 shadow-[0_-8px_32px_rgba(45,47,47,0.06)] z-50 rounded-t-2xl">
        <a href="<?= BASE_URL ?>?c=dashboard" class="flex flex-col items-center justify-center bg-primary/10 text-primary rounded-2xl px-4 py-1">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">explore</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Explorar</span>
        </a>
        <a href="<?= BASE_URL ?>?c=actividad&a=crear" class="flex flex-col items-center justify-center text-on-surface opacity-60">
            <span class="material-symbols-outlined">add_circle</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Crear</span>
        </a>
        <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="flex flex-col items-center justify-center text-on-surface opacity-60">
            <span class="material-symbols-outlined">chat</span>
            <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Mis grupos</span>
        </a>
    </nav>

    <script>
        // --------------------------------------------------------------
        // 1. Variables PHP incrustadas en JavaScript
        // --------------------------------------------------------------
        const actividades = <?= json_encode($actividades) ?>;  // array de actividades
        const currentUserId = <?= $_SESSION['usuario_id'] ?? 0 ?>;
        const BASE_URL = "<?= BASE_URL ?>";

        let mapInstance = null;
        let markerLayer = null;
        let userMarker = null;
        let currentFilter = "";       // filtro por texto (búsqueda)
        let currentCategoria = "";    // filtro por categoría

        // --------------------------------------------------------------
        // 2. Funciones de renderizado de la lista de actividades
        // --------------------------------------------------------------
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function filtrarActividades() {
            return actividades.filter(act => {
                const matchTexto = currentFilter === "" || 
                    act.titulo.toLowerCase().includes(currentFilter) || 
                    act.categoria.toLowerCase().includes(currentFilter);
                const matchCategoria = currentCategoria === "" || act.categoria === currentCategoria;
                return matchTexto && matchCategoria && act.estado !== 'cancelada';
            });
        }

        function renderLista() {
            const container = document.getElementById('listaActividades');
            const filtradas = filtrarActividades();
            if (filtradas.length === 0) {
                container.innerHTML = `<div class="bg-surface-container-lowest rounded-xl p-4 text-center text-on-surface-variant text-sm">No hay eventos con esos filtros.</div>`;
                return;
            }
            container.innerHTML = filtradas.map(act => `
                <div class="bg-surface-container-lowest p-3 rounded-xl shadow-sm hover:translate-x-1 transition-all duration-200 cursor-pointer border border-surface-container-high" onclick="verDetalle(${act.id_actividad})">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-xl">event_note</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-on-surface">${escapeHtml(act.titulo)}</h3>
                            <p class="text-xs text-on-surface-variant">${act.fecha || 'Próximamente'} ${act.hora ? '· ' + act.hora.slice(0,5) : ''}</p>
                            <div class="flex items-center mt-1 gap-2 text-[10px] text-primary font-semibold">
                                <span>📍 ${act.latitud}, ${act.longitud}</span>
                                <span>👥 ${act.limite_personas}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Redirigir al detalle de la actividad
        window.verDetalle = function(id) {
            window.location.href = BASE_URL + "?c=actividad&a=detalle&id=" + id;
        };

        // --------------------------------------------------------------
        // 3. Mapa y marcadores (Leaflet)
        // --------------------------------------------------------------
        function actualizarMapa() {
            if (!mapInstance) return;
            if (markerLayer) mapInstance.removeLayer(markerLayer);
            markerLayer = L.layerGroup().addTo(mapInstance);
            
            const actividadesFiltradas = filtrarActividades();
            actividadesFiltradas.forEach(act => {
                if (act.latitud && act.longitud) {
                    const marker = L.marker([parseFloat(act.latitud), parseFloat(act.longitud)], { riseOnHover: true });
                    marker.bindTooltip(`<strong>${escapeHtml(act.titulo)}</strong><br>${act.categoria} · ${act.fecha || 'Próximo'}`, { sticky: true });
                    marker.on('click', () => verDetalle(act.id_actividad));
                    marker.addTo(markerLayer);
                }
            });
        }

        function initMap() {
            const defaultCenter = [18.4500, -96.3500];
            mapInstance = L.map('map').setView(defaultCenter, 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
            }).addTo(mapInstance);
            
            // Geolocalización
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        mapInstance.setView([userLat, userLng], 13);
                        if (userMarker) mapInstance.removeLayer(userMarker);
                        const customIcon = L.divIcon({ className: 'user-marker', html: '📍', iconSize: [24, 24] });
                        userMarker = L.marker([userLat, userLng], { icon: customIcon }).addTo(mapInstance);
                        userMarker.bindTooltip("Tu ubicación actual", { sticky: true });
                        actualizarMapa();
                    },
                    () => { actualizarMapa(); }
                );
            } else {
                actualizarMapa();
            }
        }

        // Botón "Mi ubicación"
        function centrarEnMiUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        mapInstance.setView([pos.coords.latitude, pos.coords.longitude], 14);
                    },
                    () => alert("No se pudo obtener tu ubicación")
                );
            } else {
                alert("Geolocalización no soportada");
            }
        }

        // --------------------------------------------------------------
        // 4. Eventos de búsqueda y filtros por categoría
        // --------------------------------------------------------------
        function aplicarFiltros() {
            renderLista();
            actualizarMapa();
        }

        // Barra de búsqueda
        const inputBusqueda = document.getElementById('busquedaEventos');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', (e) => {
                currentFilter = e.target.value.toLowerCase().trim();
                aplicarFiltros();
            });
        }

        // Filtros por categoría (etiquetas dinámicas)
        document.querySelectorAll('.filtro-categoria').forEach(el => {
            el.addEventListener('click', (e) => {
                const cat = el.getAttribute('data-categoria');
                if (currentCategoria === cat) {
                    currentCategoria = "";
                    el.classList.remove('bg-primary', 'text-on-primary');
                    el.classList.add('bg-surface-container-highest', 'text-on-surface');
                } else {
                    currentCategoria = cat;
                    document.querySelectorAll('.filtro-categoria').forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-on-primary');
                        btn.classList.add('bg-surface-container-highest', 'text-on-surface');
                    });
                    el.classList.remove('bg-surface-container-highest', 'text-on-surface');
                    el.classList.add('bg-primary', 'text-on-primary');
                }
                aplicarFiltros();
            });
        });

        // Botón reset filtros
        document.getElementById('resetFiltros')?.addEventListener('click', () => {
            currentFilter = "";
            currentCategoria = "";
            if (inputBusqueda) inputBusqueda.value = "";
            document.querySelectorAll('.filtro-categoria').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-on-primary');
                btn.classList.add('bg-surface-container-highest', 'text-on-surface');
            });
            aplicarFiltros();
        });

        // --------------------------------------------------------------
        // 5. Sidebar toggle y eventos adicionales
        // --------------------------------------------------------------
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            sidebar.classList.toggle('collapsed');
            toggleIcon.innerText = sidebar.classList.contains('collapsed') ? 'chevron_right' : 'chevron_left';
            // Forzar redibujo del mapa al cambiar tamaño del sidebar
            setTimeout(() => { if(mapInstance) mapInstance.invalidateSize(); }, 300);
        }

        document.getElementById('btnMiUbicacion')?.addEventListener('click', centrarEnMiUbicacion);
        
        // Inicialización cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            renderLista();
        });
    </script>
</body>
</html>
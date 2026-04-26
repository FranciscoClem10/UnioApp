<?php
require_once 'Modelos/ModeloNotificacion.php';
$modeloNotif = new ModeloNotificacion();
$notificacionesNoLeidas = $modeloNotif->contarNoLeidas($_SESSION['usuario_id']);

// Las siguientes variables deben ser definidas en el controlador:
// $actividades, $totalActividades, $actividadesPorCategoria, $totalMisActividades
?>

<?php include 'includes/header.php'; ?>

<!-- Estilos específicos de esta página (sin modificar header.php) -->
<style>
    .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); }
    .sidebar-scroll { overflow-y: auto; scrollbar-width: thin; }
    .user-marker { background: none; font-size: 26px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    
    /* Sidebar colapsable */
    #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s ease; }
    #sidebar.collapsed { width: 0; padding-left: 0; padding-right: 0; overflow: hidden; border-right-width: 0; }
    #sidebar.collapsed > div { opacity: 0; pointer-events: none; }
    #sidebar:not(.collapsed) > div { opacity: 1; transition: opacity 0.3s ease-in-out; }
    #revealButton { transition: opacity 0.3s ease-in-out, transform 0.3s ease; pointer-events: none; opacity: 0; transform: translateX(-100%); }
    #sidebar.collapsed ~ #revealButton { pointer-events: auto; opacity: 1; transform: translateX(0); }
</style>

<?php include 'includes/top-nav.php'; ?>

<!-- Contenido principal: sidebar + mapa -->
<div class="flex flex-1 overflow-hidden relative">
    <!-- Sidebar colapsable con estadísticas y lista de actividades -->
    <aside class="relative h-full w-80 bg-[#f6f6f6] flex flex-col p-4 md:p-6 space-y-6 z-40 shrink-0 border-r border-outline-variant/20 sidebar-scroll" id="sidebar">
        <div class="flex flex-col space-y-6 flex-1 min-w-[260px]">
            <!-- Cabecera -->
            <div>
                <h1 class="text-[1.75rem] font-bold text-on-surface">Mi Comunidad</h1>
                <p class="text-on-surface-variant text-sm">Explora y Conecta</p>
            </div>

            <!-- Estadísticas rápidas -->
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
                <a class="text-[10px] text-outline" href="<?= BASE_URL ?>?c=actividad&a=edicion">
                    <div class="bg-surface-container-lowest p-2 rounded-xl text-center shadow-sm">
                        <span class="material-symbols-outlined text-tertiary text-xl">star</span>
                        <p class="text-xs font-bold"><?= $totalMisActividades ?></p>
                        <p class="text-[10px] text-outline">Mis eventos</p>        
                    </div>
                </a> 
            </div>

            <!-- Filtros de intereses (categorías) -->
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

            <!-- Lista de actividades + búsqueda -->
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
                    <div class="text-center text-on-surface-variant py-6 text-sm">Cargando actividades...</div>
                </div>
            </section>
        </div>

        <!-- Botón para colapsar sidebar -->
        <button class="absolute -right-5 top-1/2 -translate-y-1/2 w-10 h-16 bg-white border-2 border-primary/30 rounded-full shadow-lg flex items-center justify-center cursor-pointer z-50 hover:bg-primary transition-colors" id="sidebarToggle" onclick="toggleSidebar()">
            <span class="material-symbols-outlined text-2xl font-bold" id="toggleIcon">chevron_left</span>
        </button>
    </aside>

    <!-- Botón flotante para móvil cuando sidebar está colapsado -->
    <button class="fixed left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 backdrop-blur-md rounded-xl shadow-xl border border-outline-variant/30 flex items-center justify-center z-50 text-primary hover:bg-primary hover:text-on-primary transition-all active:scale-95" id="revealButton" onclick="toggleSidebar()">
        <span class="material-symbols-outlined text-2xl">chevron_right</span>
    </button>

    <!-- Contenedor del mapa -->
    <div class="flex-1 relative bg-surface-container overflow-hidden">
        <div id="map" class="absolute inset-0 z-0"></div>
        <!-- Capa flotante sobre el mapa -->
        <div class="absolute inset-0 z-10 pointer-events-none p-4 md:p-8 flex flex-col justify-between">
            <div class="flex justify-between items-start pointer-events-auto">
                <div></div>
                <div class="flex flex-col gap-2">
                    <button id="btnMiUbicacion" class="w-12 h-12 glass-card rounded-xl shadow-lg flex items-center justify-center text-on-surface active:scale-95 transition-colors hover:bg-primary/20">
                        <span class="material-symbols-outlined">my_location</span>
                    </button>
                </div>
            </div>
            <div class="hidden md:flex justify-end pointer-events-auto">
                <a href="<?= BASE_URL ?>?c=actividad&a=crear" class="flex items-center gap-3 bg-primary text-on-primary px-6 py-3 rounded-full shadow-lg hover:shadow-xl active:scale-90 transition-all">
                    <span class="font-bold">Crear actividad</span>
                    <span class="material-symbols-outlined">add</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// Cerrar el <main> que fue abierto en header.php
echo '</main>';
include 'includes/bottom-nav.php';
?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<?php require_once __DIR__ . '/../../Scripts/DashboardS.php'; ?>

</body>
</html>
<?php
if (!isset($_SESSION['usuario_id'])) return;

// Determinar sección activa para el menú superior
$seccion_activa = '';
$controller = $_GET['c'] ?? '';
$action = $_GET['a'] ?? '';

if ($controller === 'dashboard') {
    $seccion_activa = 'explorar';
} elseif ($controller === 'actividad' && $action === 'crear') {
    $seccion_activa = 'crear';  
} elseif ($controller === 'mensajes') {
    $seccion_activa = 'mensajes';
} elseif ($controller === 'amigos') {
    $seccion_activa = '';
}
// Determinar sección activa para el menú desplegable del usuario (ya no incluye notificaciones/ajustes)
$seccion_usuario = '';
if ($controller === 'perfil' && $action === 'index') {
    $seccion_usuario = 'perfil';
} elseif ($controller === 'amigos') {
    $seccion_usuario = 'amigos';
} elseif ($controller === 'actividad' && $action === 'edicion') {
    $seccion_usuario = 'actividad';
}

// Contador de notificaciones
$notificacionesNoLeidas = 0;
if (class_exists('ModeloNotificacion')) {
    try {
        require_once __DIR__ . '/../Modelos/ModeloNotificacion.php';
        $modeloNotif = new ModeloNotificacion();
        $notificacionesNoLeidas = $modeloNotif->contarNoLeidas($_SESSION['usuario_id']);
    } catch (Exception $e) {
        // silencioso
    }
}
?>
<header class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16 px-4 md:px-8 flex justify-between items-center">
    <div class="flex items-center gap-4 md:gap-8">
        <img src="<?= BASE_URL ?>/Assets/imgs/logo.png" class="h-10 w-auto object-contain" alt="Unio Logo">
        <nav class="hidden md:flex gap-6">
            <a class="p-2 rounded-lg transition-transform active:scale-95 inline-block <?= $seccion_activa === 'explorar' ? 'text-primary font-bold' : 'text-slate-500 hover:bg-slate-50' ?>" 
            href="<?= BASE_URL ?>?c=dashboard">
                Explorar
            </a>
            <a class="p-2 rounded-lg transition-transform active:scale-95 inline-block <?= $seccion_activa === 'crear' ? 'text-primary font-bold' : 'text-slate-500 hover:bg-slate-50' ?>" 
            href="<?= BASE_URL ?>?c=actividad&a=crear">
                Crear
            </a>
            <a class="p-2 rounded-lg transition-transform active:scale-95 inline-block <?= $seccion_activa === 'mensajes' ? 'text-primary font-bold' : 'text-slate-500 hover:bg-slate-50' ?>" 
            href="<?= BASE_URL ?>?c=mensajes&a=chats">
                Mensajes
            </a>
        </nav>
    </div>

    <div class="flex items-center gap-2">
        <!-- Notificaciones (fuera del menú flotante) -->
        <a href="<?= BASE_URL ?>?c=notificacion" 
           class="relative p-2 rounded-full transition-colors hover:bg-surface-container-high <?= $controller === 'notificacion' ? 'text-primary' : 'text-slate-600' ?>">
            <span class="material-symbols-outlined">notifications</span>
            <?php if ($notificacionesNoLeidas > 0): ?>
                <span class="absolute -top-0.5 -right-0.5 bg-error text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"><?= $notificacionesNoLeidas ?></span>
            <?php endif; ?>
        </a>

        <!-- Ajustes (fuera del menú flotante) -->
        <a href="<?= BASE_URL ?>?c=perfil&a=ajustes"
           class="p-2 rounded-full transition-colors hover:bg-surface-container-high <?= ($controller === 'perfil' && $action === 'ajustes') ? 'text-primary' : 'text-slate-600' ?>">
            <span class="material-symbols-outlined">settings</span>
        </a>

        <!-- Botón que abre el menú flotante (perfil, conexiones, etc.) -->
        <div class="relative group">
            <button class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm font-medium hidden sm:inline"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></span>
            </button>
            <div class="absolute right-0 top-full mt-2 w-56 bg-white/90 backdrop-blur-xl border border-outline-variant/10 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60] py-2">
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors <?= $seccion_usuario === 'perfil' ? 'bg-primary/5 text-primary' : 'text-on-surface hover:bg-primary/5 hover:text-primary' ?>" 
                href="<?= BASE_URL ?>?c=perfil&a=index">
                    <span class="material-symbols-outlined">person</span> Mi perfil
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors <?= $seccion_usuario === 'amigos' ? 'bg-primary/5 text-primary' : 'text-on-surface hover:bg-primary/5 hover:text-primary' ?>" 
                href="<?= BASE_URL ?>?c=amigos&a=index">
                    <span class="material-symbols-outlined">group</span> Mis Conexiones
                </a>
                <a href="<?= BASE_URL ?>?c=actividad&a=edicion" 
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors <?= $seccion_usuario === 'actividad' ? 'bg-primary/5 text-primary' : 'text-on-surface hover:bg-primary/5 hover:text-primary' ?>">
                    <span class="material-symbols-outlined">event_note</span> Actividades
                </a>
                <a href="<?= BASE_URL ?>?c=login&a=logout" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-100 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                </a> 
            </div>
        </div>
    </div>
</header>
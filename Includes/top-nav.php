<?php
if (!isset($_SESSION['usuario_id'])) return;
// Necesitamos el contador de notificaciones no leídas si está disponible
$notificacionesNoLeidas = 0;
if (isset($modeloNotif) || class_exists('ModeloNotificacion')) {
    try {
        if (!isset($modeloNotif)) {
            require_once __DIR__ . '/../Modelos/ModeloNotificacion.php';
            $modeloNotif = new ModeloNotificacion();
        }
        $notificacionesNoLeidas = $modeloNotif->contarNoLeidas($_SESSION['usuario_id']);
    } catch (Exception $e) {}
}
?>
<header class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] h-16 px-4 md:px-8 flex justify-between items-center">
    <div class="flex items-center gap-4 md:gap-8">
        <img src="<?= BASE_URL ?>/Assets/imgs/logo.png" class="h-10 w-auto object-contain" alt="Unio Logo">
        <nav class="hidden md:flex gap-6">
            <a class="text-slate-500 font-medium hover:bg-slate-50 transition-colors px-3 py-2 rounded-lg <?= ($_GET['c'] ?? '') == 'dashboard' ? 'text-primary font-bold' : '' ?>" href="<?= BASE_URL ?>?c=dashboard">Explorar</a>
            <a class="text-slate-500 font-medium hover:bg-slate-50 transition-colors px-3 py-2 rounded-lg <?= (($_GET['c'] ?? '') == 'actividad' && ($_GET['a'] ?? '') == 'crear') ? 'text-primary font-bold' : '' ?>" href="<?= BASE_URL ?>?c=actividad&a=crear">Crear</a>
            <a class="p-2 rounded-full transition-transform active:scale-95 inline-block <?= ($_GET['c'] ?? '') == 'mensajes' ? 'text-primary font-bold' : 'text-slate-500 font-medium' ?>" href="<?= BASE_URL ?>?c=mensajes&a=chats">Mensajes</a>
        </nav>
    </div>
    <div class="relative group">
        <button class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined">person</span>
            <span class="text-sm font-medium hidden sm:inline"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></span>
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
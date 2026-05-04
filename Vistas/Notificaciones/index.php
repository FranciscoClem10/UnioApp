<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php'; 
?>

<!-- Ajustamos el body para que ocupe toda la pantalla y el scroll se maneje dentro del main -->
<body class="bg-background text-on-surface antialiased h-screen overflow-hidden flex flex-col">
    <?php require_once __DIR__ . '/../../includes/top-nav.php';?>

    <!-- Contenido principal: scrollable (siempre con scrollbar visible a la derecha) -->
    <main class="flex-1 overflow-y-scroll pt-20 pb-20 md:pb-12 px-4 md:px-6 max-w-4xl mx-auto w-full" id ="scroll">
        <!-- Cabecera con acciones: ahora los botones van debajo del título con separación -->
        <div class="mb-8 flex flex-col">
            <div class="mb-4">
                <h1 class="text-[3.5rem] font-extrabold tracking-tight text-on-surface leading-tight mb-4">
                    <span class="text-primary">Notificaciones</span>
                </h1>
                <p class="text-on-surface-variant text-base mt-1">Mantente al día con lo que sucede en tu red.</p>
            </div>
            <div class="flex flex-wrap gap-3 mt-2">
                <a href="<?= BASE_URL ?>?c=notificacion&a=marcarTodasLeidas" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-low text-on-surface-variant rounded-full text-sm font-medium hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-base">done_all</span>
                    Marcar todas como leídas
                </a>
                <a href="<?= BASE_URL ?>?c=dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    Volver al dashboard
                </a>
            </div>
        </div>

        <?php if (empty($notificaciones)): ?>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <span class="material-symbols-outlined text-6xl text-outline-variant mb-3">notifications_off</span>
                <h3 class="text-xl font-semibold text-on-surface">No hay notificaciones</h3>
                <p class="text-on-surface-variant mt-1">Cuando recibas novedades, aparecerán aquí.</p>
            </div>
        <?php else: ?>
            <?php
            // Ordenar todas las notificaciones por fecha descendente (más recientes primero)
            usort($notificaciones, function($a, $b) {
                return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
            });

            // Agrupar por fecha (Hoy, Ayer, Esta semana, Anteriores)
            $hoy = date('Y-m-d');
            $ayer = date('Y-m-d', strtotime('-1 day'));
            $semana = date('Y-m-d', strtotime('-7 days'));

            $grupos = [
                'Hoy' => [],
                'Ayer' => [],
                'Esta semana' => [],
                'Anteriores' => []
            ];

            foreach ($notificaciones as $n) {
                $fecha = date('Y-m-d', strtotime($n['fecha_creacion']));
                if ($fecha == $hoy) $grupos['Hoy'][] = $n;
                elseif ($fecha == $ayer) $grupos['Ayer'][] = $n;
                elseif ($fecha >= $semana) $grupos['Esta semana'][] = $n;
                else $grupos['Anteriores'][] = $n;
            }

            // Función para determinar icono según título/contenido
            function getNotifIcon($titulo, $contenido) {
                $texto = strtolower($titulo . ' ' . $contenido);
                if (strpos($texto, 'evento') !== false) return 'event';
                if (strpos($texto, 'mensaje') !== false || strpos($texto, 'chat') !== false) return 'chat';
                if (strpos($texto, 'seguridad') !== false || strpos($texto, 'verificado') !== false) return 'verified_user';
                if (strpos($texto, 'conexión') !== false || strpos($texto, 'conectar') !== false) return 'person_add';
                return 'notifications';
            }

            foreach ($grupos as $nombre => $notis):
                if (empty($notis)) continue;

                // Separar leídas y no leídas dentro del grupo
                $noLeidas = array_filter($notis, function($n) { return !$n['leida']; });
                $leidas = array_filter($notis, function($n) { return $n['leida']; });

                // Ordenar cada subgrupo por fecha descendente (ya lo están por el usort global, pero aseguramos)
                usort($noLeidas, function($a, $b) {
                    return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
                });
                usort($leidas, function($a, $b) {
                    return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
                });

                // Limitar leídas a las 10 más recientes
                $leidasLimitadas = array_slice($leidas, 0, 5);

                // Combinar: primero no leídas, luego leídas limitadas
                $notisFiltradas = array_merge($noLeidas, $leidasLimitadas);

                // Si después del filtro no quedan elementos, saltamos el grupo
                if (empty($notisFiltradas)) continue;
            ?>
                <div class="mt-6 first:mt-0">
                    <h2 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-primary rounded-full"></span>
                        <?= $nombre ?>
                    </h2>
                    <div class="space-y-3">
                        <?php foreach ($notisFiltradas as $n): ?>
                            <div class="notification-card group relative bg-surface-container-lowest p-4 md:p-5 rounded-xl shadow-[0_4px_16px_rgba(45,47,47,0.04)] hover:shadow-[0_12px_32px_rgba(45,47,47,0.08)] transition-all duration-300 flex items-start gap-4 border border-transparent hover:border-outline-variant/10 <?= $n['leida'] ? 'opacity-80' : '' ?>">
                                <!-- Icono / Avatar -->
                                <div class="w-12 h-12 rounded-xl shrink-0 bg-primary-container/20 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-2xl"><?= getNotifIcon($n['titulo'], $n['contenido']) ?></span>
                                </div>
                                
                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <h3 class="text-on-surface font-bold text-base"><?= htmlspecialchars($n['titulo']) ?></h3>
                                        <span class="text-[10px] text-outline font-medium whitespace-nowrap"><?= date('d M, H:i', strtotime($n['fecha_creacion'])) ?></span>
                                    </div>
                                    <p class="text-on-surface-variant text-sm mt-1"><?= nl2br(htmlspecialchars($n['contenido'])) ?></p>
                                    <?php if (!empty($n['enlace'])): ?>
                                        <a href="<?= BASE_URL . ltrim($n['enlace'], '/') ?>" class="inline-flex items-center gap-1 text-primary text-xs font-semibold mt-2 hover:underline">
                                            Ver más
                                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Botón de menú (tres puntos) -->
                                <div class="relative shrink-0">
                                    <button class="p-2 rounded-full hover:bg-surface-container transition-colors text-on-surface-variant" data-dropdown-trigger="true">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                    <div class="hidden absolute right-0 top-10 w-64 bg-white rounded-xl shadow-xl border border-surface-container-low z-[60] overflow-hidden py-1" data-dropdown-menu>
                                        <?php if (!$n['leida']): ?>
                                            <a href="<?= BASE_URL ?>?c=notificacion&a=marcarLeida&id=<?= $n['id_notificacion'] ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-primary/5 transition-colors w-full text-left">
                                                <span class="material-symbols-outlined text-lg">mark_email_read</span>
                                                Marcar como leída
                                            </a>
                                        <?php endif; ?>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-primary/5 transition-colors w-full text-left">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                            Eliminar esta notificación
                                        </a>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-primary/5 transition-colors w-full text-left">
                                            <span class="material-symbols-outlined text-lg">notifications_off</span>
                                            Dejar de recibir este tipo
                                        </a>
                                        <hr class="my-1 border-surface-container-low">
                                        <a href="<?= BASE_URL ?>?c=ajustes" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-primary/5 transition-colors w-full text-left">
                                            <span class="material-symbols-outlined text-lg">settings</span>
                                            Ajustes de notificaciones
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <!-- Bottom Navigation para móviles -->
    <?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>

    <!-- Estilos para el scroll personalizado (morado y más ancho) -->
    <style>
        /* Scrollbar siempre visible y anclado a la derecha */
        main {
            overflow-y: scroll; /* Fuerza la barra a estar siempre presente */
            scrollbar-width: thin;
            scrollbar-color: #5a2af7 #e7e8e8;
        }
        main::-webkit-scrollbar {
            width: 6px;
        }
        main::-webkit-scrollbar-track {
            background: #e7e8e8;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb {
            background: #5a2af7;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: #4e0bec;
        }
    </style>

    <!-- Script para cerrar dropdowns al hacer scroll -->
    <script>
        const mainScroll = document.querySelector('main');
        if (mainScroll) {
            mainScroll.addEventListener('scroll', () => {
                document.querySelectorAll('[data-dropdown-menu]').forEach(menu => menu.classList.add('hidden'));
            });
        }
    </script>
</body>
</html>
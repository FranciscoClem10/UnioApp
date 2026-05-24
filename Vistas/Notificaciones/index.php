<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased h-screen overflow-hidden flex flex-col">
    <?php require_once __DIR__ . '/../../includes/top-nav.php'; ?>

    <main class="flex-1 overflow-y-scroll pt-20 pb-20 md:pb-12 px-4 md:px-6 max-w-4xl mx-auto w-full" id="scroll">
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
            usort($notificaciones, function ($a, $b) {
                return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
            });

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

                $noLeidas = array_filter($notis, function ($n) {
                    return !$n['leida'];
                });
                $leidas = array_filter($notis, function ($n) {
                    return $n['leida'];
                });

                usort($noLeidas, function ($a, $b) {
                    return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
                });
                usort($leidas, function ($a, $b) {
                    return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
                });

                $leidasLimitadas = array_slice($leidas, 0, 5);
                $notisFiltradas = array_merge($noLeidas, $leidasLimitadas);
                if (empty($notisFiltradas)) continue;
            ?>
                <div class="mt-6 first:mt-0">
                    <h2 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-primary rounded-full"></span>
                        <?= $nombre ?>
                    </h2>
                    <div class="space-y-3">
                        <?php foreach ($notisFiltradas as $n): ?>
                            <?php 
                                $esInvitacionActividad = ($n['tipo'] == 'actividad' && $n['titulo'] == 'Invitación a actividad');
                                $esInvitacionOrganizador = ($n['tipo'] == 'actividad' && $n['titulo'] == 'Invitación a organizador');
                                $esClickeable = ($n['tipo'] != 'solicitud_amistad' && !empty($n['enlace']) && !$esInvitacionActividad && !$esInvitacionOrganizador);
                            ?>
                            <!-- Contenedor principal con GRID para evitar salto de línea -->
                            <div class="notification-card group relative bg-surface-container-lowest p-4 md:p-5 rounded-xl shadow-[0_4px_16px_rgba(45,47,47,0.04)] hover:shadow-[0_12px_32px_rgba(45,47,47,0.08)] transition-all duration-300 grid grid-cols-[auto,1fr,auto] gap-3 border border-transparent hover:border-outline-variant/10 <?= $n['leida'] ? 'opacity-80' : '' ?>">
                                
                                <!-- Icono (columna 1) -->
                                <div class="w-12 h-12 rounded-xl shrink-0 bg-primary-container/20 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-2xl"><?= getNotifIcon($n['titulo'], $n['contenido']) ?></span>
                                </div>

                                <!-- Contenido principal (columna 2) -->
                                <?php if ($esClickeable): ?>
                                    <a href="<?= BASE_URL ?>?c=notificacion&a=click&id=<?= $n['id_notificacion'] ?>" class="min-w-0">
                                <?php else: ?>
                                    <div class="min-w-0">
                                <?php endif; ?>
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <h3 class="text-on-surface font-bold text-base break-words"><?= htmlspecialchars($n['titulo']) ?></h3>
                                        <span class="text-[10px] text-outline font-medium whitespace-nowrap"><?= date('d M, H:i', strtotime($n['fecha_creacion'])) ?></span>
                                    </div>
                                    <p class="text-on-surface-variant text-sm mt-1 break-words"><?= nl2br(htmlspecialchars($n['contenido'])) ?></p>
                                <?php if ($esClickeable): ?>
                                    </a>
                                <?php else: ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Menú de tres puntos (columna 3) -->
                                <div class="relative justify-self-end">
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

                                <!-- Botones para solicitudes de amistad (ocupan toda la fila inferior) -->
                                <?php if ($n['tipo'] == 'solicitud_amistad' && !$n['leida']): ?>
                                    <div class="col-span-3 flex flex-col sm:flex-row gap-2 mt-2 justify-end">
                                        <a href="<?= BASE_URL ?>?c=notificacion&a=responderSolicitud&id_notif=<?= $n['id_notificacion'] ?>&respuesta=aceptar"
                                           class="px-4 py-1.5 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary-dark transition text-center">
                                            Aceptar
                                        </a>
                                        <a href="<?= BASE_URL ?>?c=notificacion&a=responderSolicitud&id_notif=<?= $n['id_notificacion'] ?>&respuesta=rechazar"
                                           class="px-4 py-1.5 bg-outline-variant text-on-surface rounded-full text-sm font-medium hover:bg-surface-container transition text-center">
                                            Rechazar
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Botones para invitaciones a actividad -->
                                <?php if ($n['tipo'] == 'actividad' && !$n['leida'] && $n['titulo'] == 'Invitación a actividad'): ?>
                                    <div class="col-span-3 flex flex-col sm:flex-row gap-2 mt-2 justify-end">
                                        <button onclick="responderActividad(<?= $n['id_notificacion'] ?>, 'aceptar')"
                                                class="px-4 py-1.5 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary-dark transition text-center">
                                            Aceptar
                                        </button>
                                        <button onclick="responderActividad(<?= $n['id_notificacion'] ?>, 'rechazar')"
                                                class="px-4 py-1.5 bg-outline-variant text-on-surface rounded-full text-sm font-medium hover:bg-surface-container transition text-center">
                                            Rechazar
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <!-- Botones para invitación a organizador -->
                                <?php if ($n['tipo'] == 'actividad' && !$n['leida'] && $n['titulo'] == 'Invitación a organizador'): ?>
                                    <div class="col-span-3 flex flex-col sm:flex-row gap-2 mt-2 justify-end">
                                        <button onclick="responderOrganizador(<?= $n['id_notificacion'] ?>, 'aceptar')"
                                                class="px-4 py-1.5 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary-dark transition text-center">
                                            Aceptar
                                        </button>
                                        <button onclick="responderOrganizador(<?= $n['id_notificacion'] ?>, 'rechazar')"
                                                class="px-4 py-1.5 bg-outline-variant text-on-surface rounded-full text-sm font-medium hover:bg-surface-container transition text-center">
                                            Rechazar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>

    <!-- Toast flotante para mensajes -->
    <div id="toast" class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-black/80 text-white px-4 py-2 rounded-full text-sm z-50 hidden transition-all"></div>

    <style>
        main {
            overflow-y: scroll;
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

    <script>
        // Definir BASE_URL (debe venir desde PHP)
        const BASE_URL = '<?= BASE_URL ?>';

        // Función para mostrar notificaciones toast
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.remove('hidden');
            toast.style.backgroundColor = isError ? '#b41340' : '#2d2f2f';
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        // Cerrar menús desplegables al hacer scroll
        const mainScroll = document.querySelector('main');
        if (mainScroll) {
            mainScroll.addEventListener('scroll', () => {
                document.querySelectorAll('[data-dropdown-menu]').forEach(menu => menu.classList.add('hidden'));
            });
        }

        // Respuesta a invitación de actividad
        async function responderActividad(idNotif, respuesta) {
            const formData = new FormData();
            formData.append('id_notif', idNotif);
            formData.append('respuesta', respuesta);
            try {
                const resp = await fetch(`${BASE_URL}?c=notificacion&a=responderInvitacionActividad`, {
                    method: 'POST',
                    body: formData
                });
                const json = await resp.json();
                if (json.success) {
                    showToast(json.message);
                    location.reload();
                } else {
                    showToast(json.message, true);
                }
            } catch (e) {
                showToast('Error al procesar la solicitud', true);
            }
        }

        // Respuesta a invitación de organizador
        async function responderOrganizador(idNotif, respuesta) {
            const formData = new FormData();
            formData.append('id_notif', idNotif);
            formData.append('respuesta', respuesta);
            try {
                const resp = await fetch(`${BASE_URL}?c=notificacion&a=responderInvitacionOrganizador`, {
                    method: 'POST',
                    body: formData
                });
                const json = await resp.json();
                if (json.success) {
                    showToast(json.message);
                    location.reload();
                } else {
                    showToast(json.message, true);
                }
            } catch (e) {
                showToast('Error al procesar la solicitud', true);
            }
        }
    </script>
</body>
</html>
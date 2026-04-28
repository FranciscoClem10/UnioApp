<?php
// Asegurar que las variables existan (vienen del controlador)
$amigos = $amigos ?? [];
$solicitudes = $solicitudes ?? [];
$sugerencias = $sugerencias ?? [];
$rechazados = $rechazados ?? [];
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<?php require_once __DIR__ . '/../../includes/top-nav.php'; ?>

<!-- Contenido principal (dentro del <main> ya abierto por header.php) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
    <!-- Mensajes flash -->
    <?php if (isset($_SESSION['mensaje_amigos'])): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-xl"><?= htmlspecialchars($_SESSION['mensaje_amigos']) ?></div>
        <?php unset($_SESSION['mensaje_amigos']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_amigos'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-xl"><?= htmlspecialchars($_SESSION['error_amigos']) ?></div>
        <?php unset($_SESSION['error_amigos']); ?>
    <?php endif; ?>

    <!-- Cabecera de la página -->
    <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Mis Conexiones</h1>
        <p class="text-on-surface-variant text-base mt-1">Gestiona las personas que has conocido en la red UNIO</p>
    </div>

    <!-- TABS -->
    <div class="flex border-b border-surface-container mb-6 gap-4 sm:gap-6 overflow-x-auto pb-1">
        <button id="tabFriendsBtn" class="tab-active tab-transition py-3 px-1 text-base font-semibold flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-xl">group</span> Amigos
            <span id="friendsTabCount" class="ml-1 bg-surface-variant text-on-surface-variant text-xs font-bold px-2 py-0.5 rounded-full"><?= count($amigos) ?></span>
        </button>
        <button id="tabRequestsBtn" class="tab-inactive tab-transition py-3 px-1 text-base font-semibold flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-xl">person_add</span> Solicitudes
            <span id="requestsTabCount" class="ml-1 bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full"><?= count($solicitudes) ?></span>
        </button>
        <button id="tabRejectedBtn" class="tab-inactive ...">
            <span class="material-symbols-outlined text-xl">block</span> Rechazados
            <span class="ml-1 bg-error/10 text-error text-xs font-bold px-2 py-0.5 rounded-full"><?= count($rechazados) + count($bloqueados ?? []) ?></span>
        </button>
    </div>

    <!-- Panel Amigos -->
    <div id="friendsPanel" class="panel-content transition-all duration-300 w-full">
        <!-- Barra de búsqueda mejorada -->
        <div class="w-full mb-8">
            <div class="relative max-w-md mx-auto md:mx-0">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-xl">search</span>
                <input type="text" id="searchFriendsInput" placeholder="Buscar amigos por nombre..." 
                    class="w-full pl-11 pr-10 py-3 bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm focus:ring-2 focus:ring-primary/30 outline-none transition">
                <!-- Botón para limpiar búsqueda (se muestra solo cuando hay texto) -->
                <button id="clearSearchFriends" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-error transition-colors hidden">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        </div>

        <!-- Grid de amigos -->
        <div id="friendsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($amigos)): ?>
                <!-- Estado vacío mejorado con llamado a la acción -->
                <div class="col-span-full text-center py-12 bg-surface-container-lowest rounded-2xl border border-dashed border-outline-variant">
                    <span class="material-symbols-outlined text-6xl text-outline mb-3">people_outline</span>
                    <p class="text-on-surface-variant mb-4">No tienes amigos agregados.</p>
                    <a href="<?= BASE_URL ?>?c=amigos&a=agregar" 
                    class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-full hover:bg-primary-dark transition shadow-sm">
                        <span class="material-symbols-outlined text-sm">person_add</span>
                        <span>Buscar amigos</span>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($amigos as $amigo): ?>
                    <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-md border border-surface-container/60 transition-all duration-200 hover:shadow-xl hover:-translate-y-1 flex flex-col" 
                        data-friend-id="<?= $amigo['id_usuario'] ?>">
                        
                        <!-- Cabecera: avatar + nombre + acciones -->
                        <div class="flex gap-4 items-start">
                            <!-- Avatar circular -->
                            <div class="w-14 h-14 rounded-full overflow-hidden shadow-sm flex-shrink-0 bg-surface-container">
                                <?php if (!empty($amigo['foto_base64'])): ?>
                                    <img class="w-full h-full object-cover" src="<?= $amigo['foto_base64'] ?>" alt="foto de <?= htmlspecialchars($amigo['nombre_completo']) ?>">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined text-2xl">person</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Información principal -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg text-on-surface truncate"><?= htmlspecialchars($amigo['nombre_completo']) ?></h3>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="material-symbols-outlined text-primary text-sm">group</span>
                                    <span class="text-xs font-medium text-on-primary-container bg-primary/10 px-2 py-0.5 rounded-full">Amigo</span>
                                </div>
                            </div>
                            
                            <!-- Grupo de acciones: Eliminar y Bloquear -->
                            <div class="flex gap-1 flex-shrink-0">
                                <!-- Eliminar amigo -->
                                <form action="<?= BASE_URL ?>?c=amigos&a=eliminarAmigo" method="POST" onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars($amigo['nombre_completo']) ?> de tus amigos?')" class="inline">
                                    <input type="hidden" name="id" value="<?= $amigo['id_usuario'] ?>">
                                    <button type="submit" class="p-1.5 rounded-full text-outline hover:text-error hover:bg-error/10 transition-colors" title="Eliminar amigo">
                                        <span class="material-symbols-outlined text-2xl">person_remove</span>
                                    </button>
                                </form>
                                <!-- Bloquear usuario -->
                                <form action="<?= BASE_URL ?>?c=amigos&a=bloquear" method="POST" onsubmit="return confirm('¿Bloquear a <?= htmlspecialchars($amigo['nombre_completo']) ?>? Perderás la amistad y no podrá contactarte.')" class="inline">
                                    <input type="hidden" name="id" value="<?= $amigo['id_usuario'] ?>">
                                    <button type="submit" class="p-1.5 rounded-full text-outline hover:text-error/80 hover:bg-error/10 transition-colors" title="Bloquear usuario">
                                        <span class="material-symbols-outlined text-2xl">block</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Pie de tarjeta: Ver perfil -->
                        <div class="mt-5 pt-3 border-t border-surface-container/50 flex justify-end">
                            <a href="<?= BASE_URL ?>?c=amigos&a=verPerfil&id=<?= $amigo['id_usuario'] ?>" 
                            class="inline-flex items-center gap-1.5 text-sm text-on-surface-variant hover:text-primary transition px-3 py-1.5 rounded-full hover:bg-primary/5">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                <span>Ver perfil</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pequeño script para mostrar/ocultar el botón de limpiar búsqueda -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchFriendsInput');
        const clearBtn = document.getElementById('clearSearchFriends');
        if (searchInput && clearBtn) {
            const toggleClearBtn = () => {
                clearBtn.classList.toggle('hidden', searchInput.value === '');
            };
            searchInput.addEventListener('input', toggleClearBtn);
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
                // Disparar evento personalizado si tu lógica de búsqueda lo requiere
                searchInput.dispatchEvent(new Event('keyup'));
            });
            toggleClearBtn();
        }
    });
    </script>

    <!-- Panel Solicitudes -->
    <div id="requestsPanel" class="panel-content hidden transition-all duration-300 w-full">
        <p class="text-sm text-on-surface-variant mb-4">Estas son las solicitudes de amistad pendientes</p>
        <div id="requestsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($solicitudes)): ?>
                <div class="col-span-full text-center py-12 bg-surface-container-lowest rounded-2xl text-outline-variant">
                    <span class="material-symbols-outlined text-5xl">inbox</span>
                    <p class="mt-2">No hay solicitudes pendientes</p>
                    <p class="text-sm mt-1">Cuando alguien te envíe una solicitud, aparecerá aquí.</p>
                </div>
            <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <div class="bg-surface-container-lowest rounded-xl p-5 shadow-md border border-surface-container/80 card-hover flex flex-col">
                        <div class="flex gap-4 items-start">
                            <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm flex-shrink-0">
                                <?php if (!empty($solicitud['foto_base64'])): ?>
                                    <img class="w-full h-full object-cover" src="<?= $solicitud['foto_base64'] ?>" alt="foto">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">Sin foto</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg text-on-surface truncate"><?= htmlspecialchars($solicitud['nombre_completo']) ?></h3>
                                <div class="flex items-center gap-1 mt-1.5 text-xs bg-primary/5 px-2 py-0.5 rounded-full w-fit">
                                    <span class="material-symbols-outlined text-primary text-[13px]">pending</span>
                                    <span class="text-on-primary-container text-[11px] font-medium">Solicitud pendiente</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <form action="<?= BASE_URL ?>?c=amigos&a=responder" method="POST" class="flex-1">
                                <input type="hidden" name="id" value="<?= $solicitud['id_solicitante'] ?>">
                                <input type="hidden" name="accion" value="aceptar">
                                <button type="submit" class="w-full bg-primary/10 text-primary font-bold py-2.5 rounded-xl hover:bg-primary/20 transition flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-lg">check_circle</span> Aceptar
                                </button>
                            </form>
                            <form action="<?= BASE_URL ?>?c=amigos&a=responder" method="POST" class="flex-1">
                                <input type="hidden" name="id" value="<?= $solicitud['id_solicitante'] ?>">
                                <input type="hidden" name="accion" value="rechazar">
                                <button type="submit" class="w-full bg-error/5 text-error font-medium py-2.5 rounded-xl hover:bg-error/10 transition flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-lg">cancel</span> Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel Rechazados y Bloqueados -->
    <div id="rejectedPanel" class="panel-content hidden transition-all duration-300 w-full">
        <!-- Sección Rechazados (solicitudes manualmente rechazadas) -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                 Rechazados manualmente
            </h3>
            <div id="rejectedGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($rechazados)): ?>
                    <div class="col-span-full text-center py-8 bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                        <span class="material-symbols-outlined text-4xl text-outline">block</span>
                        <p class="mt-1 text-on-surface-variant">No hay usuarios rechazados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($rechazados as $rech): ?>
                        <div class="bg-surface-container-lowest rounded-xl p-4 shadow-md border border-surface-container/60">
                            <div class="flex gap-3 items-start">
                                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                                    <?= $rech['foto_base64'] ? "<img src='{$rech['foto_base64']}' class='w-full h-full object-cover'>" : '<div class="w-full h-full bg-gray-200 flex items-center justify-center text-xs">Sin foto</div>' ?>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold truncate"><?= htmlspecialchars($rech['nombre_completo']) ?></h4>
                                    <p class="text-xs text-outline truncate"><?= htmlspecialchars($rech['email']) ?></p>
                                    <p class="text-[10px] text-error mt-1">Rechazado el <?= date('d/m/Y', strtotime($rech['fecha_respuesta'])) ?></p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <form action="<?= BASE_URL ?>?c=amigos&a=desrechazar" method="POST" class="flex-1">
                                    <input type="hidden" name="id" value="<?= $rech['id_usuario'] ?>">
                                    <button type="submit" class="w-full bg-primary/10 text-primary font-medium py-1.5 rounded-lg hover:bg-primary/20 transition text-sm">Desrechazar</button>
                                </form>
                                <form action="<?= BASE_URL ?>?c=amigos&a=bloquear" method="POST" class="flex-1">
                                    <input type="hidden" name="id" value="<?= $rech['id_usuario'] ?>">
                                    <button type="submit" class="w-full bg-error/10 text-error font-medium py-1.5 rounded-lg hover:bg-error/20 transition text-sm" onclick="return confirm('¿Bloquear a este usuario? No podrá contactarte ni ver tu perfil.')">Bloquear</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección Bloqueados -->
        <div>
            <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                 Usuarios bloqueados
            </h3>
            <div id="blockedGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($bloqueados)): ?>
                    <div class="col-span-full text-center py-8 bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                        <span class="material-symbols-outlined text-4xl text-outline">do_not_disturb</span>
                        <p class="mt-1 text-on-surface-variant">No hay usuarios bloqueados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($bloqueados as $bloq): ?>
                        <div class="bg-surface-container-lowest rounded-xl p-4 shadow-md border border-error/30">
                            <div class="flex gap-3 items-start">
                                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                                    <?= $bloq['foto_base64'] ? "<img src='{$bloq['foto_base64']}' class='w-full h-full object-cover'>" : '<div class="w-full h-full bg-gray-200 flex items-center justify-center text-xs">Sin foto</div>' ?>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold truncate"><?= htmlspecialchars($bloq['nombre_completo']) ?></h4>
                                    <p class="text-xs text-outline truncate"><?= htmlspecialchars($bloq['email']) ?></p>
                                    <p class="text-[10px] text-error mt-1">Bloqueado el <?= date('d/m/Y', strtotime($bloq['fecha_respuesta'])) ?></p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <form action="<?= BASE_URL ?>?c=amigos&a=desbloquear" method="POST">
                                    <input type="hidden" name="id" value="<?= $bloq['id_usuario'] ?>">
                                    <button type="submit" class="w-full bg-warning/10 text-warning font-medium py-1.5 rounded-lg hover:bg-warning/20 transition text-sm" onclick="return confirm('¿Desbloquear a este usuario? Podrá volver a contactarte.')">
                                        Desbloquear
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Botón flotante agregar amigo -->
    <div class="fixed bottom-28 right-6 z-30">
        <button id="fabAddFriendGlobal" class="bg-primary text-white rounded-full p-4 shadow-xl shadow-primary/40 hover:bg-primary/90 transition-all active:scale-95 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">person_add</span>
        </button>
    </div>
</div>

<!-- Modal Enviar Solicitud (mejorado con buscador) -->
<div id="addFriendModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 opacity-0 invisible transition-all duration-200 px-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-all duration-200 overflow-hidden">
        <div class="p-5 border-b border-surface-container flex justify-between items-center">
            <h3 class="text-xl font-bold">Conectar con nueva persona</h3>
            <button id="closeModalBtn" class="text-outline hover:text-on-surface p-1 rounded-full"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-5 max-h-[65vh] overflow-y-auto">
            <!-- BUSCADOR -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-on-surface mb-2">Buscar por nombre, apellido o correo</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-xl">search</span>
                    <input type="text" id="searchUserInput" placeholder="Ej: María o maria@example.com" 
                           class="w-full pl-10 pr-4 py-2 bg-surface-container-low rounded-xl border border-surface-container focus:ring-2 focus:ring-primary/30 outline-none transition">
                </div>
                <div id="searchResults" class="mt-3 space-y-2 hidden"></div>
                <div id="searchEmpty" class="mt-3 text-center text-outline text-sm hidden">No se encontraron usuarios</div>
            </div>
            <!-- SEPARADOR -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-surface-container"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-3 bg-surface-container-lowest text-outline">O recomendados para ti</span></div>
            </div>
            <!-- RECOMENDADOS -->
            <div>
                <div id="suggestedUsersList" class="space-y-3">
                    <?php if (empty($sugerencias)): ?>
                        <div class="text-center py-6 text-outline">✨ No hay más sugerencias</div>
                    <?php else: ?>
                        <?php foreach ($sugerencias as $sug): ?>
                            <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-transparent hover:border-primary/30 transition">
                                <div class="flex gap-3 items-center min-w-0">
                                    <?php if (!empty($sug['foto_base64'])): ?>
                                        <img src="<?= $sug['foto_base64'] ?>" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-xs flex-shrink-0">Sin foto</div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm truncate"><?= htmlspecialchars($sug['nombre_completo']) ?></p>
                                        <p class="text-xs text-outline truncate"><?= htmlspecialchars($sug['email']) ?></p>
                                    </div>
                                </div>
                                <form action="<?= BASE_URL ?>?c=amigos&a=enviarSolicitud" method="POST" class="send-request-form flex-shrink-0 ml-2">
                                    <input type="hidden" name="id" value="<?= $sug['id_usuario'] ?>">
                                    <button type="submit" class="bg-primary/10 text-primary hover:bg-primary/25 text-sm font-bold py-1.5 px-3 rounded-xl transition">
                                        Enviar solicitud
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="p-4 bg-surface-container-low border-t border-surface-container text-right">
            <button id="modalCancelBtn" class="px-4 py-2 text-outline font-medium rounded-xl hover:bg-surface-container transition">Cerrar</button>
        </div>
    </div>
</div>

<style>
    .tab-active { border-bottom: 3px solid #5a2af7; color: #2d2f2f; font-weight: 700; }
    .tab-inactive { border-bottom: 3px solid transparent; color: #767777; font-weight: 500; }
    .tab-transition { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .card-hover { transition: all 0.25s ease; }
    .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 30px -12px rgba(90,42,247,0.12); }
    .panel-content {
        width: 100%;
        text-align: left;
    }
    /* Asegurar que las cuadrículas no centren su contenido */
    .panel-content .grid {
        justify-items: stretch;
    }
    /* Para móviles, evitar overflow horizontal */
    body {
        overflow-x: hidden;
    }
    .max-w-7xl {
        overflow-x: hidden;
    }
</style>

<?php require_once __DIR__ . '/../../Scripts/amigosS.php'; ?>
<?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>
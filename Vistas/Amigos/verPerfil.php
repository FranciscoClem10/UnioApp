<?php
// Variables que llegan del controlador:
// $esPropietario, $relacion, $id_perfil, $id_visitante, $base_url, $nombreCompleto,
// $mostrarFoto, $fotoPerfilBase64, $mostrarUbicacion, $usuario, $mostrarEdad, $edad,
// $totalAmigos, $totalActividades, $mostrarActividades, $actividadesProximas, $actividadesPasadas,
// $intereses, $amigos, $mostrarAmigos, $puedeEnviarMensaje, etc.

$botonAmistad = '';
if ($esPropietario) {
    $botonAmistad = '';
} else {
    switch ($relacion) {
        case 'aceptado':
            $botonAmistad = '<div class="relative group">
                <button class="px-6 py-2.5 bg-green-50 text-green-700 font-bold rounded-xl border border-green-200 flex items-center gap-2 hover:bg-green-100 transition-colors active:scale-95">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    Amigos
                </button>
                <div class="absolute right-0 top-full mt-2 w-48 bg-white/90 backdrop-blur-xl border border-outline-variant/10 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60] py-2">
                    <a href="'.BASE_URL.'?c=amigos&a=eliminarAmigo&id='.$id_perfil.'" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">person_remove</span>
                        <span>Eliminar amigo</span>
                    </a>
                    <a href="'.BASE_URL.'?c=amigos&a=bloquear&id='.$id_perfil.'" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">block</span>
                        <span>Bloquear</span>
                    </a>
                </div>
            </div>';
            break;
        case 'pendiente':
            $db = Database::getConexion();
            $stmt = $db->prepare("SELECT id_solicitante FROM amistades WHERE (id_solicitante = ? AND id_receptor = ?) OR (id_solicitante = ? AND id_receptor = ?)");
            $stmt->execute([$id_visitante, $id_perfil, $id_perfil, $id_visitante]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $solicitante = $row['id_solicitante'] ?? null;
            if ($solicitante == $id_visitante) {
                $botonAmistad = '<button class="px-6 py-2.5 bg-gray-200 text-gray-600 font-bold rounded-xl flex items-center gap-2 cursor-default">
                                    <span class="material-symbols-outlined text-lg">hourglass_empty</span>
                                    Solicitud enviada
                                </button>';
            } else {
                $botonAmistad = '<div class="flex gap-2">
                                    <a href="'.BASE_URL.'?c=amigos&a=responder&id='.$id_visitante.'&accion=aceptar" class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-xl hover:bg-primary-dim">Aceptar</a>
                                    <a href="'.BASE_URL.'?c=amigos&a=responder&id='.$id_visitante.'&accion=rechazar" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-bold rounded-xl">Rechazar</a>
                                </div>';
            }
            break;
        default:
            $botonAmistad = '<a href="'.BASE_URL.'?c=amigos&a=enviarSolicitud&id='.$id_perfil.'" class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-xl flex items-center gap-2 hover:bg-primary-dim">
                                <span class="material-symbols-outlined text-lg">person_add</span>
                                Agregar amigo
                            </a>';
            break;
    }
}

$botonMensaje = '';
if (!$esPropietario && $puedeEnviarMensaje) {
    $botonMensaje = '<a href="'.BASE_URL.'?c=mensajes&a=verPrivado&id='.$id_perfil.'" class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-xl flex items-center gap-2 hover:bg-primary-dim">
                        <span class="material-symbols-outlined text-lg">chat_bubble</span>
                        Mensaje
                    </a>';
}

$tituloActividades = $esPropietario ? 'Mis Actividades' : 'Sus actividades';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<div class="flex-1 overflow-y-auto">
    <!-- Hero Header -->
    <div class="relative">
        <div class="h-64 md:h-80 w-full overflow-hidden relative">
            <div class="w-full h-full bg-[#5a2af7]"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 md:px-12 -mt-16 relative z-10">
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] flex flex-col md:flex-row items-center md:items-end gap-6">
                <!-- Foto de Perfil -->
                <div class="relative">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-3xl overflow-hidden border-4 border-white shadow-xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600">
                        <?php if ($mostrarFoto && $fotoPerfilBase64): ?>
                            <img src="<?= $fotoPerfilBase64 ?>" alt="Foto de perfil" class="w-full h-full object-cover"/>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-white">person</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-black font-headline tracking-tight text-on-surface">
                                <?= htmlspecialchars($nombreCompleto) ?>
                            </h1>
                            <?php if ($mostrarUbicacion && !empty($usuario['latitud']) && !empty($usuario['longitud'])): ?>
                            <div class="text-on-surface-variant font-medium text-sm mt-1">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                <span id="direccion-usuario">Obteniendo dirección...</span>
                            </div>
                            <?php endif; ?>
                            <?php if ($mostrarEdad && $edad !== null): ?>
                            <p class="text-on-surface-variant font-medium"><?= $edad ?> años</p>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-center md:justify-start gap-3 w-full md:w-auto">
                            <?php if ($esPropietario): ?>
                                <a href="<?= BASE_URL ?>?c=perfil&a=editar" class="px-6 py-2.5 bg-surface-container-low text-primary font-bold rounded-xl border border-primary/10 flex items-center gap-2 hover:bg-surface-container-high transition-colors active:scale-95">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                    Editar perfil
                                </a>
                            <?php else: ?>
                                <?= $botonAmistad ?>
                                <?= $botonMensaje ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-center md:justify-start gap-12 mt-6 pt-6 border-t border-outline-variant/10">
                        <!-- Contador de amigos: visible solo si la configuración lo permite -->
                        <?php if ($mostrarAmigos): ?>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="text-2xl font-black text-on-surface"><?= $totalAmigos ?></span>
                            <span class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Conexiones</span>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center md:items-start opacity-50">
                            <span class="material-symbols-outlined text-2xl">lock</span>
                            <span class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Conexiones privadas</span>
                        </div>
                        <?php endif; ?>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="text-2xl font-black text-on-surface"><?= $totalActividades ?></span>
                            <span class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Actividades</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-6xl mx-auto px-6 md:px-12 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8 pb-24">
        <!-- Left Column -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Sobre mí -->
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <h2 class="text-xl font-bold font-headline mb-4 text-on-surface">Sobre mí</h2>
                <p class="text-on-surface-variant leading-relaxed text-sm">
                    <?= !empty($usuario['biografia']) ? nl2br(htmlspecialchars($usuario['biografia'])) : "Este usuario aún no ha escrito una biografía." ?>
                </p>
            </section>

            <!-- Sección de Amigos (solo si la visibilidad lo permite y hay amigos) -->
            <?php if ($mostrarAmigos && !empty($amigos)): ?>
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <h2 class="text-xl font-bold font-headline mb-6 text-on-surface">Amigos</h2>
                <div class="flex items-center gap-4 mb-4">
                    <?php foreach ($amigos as $amigo): ?>
                        <div class="relative" title="<?= htmlspecialchars($amigo['nombre_completo']) ?>">
                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow">
                                <?php if (!empty($amigo['foto_base64'])): ?>
                                    <img src="<?= $amigo['foto_base64'] ?>" 
                                         alt="<?= htmlspecialchars($amigo['nombre_completo']) ?>" 
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-violet-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-2xl">person</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= BASE_URL ?>?c=amigos&a=listaAmigos&id=<?= $id_perfil ?>"
                   class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-surface-container-low text-primary font-bold rounded-xl border border-primary/10 hover:bg-surface-container-high transition-colors text-sm gap-2">
                    <span class="material-symbols-outlined text-lg">groups</span>
                    Ver todas las conexiones
                </a>
            </section>
            <?php elseif ($mostrarAmigos && empty($amigos)): ?>
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <h2 class="text-xl font-bold font-headline mb-4 text-on-surface">Amigos</h2>
                <p class="text-on-surface-variant text-sm">Este usuario aún no tiene amigos.</p>
            </section>
            <?php endif; ?>

            <!-- Intereses -->
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold font-headline text-on-surface"><?= $esPropietario ? 'Mis Intereses' : 'Intereses' ?></h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if (empty($intereses)): ?>
                        <span class="text-on-surface-variant text-sm">No ha especificado intereses.</span>
                    <?php else: ?>
                        <?php foreach ($intereses as $interes): ?>
                            <span class="px-4 py-2 bg-primary/10 text-primary text-xs font-bold rounded-lg"><?= htmlspecialchars($interes) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Right Column: Actividades -->
        <div class="lg:col-span-8 space-y-8">
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black font-headline tracking-tight text-on-surface"><?= $tituloActividades ?></h2>
                    <?php if ($mostrarActividades): ?>
                    <div class="bg-surface-container-low p-1 rounded-xl flex gap-1">
                        <button id="btn-proximas" onclick="toggleActivities('proximas')" class="px-5 py-1.5 bg-white shadow-sm text-primary font-bold text-sm rounded-lg transition-all">Próximas</button>
                        <button id="btn-pasadas" onclick="toggleActivities('pasadas')" class="px-5 py-1.5 text-on-surface-variant hover:text-on-surface font-medium text-sm rounded-lg transition-all">Pasadas</button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!$mostrarActividades): ?>
                    <div class="bg-surface-container-lowest rounded-2xl p-8 text-center">
                        <p class="text-on-surface-variant">Este usuario ha decidido ocultar sus actividades.</p>
                    </div>
                <?php else: ?>
                <div id="container-proximas" class="grid grid-cols-1 gap-6">
                    <?php if (empty($actividadesProximas)): ?>
                        <p class="text-on-surface-variant text-sm">No hay actividades próximas.</p>
                    <?php else: ?>
                        <?php foreach ($actividadesProximas as $act): ?>
                        <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-outline-variant/5 group flex flex-col md:flex-row h-auto md:h-64">
                            <div class="md:w-2/5 relative h-48 md:h-full overflow-hidden">
                                <?php if (!empty($act['foto_base64'])): ?>
                                    <img alt="<?= htmlspecialchars($act['nombre']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= $act['foto_base64'] ?>" />
                                <?php else: ?>
                                    <img alt="Imagen por defecto" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1200&auto=format&fit=crop" />
                                <?php endif; ?>
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 <?= $act['estado'] == 'en_curso' ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface-variant' ?> text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg"><?= $act['estado_visual'] ?></span>
                                </div>
                            </div>
                            <div class="p-6 md:w-3/5 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-secondary"><?= htmlspecialchars($act['nombre_tipo'] ?? 'Actividad') ?></span>
                                            <span class="text-outline-variant text-[10px]">•</span>
                                            <span class="material-symbols-outlined text-sm text-outline-variant"><?= $act['privacidad'] == 'publica' ? 'public' : 'lock' ?></span>
                                        </div>
                                        <div class="flex items-center gap-1 text-on-surface-variant">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            <span class="text-[11px] font-bold">Min: <?= $act['limite_participantes_min'] ?> / Max: <?= $act['limite_participantes_max'] ?? '∞' ?></span>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-on-surface mb-2 leading-tight"><?= htmlspecialchars($act['nombre']) ?></h3>
                                    <p class="text-on-surface-variant text-sm line-clamp-2"><?= htmlspecialchars($act['descripcion']) ?></p>
                                    <p class="text-xs text-primary font-bold mt-2">Creado por: <?= htmlspecialchars($act['nombre_creador']) ?></p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-outline-variant/10 flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1 text-on-surface-variant text-[11px] font-medium">
                                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                            <?= date('d M, Y · H:i', strtotime($act['fecha_inicio'])) ?>
                                        </div>
                                        <div class="flex items-center gap-1 text-outline text-[11px]">
                                            <span class="material-symbols-outlined text-[14px]">event_repeat</span>
                                            <?= date('d M, Y · H:i', strtotime($act['fecha_fin'])) ?>
                                        </div>
                                    </div>
                                    <a href="<?= BASE_URL ?>?c=actividades&a=ver&id=<?= $act['id_actividad'] ?>" class="text-primary font-bold text-xs flex items-center gap-1 hover:gap-2 transition-all">
                                        Ver detalles
                                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div id="container-pasadas" class="grid grid-cols-1 gap-6 hidden">
                    <?php if (empty($actividadesPasadas)): ?>
                        <p class="text-on-surface-variant text-sm">No hay actividades pasadas.</p>
                    <?php else: ?>
                        <?php foreach ($actividadesPasadas as $act): ?>
                        <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-outline-variant/5 group flex flex-col md:flex-row h-auto md:h-64">
                            <div class="md:w-2/5 relative h-48 md:h-full overflow-hidden">
                                <img alt="<?= htmlspecialchars($act['nombre']) ?>" class="w-full h-full object-cover" src="<?= !empty($act['foto_base64']) ? $act['foto_base64'] : 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1200&auto=format&fit=crop' ?>" />
                                <div class="absolute top-4 left-4"><span class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-[10px] font-bold uppercase rounded-full">Finalizada</span></div>
                            </div>
                            <div class="p-6 md:w-3/5">
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($act['nombre']) ?></h3>
                                <p class="text-on-surface-variant text-sm"><?= htmlspecialchars($act['descripcion']) ?></p>
                                <div class="mt-2 text-xs text-outline"><?= date('d M, Y', strtotime($act['fecha_inicio'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<?php include 'includes/bottom-nav.php'; ?>

<script>
    function toggleActivities(type) {
        const containerProximas = document.getElementById('container-proximas');
        const containerPasadas = document.getElementById('container-pasadas');
        const btnProximas = document.getElementById('btn-proximas');
        const btnPasadas = document.getElementById('btn-pasadas');
        if (type === 'proximas') {
            containerProximas.classList.remove('hidden');
            containerPasadas.classList.add('hidden');
            btnProximas.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnProximas.classList.remove('text-on-surface-variant');
            btnPasadas.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnPasadas.classList.add('text-on-surface-variant');
        } else {
            containerProximas.classList.add('hidden');
            containerPasadas.classList.remove('hidden');
            btnPasadas.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnPasadas.classList.remove('text-on-surface-variant');
            btnProximas.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnProximas.classList.add('text-on-surface-variant');
        }
    }

    document.addEventListener('DOMContentLoaded', async function() {
        toggleActivities('proximas');

        <?php if ($mostrarUbicacion && !empty($usuario['latitud']) && !empty($usuario['longitud'])): ?>
        const lat = <?= json_encode($usuario['latitud']) ?>;
        const lng = <?= json_encode($usuario['longitud']) ?>;
        const direccionSpan = document.getElementById('direccion-usuario');

        if (!lat || !lng) {
            direccionSpan.textContent = 'Ubicación no disponible';
            return;
        }

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data.address) {
                const address = data.address;
                const direccion = [
                    address.road,
                    address.suburb,
                    address.city || address.town || address.village,
                    address.state,
                    address.country
                ].filter(Boolean).join(', ');
                direccionSpan.textContent = direccion || data.display_name;
            } else {
                direccionSpan.textContent = 'Dirección no encontrada';
            }
        } catch (error) {
            console.error('Error obteniendo dirección:', error);
            direccionSpan.textContent = 'Error al obtener ubicación';
        }
        <?php endif; ?>
    });
</script>
</body>
</html>
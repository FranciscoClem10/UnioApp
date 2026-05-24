<?php
// Asegurar que las variables estén definidas
if (!isset($actividad)) die('Error: actividad no cargada');
$usuarioActualId = $_SESSION['usuario_id'] ?? 0;
// Preparar dirección para JavaScript (si existe)
$direccionBD = !empty($actividad['direccion']) ? json_encode($actividad['direccion']) : 'null';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<main class="max-w-7xl mx-auto px-6 pt-24 pb-32">
    <?php if (isset($_SESSION['error_participacion'])): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6"><?= htmlspecialchars($_SESSION['error_participacion']) ?></div>
        <?php unset($_SESSION['error_participacion']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['exito_participacion'])): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6"><?= htmlspecialchars($_SESSION['exito_participacion']) ?></div>
        <?php unset($_SESSION['exito_participacion']); ?>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16">
        <div class="lg:col-span-8">
            <div class="relative rounded-2xl overflow-hidden aspect-[16/9] shadow-2xl group">
                <?php if ($actividad['foto_base64']): ?>
                    <img src="<?= $actividad['foto_base64'] ?>" alt="<?= htmlspecialchars($actividad['nombre']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"/>
                <?php else: ?>
                    <div class="w-full h-full bg-surface-container flex items-center justify-center text-outline">Sin imagen</div>
                <?php endif; ?>
                <div class="absolute top-6 left-6 flex gap-2">
                    <span class="bg-violet-600 text-white px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg">
                        <?php
                        switch ($actividad['estado']) {
                            case 'pendiente': echo 'Por iniciar'; break;
                            case 'en_curso': echo 'En curso'; break;
                            case 'finalizada': echo 'Finalizada'; break;
                            case 'cancelada': echo 'Cancelada'; break;
                            default: echo $actividad['estado'];
                        }
                        ?>
                    </span>
                    <span class="bg-white/90 backdrop-blur text-slate-900 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg">
                        <?= htmlspecialchars($actividad['categoria']) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="lg:col-span-4 flex flex-col justify-center space-y-6">
            <div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-on-surface leading-tight tracking-tight mb-4"><?= htmlspecialchars($actividad['nombre']) ?></h1>
                <div class="flex items-center gap-3 p-3 bg-surface-container-low rounded-2xl">
                    <img src="<?= $organizador['foto_base64'] ?? '../Recursos/user.png' ?>" alt="<?= htmlspecialchars($actividad['organizador_nombre']) ?>" class="w-12 h-12 rounded-full object-cover"/>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium">Organizado por</p>
                        <p class="text-base font-bold text-on-surface"><?= htmlspecialchars($actividad['organizador_nombre']) ?></p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm">
                    <p class="text-[10px] uppercase tracking-widest text-outline mb-1 font-bold">Publicado</p>
                    <p class="text-sm font-semibold text-on-surface"><?= date('d M Y', strtotime($actividad['fecha_publicacion'])) ?></p>
                    <p class="text-xs text-outline"><?= date('h:i A', strtotime($actividad['hora_publicacion'])) ?></p>
                </div>
                <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border-l-4 border-primary">
                    <p class="text-[10px] uppercase tracking-widest text-primary mb-1 font-bold">Acceso</p>
                    <p class="text-sm font-bold text-on-surface"><?= $actividad['tipo_acceso_legible'] ?></p>
                    <p class="text-xs text-outline"><?= ($actividad['privacidad'] == 'publica') ? 'Libre inscripción' : (($actividad['privacidad'] == 'privada') ? 'Solo invitados' : 'Requiere aprobación') ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Grid Info (Capacidad, Horario, Edad) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
        <!-- Capacidad -->
        <div class="md:col-span-2 lg:col-span-1 bg-surface-container-lowest p-8 rounded-2xl flex flex-col justify-between shadow-sm relative overflow-hidden">
            <div class="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined !text-[160px] leading-none select-none text-outline">groups</span>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-6">Capacidad</h3>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-5xl font-black text-primary"><?= $totalAsistentes ?></span>
                    <span class="text-xl font-medium text-outline">/ <?= $actividad['capacidad_max'] == 'Sin límite' ? '∞' : $actividad['capacidad_max'] ?></span>
                </div>
                <p class="text-sm text-on-surface-variant mb-6 font-medium">Asistentes confirmados</p>
            </div>
            <div class="relative z-10">
                <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: <?= min(100, $porcentajeCapacidad) ?>%"></div>
                </div>
                <div class="flex justify-between text-[10px] font-bold text-outline uppercase">
                    <span>Mín: <?= $actividad['capacidad_min'] ?></span>
                    <span>Máx: <?= $actividad['capacidad_max'] == 'Sin límite' ? '∞' : $actividad['capacidad_max'] ?></span>
                </div>
            </div>
        </div>

        <!-- Horario -->
        <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm">
            <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-6">Horario</h3>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="bg-primary/10 p-3 rounded-xl"><span class="material-symbols-outlined text-primary">event_upcoming</span></div>
                    <div><p class="text-xs font-bold text-outline uppercase tracking-tighter">Inicio</p><p class="text-base font-bold text-on-surface"><?= date('d M, h:i A', strtotime($actividad['fecha_inicio'])) ?></p></div>
                </div>
                <div class="flex gap-4">
                    <div class="bg-tertiary/10 p-3 rounded-xl"><span class="material-symbols-outlined text-tertiary">event_available</span></div>
                    <div><p class="text-xs font-bold text-outline uppercase tracking-tighter">Fin</p><p class="text-base font-bold text-on-surface"><?= date('d M, h:i A', strtotime($actividad['fecha_fin'])) ?></p></div>
                </div>
            </div>
        </div>

        <!-- Rango Edad -->
        <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-4"><span class="material-symbols-outlined text-on-surface text-3xl">face</span></div>
            <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-2">Rango de Edad</h3>
            <p class="text-4xl font-black text-on-surface tracking-tight"><?= $actividad['edad_minima'] ?> - <?= $actividad['edad_maxima'] ?></p>
            <p class="text-xs font-medium text-on-surface-variant mt-2">Años de edad</p>
        </div>
    </section>

    <!-- SECCIÓN MAPA + DIRECCIÓN (modificada) -->
    <section class="mb-20">
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
            <div id="map-container" class="relative transition-all duration-300">
                <div id="map" class="h-64 w-full"></div>
                <div id="map-overlay" class="absolute inset-0 flex items-center justify-center pointer-events-none bg-black/30 transition-opacity duration-300" style="opacity:0;">
                    <div class="bg-white/90 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold text-primary shadow-lg pointer-events-auto">📍 Toca el mapa para elegir tu punto de partida</div>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <!-- Dirección de la actividad (destino) -->
                <div>
                    <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-2">Ubicación de la actividad</h3>
                    <p class="text-base font-bold text-on-surface" id="direccion-actividad">
                        <?= htmlspecialchars($actividad['direccion'] ?? 'Obteniendo dirección...') ?>
                    </p>
                    <p class="text-xs text-outline mt-1" id="coordenadas-actividad" style="display:none;"><?= $actividad['lat'] . ',' . $actividad['lng'] ?></p>
                </div>
                <!-- Dirección del origen (punto de partida del usuario) -->
                <div id="origin-info" class="hidden border-t border-outline-variant/20 pt-3">
                    <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-1">Tu punto de partida</h3>
                    <p class="text-sm text-on-surface" id="direccion-origen"></p>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 text-sm font-medium cursor-pointer">
                        <input type="checkbox" id="toggleCar" checked class="w-4 h-4 rounded border-primary text-primary focus:ring-primary">
                        <span>Mostrar ruta</span>
                    </label>
                    <button id="resetOriginBtn" class="text-xs text-red-600 hover:text-red-700 font-medium px-3 py-1 rounded-full bg-red-50 hidden">Reiniciar origen</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Descripción y CTA -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-20">
        <div class="lg:col-span-8">
            <h2 class="text-2xl font-extrabold text-on-surface mb-6 flex items-center gap-2">Sobre la actividad<div class="h-1 flex-grow bg-surface-container rounded-full ml-4"></div></h2>
            <div class="prose prose-slate max-w-none text-on-surface-variant leading-relaxed space-y-4">
                <p class="text-lg"><?= nl2br(htmlspecialchars($actividad['descripcion'])) ?></p>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mt-8">
                    <div class="p-4 bg-surface-container-low rounded-xl">
                        <p class="font-bold text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">assignment</span> Requisitos
                        </p>
                        <ul class="text-sm space-y-1 opacity-80">
                            <?php if (!empty($actividad['requisitos'])): ?>
                                <?php foreach (explode("\n", $actividad['requisitos']) as $req): ?>
                                    <li>• <?= htmlspecialchars($req) ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>• No hay requisitos específicos</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-28 bg-white p-8 rounded-3xl shadow-xl border border-slate-50 flex flex-col gap-4">
                <p class="text-center text-sm font-medium text-on-surface-variant mb-2">¿Te gustaría participar en este evento?</p>
                <?php if ($esCreador): ?>
                    <button disabled class="w-full bg-gray-400 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">Eres el organizador</button>
                <?php elseif ($yaUnido): ?>
                    <form action="<?= BASE_URL ?>?c=participacion&a=salir" method="POST" onsubmit="return confirm('¿Seguro que deseas salir de esta actividad?');">
                        <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl font-bold text-lg shadow-md transition-all">Salir de la actividad</button>
                    </form>
                <?php elseif ($solicitudPendiente): ?>
                    <button disabled class="w-full bg-yellow-500 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">Solicitud pendiente</button>
                <?php elseif ($invitado): ?>
                    <form action="<?= BASE_URL ?>?c=participacion&a=solicitar" method="POST">
                        <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                        <button type="submit" class="w-full bg-primary bg-gradient-to-br from-primary to-primary-container text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">Aceptar invitación</button>
                    </form>
                <?php elseif ($actividadNoDisponible): ?>
                    <button disabled class="w-full bg-gray-400 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">Actividad no disponible</button>
                <?php elseif (!$edadValida): ?>
                    <button disabled class="w-full bg-red-500 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">No cumples el rango de edad</button>
                <?php elseif ($capacidadLlena): ?>
                    <button disabled class="w-full bg-red-500 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">Actividad llena</button>
                <?php elseif ($actividad['privacidad'] == 'privada'): ?>
                    <button disabled class="w-full bg-yellow-500 text-white py-4 rounded-2xl font-bold text-lg cursor-not-allowed">Privada (solo invitados)</button>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>?c=participacion&a=solicitar" method="POST">
                        <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                        <button type="submit" class="w-full bg-primary bg-gradient-to-br from-primary to-primary-container text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all"><?= ($actividad['privacidad'] == 'por_aprobacion') ? 'Solicitar unión' : 'Confirmar Asistencia' ?></button>
                    </form>
                <?php endif; ?>

                <div class="mt-4 flex -space-x-2 justify-center overflow-hidden py-2">
                    <?php foreach ($asistentes as $asis): ?>
                        <img src="<?= $asis['foto_base64'] ?? '../Recursos/user.png' ?>" alt="<?= htmlspecialchars($asis['nombre_completo']) ?>" class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover">
                    <?php endforeach; ?>
                    <?php if ($otrosAsistentes > 0): ?>
                        <div class="flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-slate-100 text-[10px] font-bold text-slate-500">+<?= $otrosAsistentes ?></div>
                    <?php endif; ?>
                </div>
                <p class="text-[10px] text-center text-outline uppercase tracking-widest mt-2"><?= $totalAsistentes ?> personas asistirán</p>
            </div>
        </div>
    </section>

    <!-- Reseñas -->
    <section class="mt-16">
        <h2 class="text-2xl font-extrabold text-on-surface mb-8 flex items-center gap-2">Reseñas de la comunidad<div class="h-1 flex-grow bg-surface-container rounded-full ml-4"></div></h2>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 bg-surface-container-lowest p-8 rounded-3xl shadow-sm border border-slate-50 self-start">
                <div class="text-center space-y-2">
                    <p class="text-6xl font-black text-on-surface"><?= $statsResenas['promedio'] ?></p>
                    <div class="flex justify-center text-primary">
                        <?php
                        $full = floor($statsResenas['promedio']);
                        $half = ($statsResenas['promedio'] - $full) >= 0.5 ? 1 : 0;
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $full) echo '<span class="material-symbols-outlined fill-1">star</span>';
                            elseif ($i == $full + 1 && $half) echo '<span class="material-symbols-outlined fill-1">star_half</span>';
                            else echo '<span class="material-symbols-outlined">star</span>';
                        }
                        ?>
                    </div>
                    <p class="text-sm font-bold text-on-surface-variant uppercase tracking-widest mt-4">Promedio de <?= $statsResenas['total'] ?> reseñas</p>
                </div>
                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3"><span class="text-xs font-bold text-outline w-4">5</span><div class="flex-grow h-2 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full" style="width: <?= $statsResenas['porcentaje_5'] ?>%"></div></div></div>
                    <div class="flex items-center gap-3"><span class="text-xs font-bold text-outline w-4">4</span><div class="flex-grow h-2 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full" style="width: <?= $statsResenas['porcentaje_4'] ?>%"></div></div></div>
                    <div class="flex items-center gap-3"><span class="text-xs font-bold text-outline w-4">3</span><div class="flex-grow h-2 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full" style="width: <?= $statsResenas['porcentaje_3'] ?>%"></div></div></div>
                    <div class="flex items-center gap-3"><span class="text-xs font-bold text-outline w-4">2</span><div class="flex-grow h-2 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full" style="width: <?= $statsResenas['porcentaje_2'] ?>%"></div></div></div>
                    <div class="flex items-center gap-3"><span class="text-xs font-bold text-outline w-4">1</span><div class="flex-grow h-2 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full" style="width: <?= $statsResenas['porcentaje_1'] ?>%"></div></div></div>
                </div>
            </div>
            <div class="lg:col-span-8 space-y-6">
                <?php if ($puedeResenar): ?>
                    <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm border border-outline-variant/10">
                        <h3 class="font-bold mb-4">Deja tu reseña</h3>
                        <form action="<?= BASE_URL ?>?c=actividad&a=guardarResena" method="POST">
                            <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                            <select name="calificacion" class="mb-3 p-2 border rounded" required>
                                <option value="5">5 - Excelente</option>
                                <option value="4">4 - Muy bueno</option>
                                <option value="3">3 - Bueno</option>
                                <option value="2">2 - Regular</option>
                                <option value="1">1 - Malo</option>
                            </select>
                            <textarea name="comentario" rows="3" class="w-full p-2 border rounded" placeholder="Tu experiencia..."></textarea>
                            <button type="submit" class="mt-3 bg-primary text-white px-4 py-2 rounded-xl">Enviar reseña</button>
                        </form>
                    </div>
                <?php endif; ?>
                <?php if (empty($resenas)): ?>
                    <p class="text-center text-outline">Aún no hay reseñas para esta actividad.</p>
                <?php else: ?>
                    <?php foreach ($resenas as $r): ?>
                        <div class="break-inside-avoid bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm border border-outline-variant/10 space-y-6">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-4">
                                    <img class="h-12 w-12 rounded-full object-cover" src="<?= (!empty($r['foto_perfil']) ? ('data:image/jpeg;base64,' . base64_encode($r['foto_perfil'])) : '../Recursos/user.png') ?>" alt="<?= htmlspecialchars($r['usuario_nombre']) ?>">
                                    <div>
                                        <h4 class="font-bold text-on-surface"><?= htmlspecialchars($r['usuario_nombre']) ?></h4>
                                        <div class="flex text-primary">
                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' <?= ($i <= $r['calificacion']) ? 1 : 0 ?>;">star</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-on-surface-variant/60"><?= date('d M Y', strtotime($r['fecha_resena'])) ?></span>
                            </div>
                            <p class="text-on-surface-variant italic leading-relaxed">"<?= nl2br(htmlspecialchars($r['comentario'])) ?>"</p>
                        </div>
                    <?php endforeach; ?>
                    <button class="w-full py-4 text-sm font-bold text-primary uppercase tracking-widest hover:bg-surface-container-high rounded-xl transition-colors">Ver todas las reseñas</button>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<style>
    #map-container {
        transition: all 0.3s ease;
    }
    #map-container.expanded {
        height: 70vh;
    }
    #map-container.expanded #map {
        height: 100%;
    }
    #map-container .leaflet-control-attribution {
        font-size: 8px;
    }
    @media (min-width: 768px) {
        #map-container.expanded {
            height: 80vh;
        }
    }
    .leaflet-marker-icon.draggable-marker {
        cursor: grab;
    }
    .leaflet-marker-icon.draggable-marker:active {
        cursor: grabbing;
    }
</style>

<?php include 'includes/bottom-nav.php'; ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Coordenadas de la actividad (destino)
    var actividadLat = <?= (float)$actividad['lat'] ?>;
    var actividadLng = <?= (float)$actividad['lng'] ?>;
    var actividadNombre = "<?= htmlspecialchars($actividad['nombre']) ?>";
    // Ubicación del usuario (origen por defecto, si existe)
    var userLat = <?= isset($userLat) && $userLat ? (float)$userLat : 'null' ?>;
    var userLng = <?= isset($userLng) && $userLng ? (float)$userLng : 'null' ?>;
    // Dirección almacenada en BD (puede ser null)
    var direccionBD = <?= $direccionBD ?>;

    var map, destinationMarker, originMarker = null;
    var carRoute = null;
    var originPoint = null;
    var addressOrigin = "";

    if (!actividadLat || !actividadLng || (actividadLat === 0 && actividadLng === 0)) {
        document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full bg-gray-200 text-outline">Ubicación no disponible</div>';
    } else {
        map = L.map('map').setView([actividadLat, actividadLng], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);

        // Icono destino (rosado)
        function getDestinationIcon() {
            return L.divIcon({
                className: 'custom-icon',
                html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="#7c3aed" stroke="#ffffff" stroke-width="3" style="width:30px; height:30px;"><path d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z"/></svg>`,
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
        }
        // Icono origen (azul, arrastrable)
        function getOriginIcon() {
            return L.divIcon({
                className: 'custom-icon draggable-marker',
                html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="#2196f3" stroke="#ffffff" stroke-width="3" stroke-linejoin="round" style="width:30px; height:30px;"><path d="M320 64C355.3 64 384 92.7 384 128C384 163.3 355.3 192 320 192C284.7 192 256 163.3 256 128C256 92.7 284.7 64 320 64zM288 224L352 224C387.3 224 416 252.7 416 288L416 336C416 353.7 401.7 368 384 368L382.2 368L371.1 467.5C369.3 483.7 355.6 496 339.3 496L300.6 496C284.3 496 270.6 483.7 268.8 467.5L257.7 368L255.9 368C238.2 368 223.9 353.7 223.9 336L223.9 288C223.9 252.7 252.6 224 287.9 224zM476.4 464.2C460.3 460 441.6 456.6 421 454L426.3 406.3C449 409.2 470 413 488.4 417.8C510.8 423.6 531 431.1 546.2 441.1C560.9 450.7 576 466 576 488.1C576 510.2 560.9 525.5 546.2 535.1C531 545 510.7 552.6 488.4 558.4C443.3 570.1 383.1 576.2 320 576.2C256.9 576.2 196.7 570.1 151.6 558.4C129.2 552.4 109 544.9 93.8 535C79.1 525.4 64 510.1 64 488C64 465.9 79.1 450.6 93.8 441C109 431.1 129.3 423.5 151.6 417.7C170.1 412.9 191.1 409.1 213.7 406.2L219 454C198.4 456.6 179.7 460.1 163.6 464.2C107 478.8 107 497.1 163.6 511.7C203.5 522 259.4 527.9 320 527.9C380.6 527.9 436.5 522 476.4 511.7C533 497.1 533 478.8 476.4 464.2z"/></svg>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
        }

        destinationMarker = L.marker([actividadLat, actividadLng], { icon: getDestinationIcon() }).addTo(map);
        destinationMarker.bindTooltip(actividadNombre).openTooltip();

        // Expansión con doble clic
        var mapContainer = document.getElementById('map-container');
        if (mapContainer) {
            map.on('dblclick', function() {
                mapContainer.classList.toggle('expanded');
                setTimeout(function() { map.invalidateSize(); }, 300);
            });
        }

        // Geocodificación inversa (para casos donde no hay dirección en BD)
        async function reverseGeocode(lat, lon) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`;
            try {
                const response = await fetch(url, { headers: { 'Accept-Language': 'es' } });
                return await response.json();
            } catch (error) {
                console.error(error);
                return null;
            }
        }

        async function getDetailedAddress(lat, lon) {
            const offset = 0.00028;
            const scanCoords = [[lat, lon], [lat + offset, lon], [lat - offset, lon], [lat, lon + offset], [lat, lon - offset]];
            const results = await Promise.all(scanCoords.map(c => reverseGeocode(c[0], c[1])));
            let roads = results.map(r => r?.address?.road || r?.address?.pedestrian).filter(Boolean);
            let uniqueRoads = [...new Set(roads)];
            let streetString = uniqueRoads.length >= 2 ? `${uniqueRoads[0]} y ${uniqueRoads[1]}` : (uniqueRoads[0] || "Ubicación");
            const main = results[0]?.address || {};
            let ciudad = main.city || main.town || main.village || main.suburb || "";
            let estado = main.state || "";
            let direccionFinal = streetString;
            if (ciudad) direccionFinal += `, ${ciudad}`;
            if (estado && !ciudad.includes(estado)) direccionFinal += `, ${estado}`;
            return { display: direccionFinal };
        }

        // Función mejorada para mostrar dirección (prioriza BD)
        async function showActivityAddress() {
            const direccionSpan = document.getElementById('direccion-actividad');
            if (!direccionSpan) return;

            // Si ya tenemos una dirección en la base de datos, mostrarla inmediatamente
            if (direccionBD && direccionBD !== 'null') {
                direccionSpan.textContent = direccionBD;
                return;
            }

            // Si no, obtenerla desde coordenadas mediante geocoding
            if (actividadLat && actividadLng) {
                try {
                    const addr = await getDetailedAddress(actividadLat, actividadLng);
                    direccionSpan.textContent = addr.display || "Dirección no encontrada";
                } catch(e) {
                    direccionSpan.textContent = "Error al cargar dirección";
                }
            } else {
                direccionSpan.textContent = "Ubicación no disponible";
            }
        }

        // Obtener ruta en auto (OSRM)
        async function fetchCarRoute(start, end) {
            const url = `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`;
            const response = await fetch(url);
            const data = await response.json();
            if (data.routes && data.routes.length > 0) {
                return data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
            }
            return null;
        }

        function drawCarRoute(startCoord, endCoord) {
            if (carRoute) map.removeLayer(carRoute);
            fetchCarRoute(startCoord, endCoord).then(coords => {
                if (coords && coords.length) {
                    carRoute = L.polyline(coords, { color: '#7c3aed', weight: 6, opacity: 0.8 }).addTo(map);
                    if (!document.getElementById('toggleCar').checked) map.removeLayer(carRoute);
                } else {
                    console.warn("No se pudo obtener ruta en auto");
                }
            }).catch(err => console.error(err));
        }

        function removeOrigin() {
            if (originMarker) {
                map.removeLayer(originMarker);
                originMarker = null;
            }
            if (carRoute) { map.removeLayer(carRoute); carRoute = null; }
            originPoint = null;
            document.getElementById('origin-info').classList.add('hidden');
            document.getElementById('resetOriginBtn').classList.add('hidden');
        }

        async function setOrigin(lat, lng, updateView = true) {
            removeOrigin();
            originPoint = { lat: lat, lng: lng };
            originMarker = L.marker([lat, lng], { icon: getOriginIcon(), draggable: true }).addTo(map);
            originMarker.bindTooltip("Tu punto de partida (arrastra)").openTooltip();

            originMarker.on('dragend', async function(e) {
                const newPos = e.target.getLatLng();
                originPoint = { lat: newPos.lat, lng: newPos.lng };
                if (document.getElementById('toggleCar').checked) {
                    drawCarRoute(originPoint, { lat: actividadLat, lng: actividadLng });
                }
                try {
                    const addr = await getDetailedAddress(newPos.lat, newPos.lng);
                    addressOrigin = addr.display;
                    document.getElementById('direccion-origen').textContent = addressOrigin;
                } catch(e) {
                    document.getElementById('direccion-origen').textContent = `${newPos.lat.toFixed(5)}, ${newPos.lng.toFixed(5)}`;
                }
                if (updateView && carRoute) map.fitBounds(carRoute.getBounds(), { padding: [40,40] });
            });

            try {
                const addr = await getDetailedAddress(lat, lng);
                addressOrigin = addr.display;
                document.getElementById('direccion-origen').textContent = addressOrigin;
                document.getElementById('origin-info').classList.remove('hidden');
                document.getElementById('resetOriginBtn').classList.remove('hidden');
            } catch(e) {
                document.getElementById('direccion-origen').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            }

            if (document.getElementById('toggleCar').checked) {
                drawCarRoute(originPoint, { lat: actividadLat, lng: actividadLng });
            }
            if (updateView && carRoute) map.fitBounds(carRoute.getBounds(), { padding: [40,40] });
        }

        // Controles
        const toggleCar = document.getElementById('toggleCar');
        if (toggleCar) {
            toggleCar.addEventListener('change', (e) => {
                if (originPoint) {
                    if (e.target.checked) drawCarRoute(originPoint, { lat: actividadLat, lng: actividadLng });
                    else if (carRoute) map.removeLayer(carRoute);
                }
            });
        }

        const resetBtn = document.getElementById('resetOriginBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                removeOrigin();
                if (userLat && userLng && userLat !== 0 && userLng !== 0) {
                    setOrigin(userLat, userLng);
                } else {
                    const overlay = document.getElementById('map-overlay');
                    if (overlay) overlay.style.opacity = '1';
                    setTimeout(() => { if(overlay) overlay.style.opacity = '0'; }, 3000);
                }
            });
        }

        // Inicializar origen
        if (userLat && userLng && userLat !== 0 && userLng !== 0) {
            setOrigin(userLat, userLng);
        } else {
            const overlay = document.getElementById('map-overlay');
            if (overlay) overlay.style.opacity = '1';
            map.once('click', async (e) => {
                if (overlay) overlay.style.opacity = '0';
                const { lat, lng } = e.latlng;
                await setOrigin(lat, lng);
            });
        }

        // Mostrar dirección de la actividad usando el nuevo método
        showActivityAddress();
    }
</script>
</body>
</html>
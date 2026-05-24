<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
if (!isset($datos)) {
    die("Error: datos no disponibles");
}
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased h-screen overflow-hidden flex flex-col">
    <?php require_once __DIR__ . '/../../includes/top-nav.php'; ?>

    <main class="flex-1 overflow-y-auto pt-20 pb-20 md:pb-12 px-4 md:px-6 max-w-7xl mx-auto w-full" id="scroll">
        <div id="globalLoader" class="hidden fixed inset-0 bg-black/20 flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded-full shadow-lg">
                <span class="material-symbols-outlined animate-spin text-primary text-3xl">progress_activity</span>
            </div>
        </div>

        <!-- Hero -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
            <div class="lg:col-span-8">
                <div class="relative rounded-2xl overflow-hidden aspect-[16/9] shadow-2xl group">
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($datos['actividad']['foto_base64'] ?? 'https://via.placeholder.com/1200x675?text=Sin+imagen') ?>" alt="Imagen actividad">
                    <div class="absolute top-6 left-6 flex gap-2">
                        <span class="bg-violet-600 text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase shadow-lg"><?= htmlspecialchars($datos['actividad']['estado'] ?? 'Pendiente') ?></span>
                        <span class="bg-white/90 backdrop-blur text-slate-900 px-4 py-1.5 rounded-full text-xs font-bold uppercase shadow-lg"><?= htmlspecialchars($datos['actividad']['categoria'] ?? 'General') ?></span>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 flex flex-col justify-center space-y-3">
                <div><h1 class="text-3xl lg:text-4xl font-extrabold text-on-surface leading-tight tracking-tight mb-2"><?= htmlspecialchars($datos['actividad']['nombre']) ?></h1></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm">
                        <p class="text-[10px] uppercase tracking-widest text-outline mb-1 font-bold">Inicio</p>
                        <p class="text-sm font-semibold text-on-surface"><?= date('d M Y', strtotime($datos['actividad']['fecha_inicio'])) ?></p>
                        <p class="text-xs text-outline"><?= date('H:i', strtotime($datos['actividad']['fecha_inicio'])) ?></p>
                    </div>
                    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border-l-4 border-primary">
                        <p class="text-[10px] uppercase tracking-widest text-primary mb-1 font-bold">Privacidad</p>
                        <p class="text-sm font-bold text-on-surface">
                            <?php
                            switch ($datos['actividad']['privacidad']) {
                                case 'publica': echo 'Pública'; break;
                                case 'privada': echo 'Privada'; break;
                                case 'por_aprobacion': echo 'Por aprobación'; break;
                                default: echo $datos['actividad']['privacidad'];
                            }
                            ?>
                        </p>
                        <p class="text-xs text-outline">
                            <?= $datos['actividad']['privacidad'] === 'por_aprobacion' ? 'Creador valida solicitudes' : ($datos['actividad']['privacidad'] === 'privada' ? 'Solo invitados' : 'Cualquiera puede unirse') ?>
                        </p>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm space-y-4">
                    <div class="flex justify-between items-center p-4 bg-surface-container-low rounded-2xl">
                        <div class="flex items-center gap-3"><span class="material-symbols-outlined text-primary">group</span><span class="font-medium">Participantes aceptados</span></div>
                        <span class="text-xl font-bold text-primary"><?= $datos['estadisticas']['total_aceptados'] ?></span>
                    </div>
                    <div class="w-full bg-surface-container-high h-3 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-container h-full rounded-full" style="width: <?= $datos['estadisticas']['porcentaje_ocupacion'] ?>%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="bg-surface-container-low p-4 rounded-2xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Solicitudes</span>
                            <span class="text-2xl font-black text-on-surface"><?= $datos['estadisticas']['total_solicitudes'] ?></span>
                        </div>
                        <div class="bg-surface-container-low p-4 rounded-2xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Reseñas</span>
                            <span class="text-2xl font-black text-on-surface"><?= $datos['estadisticas']['total_resenas'] ?></span>
                            <span class="text-xs text-on-surface-variant block">(<?= $datos['estadisticas']['promedio_resenas'] ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Info -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm">
                <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-6">Horario</h3>
                <div class="space-y-6">
                    <div class="flex gap-4"><div class="bg-primary/10 p-3 rounded-xl"><span class="material-symbols-outlined text-primary">event_upcoming</span></div><div><p class="text-xs font-bold text-outline uppercase">Inicio</p><p class="text-base font-bold text-on-surface"><?= date('d M Y, H:i', strtotime($datos['actividad']['fecha_inicio'])) ?></p></div></div>
                    <div class="flex gap-4"><div class="bg-tertiary/10 p-3 rounded-xl"><span class="material-symbols-outlined text-tertiary">event_available</span></div><div><p class="text-xs font-bold text-outline uppercase">Fin</p><p class="text-base font-bold text-on-surface"><?= date('d M Y, H:i', strtotime($datos['actividad']['fecha_fin'])) ?></p></div></div>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-4"><span class="material-symbols-outlined text-on-surface text-3xl">face</span></div>
                <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-2">Rango de Edad</h3>
                <p class="text-4xl font-black text-on-surface tracking-tight"><?= $datos['actividad']['edad_minima'] ?> - <?= $datos['actividad']['edad_maxima'] ?></p>
                <p class="text-xs font-medium text-on-surface-variant mt-2">Años</p>
            </div>
            <!-- Mapa Leaflet -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div id="map" class="h-48 w-full bg-slate-200 relative z-0"></div>
                <div class="p-6">
                    <h3 class="text-sm font-bold text-outline uppercase tracking-widest mb-2">Ubicación</h3>
                    <p id="direccionTexto" class="text-base font-bold text-on-surface"><?= htmlspecialchars($datos['actividad']['direccion'] ?? 'Obteniendo dirección...') ?></p>
                </div>
            </div>
        </section>

        <!-- Descripción -->
        <section class="mb-20">
            <h2 class="text-2xl font-extrabold text-on-surface mb-6 flex items-center gap-2">Sobre la actividad<div class="h-1 flex-grow bg-surface-container rounded-full ml-4"></div></h2>
            <div class="prose prose-slate max-w-none text-on-surface-variant leading-relaxed space-y-4"><?= nl2br(htmlspecialchars($datos['actividad']['descripcion'] ?? 'Sin descripción')) ?></div>
        </section>

        <!-- Gestión -->
        <section class="space-y-8">
            <div class="flex items-baseline gap-4"><h2 class="text-2xl font-extrabold text-on-surface flex items-center gap-2">Gestión de actividad</h2><span class="h-1 flex-grow bg-surface-container-low rounded-full"></span></div>
            <div class="grid grid-cols-1 <?= $datos['actividad']['privacidad'] === 'por_aprobacion' ? 'lg:grid-cols-2' : '' ?> gap-8">
                <?php if ($datos['actividad']['privacidad'] === 'por_aprobacion'): ?>
                <div class="bg-surface-container-lowest rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-outline-variant/20">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold flex items-center gap-2"><span class="material-symbols-outlined text-amber-600">pending_actions</span>Solicitudes Pendientes</h3>
                            <span class="bg-surface-container-high text-on-surface-variant px-3 py-1 rounded-full text-sm font-bold"><?= count($datos['solicitudes']) ?></span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="solicitudesList" class="space-y-3">
                            <?php if (empty($datos['solicitudes'])): ?>
                                <div class="text-center py-8 text-outline"><span class="material-symbols-outlined text-3xl">inbox</span><p class="text-sm mt-1">Sin solicitudes pendientes</p></div>
                            <?php else: ?>
                                <?php foreach ($datos['solicitudes'] as $s): ?>
                                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl" data-id-usuario="<?= $s['id_usuario'] ?>">
                                        <div class="flex items-center gap-4">
                                            <img class="h-12 w-12 rounded-full object-cover" src="<?= $s['foto_base64'] ?? '../Recursos/user.png' ?>">
                                            <div><p class="font-bold"><?= htmlspecialchars($s['nombre_completo']) ?></p><p class="text-xs">Solicitado <?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?></p></div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button class="rechazar-solicitud h-10 w-10 rounded-full bg-error/10 text-error hover:bg-error hover:text-on-error"><span class="material-symbols-outlined text-xl">close</span></button>
                                            <button class="aceptar-solicitud h-10 w-10 rounded-full bg-primary/10 text-primary hover:bg-primary hover:text-on-primary"><span class="material-symbols-outlined text-xl">check</span></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-surface-container-lowest rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-outline-variant/20">
                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <h3 class="text-xl font-bold flex items-center gap-2"><span class="material-symbols-outlined text-primary">verified</span>Asistentes Confirmados</h3>
                            <span class="bg-surface-container-high text-on-surface-variant px-3 py-1 rounded-full text-sm font-bold"><?= $datos['estadisticas']['asistentes_presentes'] ?>/<?= $datos['estadisticas']['total_aceptados'] ?> asistentes</span>
                            <button id="btnPaseManual" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-primary/10 transition flex items-center gap-1"><span class="material-symbols-outlined text-lg">edit_note</span>Pase de lista manual</button>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div id="asistentesList" class="grid grid-cols-1 gap-4 max-h-[360px] overflow-y-auto pr-1">
                            <?php foreach ($datos['participantes'] as $p): ?>
                                <?php 
                                    $esCreador = $datos['permisos']['es_creador'];
                                    $esOrganizador = $datos['permisos']['es_organizador'];
                                    $puedeGestionarAsistencia = $esCreador || ($esOrganizador && $p['rol'] === 'miembro');
                                ?>
                                <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 flex flex-col justify-between gap-4 <?= $p['asistio'] ? 'ring-1 ring-green-500/40' : '' ?>" data-id-usuario="<?= $p['id_usuario'] ?>">
                                    <div class="flex items-center gap-3">
                                        <img class="h-10 w-10 rounded-full object-cover" src="<?= $p['foto_base64'] ?? '../Recursos/user.png' ?>">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="font-semibold text-base truncate"><?= htmlspecialchars($p['nombre_completo']) ?></span>
                                            <?= $p['rol'] === 'organizador' ? '<span class="material-symbols-outlined text-amber-500 text-base">stars</span>' : ($p['rol'] === 'creador' ? '<span class="material-symbols-outlined text-primary text-base">crown</span>' : '') ?>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex gap-2">
                                            <?php if ($puedeGestionarAsistencia): ?>
                                                <button class="marcar-asistencia text-green-600 hover:bg-green-50 p-2 rounded-full"><span class="material-symbols-outlined text-2xl">check_circle</span></button>
                                                <button class="quitar-asistencia text-red-600 hover:bg-red-50 p-2 rounded-full"><span class="material-symbols-outlined text-2xl">do_not_disturb_alt</span></button>
                                            <?php else: ?>
                                                <span class="text-outline p-2" title="No tienes permiso para marcar asistencia a este participante"><span class="material-symbols-outlined text-2xl">block</span></span>
                                            <?php endif; ?>
                                            <?php if ($esCreador && $p['rol'] !== 'creador'): ?>
                                                <button class="eliminar-participante text-outline hover:bg-surface-container p-2 rounded-full"><span class="material-symbols-outlined text-2xl">delete</span></button>
                                            <?php endif; ?>
                                            <?php if ($esCreador && $p['rol'] === 'miembro'): ?>
                                                <button class="ascender-organizador text-blue-600 hover:bg-blue-50 p-2 rounded-full"><span class="material-symbols-outlined text-2xl">star</span></button>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-sm font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5 <?= $p['asistio'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <span class="material-symbols-outlined text-base"><?= $p['asistio'] ? 'check_circle' : 'do_not_disturb_alt' ?></span>
                                            <?= $p['asistio'] ? 'Presente' : 'No asistió' ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="manualChecklistPanel" class="hidden border-t border-outline-variant/30 pt-4 mt-2">
                            <p class="text-sm font-bold mb-3 flex items-center gap-1"><span class="material-symbols-outlined text-sm">checklist</span>Marcar asistencia manual:</p>
                            <div id="manualChecklist" class="space-y-2 max-h-48 overflow-y-auto bg-surface-container-low p-3 rounded-xl"></div>
                            <button id="guardarAsistenciaManual" class="mt-4 w-full py-2.5 bg-primary text-on-primary rounded-xl font-bold text-sm hover:bg-primary-dim transition">Guardar asistencia</button>
                        </div>
                        
                        <div class="border-t border-outline-variant/30 pt-5">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold flex items-center gap-1"><span class="material-symbols-outlined text-tertiary">manage_accounts</span>Organizadores</h4>
                                <span class="text-xs text-outline">Pueden pasar lista (solo a miembros)</span>
                            </div>
                            <div id="organizadoresList" class="flex flex-wrap gap-2 mt-3">
                                <?php foreach ($datos['organizadores'] as $org): ?>
                                    <span class="bg-primary/10 text-primary px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">star</span><?= htmlspecialchars($org['nombre_completo']) ?></span>
                                <?php endforeach; ?>
                                <?php if (empty($datos['organizadores'])): ?>
                                    <span class="text-xs text-outline flex items-center gap-1"><span class="material-symbols-outlined text-sm">info</span> Sin organizadores aún</span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-4">
                                <button id="btnMostrarAmigos" class="text-sm text-primary flex items-center gap-1 hover:underline"><span class="material-symbols-outlined text-sm">person_add</span>Agregar organizador (amigos)</button>
                            </div>
                            <div id="amigosPanel" class="hidden mt-3 p-4 bg-surface-container-high rounded-xl space-y-3">
                                <p class="text-xs font-bold">Selecciona un amigo para agregar como organizador:</p>
                                <div id="amigosList" class="space-y-2 max-h-40 overflow-y-auto">
                                    <?php foreach ($datos['amigos_disponibles'] as $am): ?>
                                        <div class="flex justify-between items-center p-2 bg-white rounded-xl shadow-sm">
                                            <div class="flex items-center gap-2"><img class="h-8 w-8 rounded-full" src="<?= $am['foto_base64'] ?? '../Recursos/user.png' ?>"><span class="text-sm font-medium"><?= htmlspecialchars($am['nombre_completo']) ?></span></div>
                                            <button class="agregar-organizador bg-primary/20 text-primary px-3 py-1 rounded-lg text-xs font-bold hover:bg-primary hover:text-white" data-id="<?= $am['id_usuario'] ?>"><span class="material-symbols-outlined text-sm">add</span> Agregar</button>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($datos['amigos_disponibles'])): ?>
                                        <p class="text-xs text-outline text-center py-2">No hay amigos disponibles</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
						
						<!-- Invitar amigos como miembros -->
						<div class="border-t border-outline-variant/30 pt-5">
							<div class="flex justify-between items-center">
								<h4 class="font-bold flex items-center gap-1"><span class="material-symbols-outlined text-primary">person_add</span>Invitar amigos</h4>
								<span class="text-xs text-outline">Se unirán como miembros</span>
							</div>
							<div class="mt-4">
								<button id="btnMostrarAmigosMiembros" class="text-sm text-primary flex items-center gap-1 hover:underline">
									<span class="material-symbols-outlined text-sm">group_add</span> Invitar amigos a la actividad
								</button>
							</div>
							<div id="amigosMiembrosPanel" class="hidden mt-3 p-4 bg-surface-container-high rounded-xl space-y-3">
								<p class="text-xs font-bold">Selecciona un amigo para invitarlo como miembro:</p>
								<div id="amigosMiembrosList" class="space-y-2 max-h-40 overflow-y-auto">
									<?php foreach ($datos['amigos_disponibles'] as $am): ?>
										<div class="flex justify-between items-center p-2 bg-white rounded-xl shadow-sm">
											<div class="flex items-center gap-2">
												<img class="h-8 w-8 rounded-full" src="<?= $am['foto_base64'] ?? '../Recursos/user.png' ?>">
												<span class="text-sm font-medium"><?= htmlspecialchars($am['nombre_completo']) ?></span>
											</div>
											<button class="invitar-miembro bg-primary/20 text-primary px-3 py-1 rounded-lg text-xs font-bold hover:bg-primary hover:text-white transition flex items-center gap-1" data-id="<?= $am['id_usuario'] ?>">
												<span class="material-symbols-outlined text-sm">send</span> Invitar
											</button>
										</div>
									<?php endforeach; ?>
									<?php if (empty($datos['amigos_disponibles'])): ?>
										<p class="text-xs text-outline text-center py-2">No hay amigos disponibles para invitar</p>
									<?php endif; ?>
								</div>
							</div>
						</div>
												
                        <button id="descargarLista" class="w-full py-3.5 border-2 border-dashed border-outline-variant/50 text-on-surface-variant font-bold rounded-xl hover:bg-surface-container-highest transition flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">file_download</span> Descargar Lista (PDF)
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>
    <div id="toast" class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-black/80 text-white px-4 py-2 rounded-full text-sm z-50 hidden transition-all"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?php
    // Limpiar datos binarios antes de convertirlos a JSON para JavaScript
    $actividadJS = isset($datos['actividad']) ? $datos['actividad'] : [];
    unset($actividadJS['foto_actividad']); // Eliminar BLOB de imagen

    $participantesJS = isset($datos['participantes']) ? $datos['participantes'] : [];
    foreach ($participantesJS as &$p) {
        unset($p['foto_perfil']);
        unset($p['foto']);
        unset($p['foto_usuario']);
    }

    $organizadoresJS = isset($datos['organizadores']) ? $datos['organizadores'] : [];
    foreach ($organizadoresJS as &$o) {
        unset($o['foto_perfil']);
        unset($o['foto']);
    }
    ?>

    <script>
        // Datos desde PHP (seguros, sin binarios)
        const BASE_URL = '<?= BASE_URL ?>';
        const actividadId = <?= $id_actividad ?>;

        const permisosActuales = <?= json_encode(
            $datos['permisos'] ?? [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '{}' ?>;

        const participantesData = <?= json_encode(
            $participantesJS,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '[]' ?>;

        const actividadData = <?= json_encode(
            $actividadJS,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '{}' ?>;

        const organizadoresData = <?= json_encode(
            $organizadoresJS,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '[]' ?>;

        function showLoader() { document.getElementById('globalLoader').classList.remove('hidden'); }
        function hideLoader() { document.getElementById('globalLoader').classList.add('hidden'); }
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.classList.remove('hidden');
            toast.style.backgroundColor = isError ? '#b41340' : '#2d2f2f';
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        // Funciones de gestión
        async function gestionarSolicitud(idUsuario, accion) {
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idUsuario);
            const endpoint = accion === 'aceptar' ? 'aceptarSolicitud' : 'rechazarSolicitud';
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=${endpoint}`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast(json.message); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }

        async function agregarOrganizador(idAmigo) {
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idAmigo);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=agregarOrganizador`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast(json.message); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }

        async function marcarAsistencia(idUsuario) {
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idUsuario);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=marcarAsistencia`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast('Asistencia marcada'); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }

        async function quitarAsistencia(idUsuario) {
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idUsuario);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=quitarAsistencia`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast('Asistencia eliminada'); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }

        async function eliminarParticipante(idUsuario) {
            if (!confirm('¿Eliminar este participante de la actividad?')) return;
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idUsuario);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=eliminarParticipante`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast('Participante eliminado'); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }

        async function ascenderMiembro(idUsuario) {
            if (!confirm('¿Ascender a este miembro como organizador?')) return;
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_usuario', idUsuario);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=ascenderMiembro`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast(json.message); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        }
		
		async function invitarAmigo(idAmigo) {
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('id_amigo', idAmigo);
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=invitarAmigo`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) {
                    showToast(json.message);
                    location.reload();
                } else {
                    showToast(json.message, true);
                }
            } catch (e) {
                showToast('Error', true);
            } finally {
                hideLoader();
            }
        }
		
        function bindEvents() {
            document.querySelectorAll('.aceptar-solicitud').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) gestionarSolicitud(card.dataset.idUsuario, 'aceptar');
                });
            });
            document.querySelectorAll('.rechazar-solicitud').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) gestionarSolicitud(card.dataset.idUsuario, 'rechazar');
                });
            });
            document.querySelectorAll('.marcar-asistencia').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) marcarAsistencia(card.dataset.idUsuario);
                });
            });
            document.querySelectorAll('.quitar-asistencia').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) quitarAsistencia(card.dataset.idUsuario);
                });
            });
            document.querySelectorAll('.eliminar-participante').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) eliminarParticipante(card.dataset.idUsuario);
                });
            });
            document.querySelectorAll('.ascender-organizador').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-id-usuario]');
                    if (card && card.dataset.idUsuario) ascenderMiembro(card.dataset.idUsuario);
                });
            });
            document.querySelectorAll('.agregar-organizador').forEach(btn => {
                btn.addEventListener('click', () => {
                    agregarOrganizador(btn.dataset.id);
                });
            });
			
			document.querySelectorAll('.invitar-miembro').forEach(btn => {
                btn.addEventListener('click', () => {
                    invitarAmigo(btn.dataset.id);
                });
            });
            document.getElementById('btnMostrarAmigosMiembros').addEventListener('click', () => {
                document.getElementById('amigosMiembrosPanel').classList.toggle('hidden');
            });
        }

        // Pase de lista manual
        const btnPaseManual = document.getElementById('btnPaseManual');
        const panelManual = document.getElementById('manualChecklistPanel');
        const checklistDiv = document.getElementById('manualChecklist');
        const guardarManual = document.getElementById('guardarAsistenciaManual');
        
        btnPaseManual.addEventListener('click', () => {
            panelManual.classList.toggle('hidden');
            if (!panelManual.classList.contains('hidden')) {
                const asistentes = document.querySelectorAll('#asistentesList > div');
                checklistDiv.innerHTML = Array.from(asistentes).map(div => {
                    const id = div.dataset.idUsuario;
                    const nombreSpan = div.querySelector('.font-semibold');
                    let nombre = nombreSpan ? nombreSpan.innerText.split('⭐')[0].split('👑')[0].trim() : 'Usuario';
                    const checked = div.classList.contains('ring-green-500/40');
                    return `<label class="flex items-center gap-3 p-2 rounded-lg cursor-pointer"><input type="checkbox" data-id="${id}" class="asistencia-checkbox" ${checked ? 'checked' : ''}><span class="text-sm">${nombre}</span></label>`;
                }).join('');
            }
        });
        
        guardarManual.addEventListener('click', async () => {
            const checkboxes = document.querySelectorAll('#manualChecklist .asistencia-checkbox');
            const asistencias = {};
            checkboxes.forEach(cb => asistencias[cb.dataset.id] = cb.checked ? 1 : 0);
            const formData = new FormData();
            formData.append('id_actividad', actividadId);
            formData.append('asistencias', JSON.stringify(asistencias));
            try {
                showLoader();
                const resp = await fetch(`${BASE_URL}?c=gestionActividad&a=pasarListaManual`, { method: 'POST', body: formData });
                const json = await resp.json();
                if (json.success) { showToast('Lista guardada'); location.reload(); }
                else showToast(json.message, true);
            } catch (e) { showToast('Error', true); }
            finally { hideLoader(); }
        });
        
        document.getElementById('btnMostrarAmigos').addEventListener('click', () => {
            document.getElementById('amigosPanel').classList.toggle('hidden');
        });
        
        // Descargar PDF con html2pdf
        document.getElementById('descargarLista').addEventListener('click', () => {
            if (!actividadData || Object.keys(actividadData).length === 0) {
                showToast('No hay datos para generar el PDF', true);
                return;
            }
            const fechaInicio = new Date(actividadData.fecha_inicio);
            const orgNames = (organizadoresData && organizadoresData.length) ? organizadoresData.map(o => o.nombre_completo).join(', ') : 'Ninguno';
            
            let htmlContent = `
                <!DOCTYPE html>
                <html>
                <head><meta charset="UTF-8"><title>Lista de Asistencia - ${escapeHtml(actividadData.nombre)}</title>
                <style>
                    body { font-family: Helvetica, Arial, sans-serif; margin: 30px; }
                    h1 { color: #5a2af7; text-align: center; }
                    .info { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .presente { color: green; font-weight: bold; }
                    .ausente { color: red; font-weight: bold; }
                    .footer { margin-top: 30px; font-size: 12px; text-align: center; color: #777; }
                </style>
                </head>
                <body>
                    <h1>Lista de Asistencia</h1>
                    <div class="info">
                        <p><strong>Actividad:</strong> ${escapeHtml(actividadData.nombre)}</p>
                        <p><strong>Fecha:</strong> ${fechaInicio.toLocaleDateString()} ${fechaInicio.toLocaleTimeString()}</p>
                        <p><strong>Ubicación:</strong> ${escapeHtml(actividadData.direccion || 'No especificada')}</p>
                        <p><strong>Organizador(es):</strong> ${escapeHtml(orgNames)}</p>
                    </div>
                    <table>
                        <thead><tr><th>Participante</th><th>Rol</th><th>Asistencia</th></tr></thead>
                        <tbody>
            `;
            if (participantesData && participantesData.length) {
                participantesData.forEach(p => {
                    htmlContent += `
                        <tr>
                            <td>${escapeHtml(p.nombre_completo)}</td>
                            <td>${p.rol === 'creador' ? 'Creador' : (p.rol === 'organizador' ? 'Organizador' : 'Miembro')}</td>
                            <td class="${p.asistio ? 'presente' : 'ausente'}">${p.asistio ? '✓ Presente' : '✗ No asistió'}</td>
                        </tr>
                    `;
                });
            } else {
                htmlContent += '<tr><td colspan="3">No hay participantes registrados</td></tr>';
            }
            htmlContent += `
                        </tbody>
                    </table>
                    <div class="footer">Generado el ${new Date().toLocaleString()}</div>
                </body>
                </html>
            `;
            
            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }
            
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5],
                filename: `Lista_asistencia_${actividadData.nombre.replace(/\s/g, '_')}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, letterRendering: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            const element = document.createElement('div');
            element.innerHTML = htmlContent;
            document.body.appendChild(element);
            html2pdf().set(opt).from(element).save().then(() => {
                document.body.removeChild(element);
            }).catch(err => {
                showToast('Error al generar PDF', true);
                console.error(err);
                document.body.removeChild(element);
            });
        });
		
        
        // Mapa Leaflet
        let map, marker;
        function getCustomIcon() {
            const svgString = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="#5a2af7"><path d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z"/></svg>`;
            return L.divIcon({ html: svgString, iconSize: [32, 32], className: 'custom-marker-icon', popupAnchor: [0, -16] });
        }
        function inicializarMapa(lat, lng, dir) {
            if (map) map.remove();
            map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>' }).addTo(map);
            marker = L.marker([lat, lng], { icon: getCustomIcon() }).addTo(map);
            marker.bindPopup(`<b>${dir || 'Ubicación'}</b>`).openPopup();
        }
        function obtenerDireccionConCache(lat, lng) {
            return new Promise(async (resolve, reject) => {
                const cacheKey = `direccion_${lat}_${lng}`;
                const cache = localStorage.getItem(cacheKey);
                if (cache) return resolve(JSON.parse(cache));
                const dirBD = document.getElementById('direccionTexto').innerText;
                if (dirBD && dirBD !== 'Obteniendo dirección...') {
                    const data = { display_name: dirBD };
                    localStorage.setItem(cacheKey, JSON.stringify(data));
                    return resolve(data);
                }
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                    const data = await response.json();
                    if (data && data.display_name) {
                        localStorage.setItem(cacheKey, JSON.stringify(data));
                        resolve(data);
                    } else reject('No se pudo obtener la dirección');
                } catch (error) { reject(error); }
            });
        }
        const lat = <?= $datos['actividad']['latitud'] ?>;
        const lng = <?= $datos['actividad']['longitud'] ?>;
        let dirActual = '<?= addslashes(htmlspecialchars($datos['actividad']['direccion'] ?? '')) ?>';
        if (dirActual) {
            document.getElementById('direccionTexto').innerText = dirActual;
            inicializarMapa(lat, lng, dirActual);
        } else {
            document.getElementById('direccionTexto').innerText = 'Obteniendo dirección...';
            obtenerDireccionConCache(lat, lng).then(data => {
                dirActual = data.display_name;
                document.getElementById('direccionTexto').innerText = dirActual;
                inicializarMapa(lat, lng, dirActual);
            }).catch(err => {
                console.error(err);
                document.getElementById('direccionTexto').innerText = 'Dirección no disponible';
                inicializarMapa(lat, lng, 'Ubicación aproximada');
            });
        }
        window.addEventListener('resize', () => { if (map) setTimeout(() => map.invalidateSize(), 100); });
        
        document.addEventListener('DOMContentLoaded', bindEvents);
    </script>
</body>
</html>
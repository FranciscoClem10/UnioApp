<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
// Variables: $actividad, $tipos, $solicitudes, $participantes, $restricciones, $user_lat, $user_lng
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<div class="overflow-y-auto flex-1 w-full px-4 md:px-8 pb-24">
    <div class="max-w-4xl mx-auto pt-6">
        <?php if (isset($_SESSION['error_edicion'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-error-container/20 border-l-4 border-error text-on-error-container text-sm font-medium">
                <?= htmlspecialchars($_SESSION['error_edicion']) ?>
                <?php unset($_SESSION['error_edicion']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['exito_edicion'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-100 border-l-4 border-green-500 text-green-800 text-sm font-medium">
                <?= htmlspecialchars($_SESSION['exito_edicion']) ?>
                <?php unset($_SESSION['exito_edicion']); ?>
            </div>
        <?php endif; ?>

        <div class="mb-8 text-center md:text-left">
            <h1 class="text-[3.5rem] font-extrabold tracking-tight text-on-surface leading-tight mb-4">
                Editar
                <span class="text-primary">Actividad</span>
            </h1>
            <p class="text-on-surface-variant max-w-xl"><?= htmlspecialchars($actividad['nombre']) ?></p>
            <a href="<?= BASE_URL ?>?c=actividad&a=edicion" class="inline-flex items-center gap-1 text-primary text-sm font-medium mt-2 hover:underline">
                ← Volver a mis actividades
            </a>
        </div>

        <?php if ($restricciones['bloquear_todo']): ?>
            <div class="bg-error-container/20 border-l-4 border-error p-6 rounded-xl text-error-container">
                Esta actividad está <?= htmlspecialchars($actividad['estado']) ?> y no se puede editar.
            </div>
        <?php else: ?>
            <form id="formEditarActividad" action="<?= BASE_URL ?>?c=actividad&a=actualizar" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-8">
                <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                <input type="hidden" name="confirmar_cancelacion" id="confirmar_cancelacion" value="0">
                <input type="hidden" name="eliminar_imagen" id="eliminarImagen" value="0">

                <!-- Imagen con opción de cambiar/eliminar (igual que en crear) -->
                <section class="bg-white p-6 md:p-8 rounded-xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] space-y-6">
                    <div class="space-y-4">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Imagen de la actividad</label>
                        <div id="imageContainer" class="relative">
                            <?php if (!empty($actividad['foto_base64'])): ?>
                                <div id="existingImageWrapper" class="relative aspect-video w-full rounded-xl overflow-hidden bg-surface-container-low">
                                    <img src="<?= $actividad['foto_base64'] ?>" class="w-full h-full object-cover">
                                    <div class="absolute top-2 right-2 flex gap-1">
                                        <button type="button" id="btnCambiarImagen" class="bg-black/60 text-white p-1 rounded-full hover:bg-black/80 transition" title="Cambiar imagen">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <button type="button" id="btnEliminarImagen" class="bg-black/60 text-white p-1 rounded-full hover:bg-black/80 transition" title="Eliminar imagen">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp" id="fotoInput" class="hidden">
                            <?php else: ?>
                                <div class="w-full">
                                    <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp" id="fotoInput" class="hidden">
                                    <label for="fotoInput" id="uploadLabel" class="aspect-video w-full bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center cursor-pointer hover:border-primary/40 transition-all group">
                                        <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-primary transition-colors">add_photo_alternate</span>
                                        <p class="mt-2 text-sm text-on-surface-variant font-medium">Subir imagen (JPG, PNG, WEBP, máx. 5MB)</p>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="previewNewImage" class="mt-2 hidden"></div>
                    </div>
                </section>

                <!-- Nombre + Tipo -->
                <section class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Nombre de la actividad <span class="text-error">*</span></label>
                        <input class="w-full h-14 px-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-on-surface font-body text-lg transition-all" 
                               type="text" name="nombre" required maxlength="100"
                               value="<?= htmlspecialchars($actividad['nombre']) ?>"
                               <?= ($restricciones['bloquear_nombre_desc'] ?? false) ? 'readonly disabled' : '' ?>>
                    </div>
                    <div class="space-y-4">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Clasificación <span class="text-error">*</span></label>
                        <div class="flex flex-wrap gap-2" id="tiposContainer">
                            <?php foreach ($tipos as $tipo): ?>
                                <button type="button" data-id="<?= $tipo['id_tipo'] ?>" 
                                    class="tipo-btn px-5 py-2.5 rounded-full text-sm font-medium transition-all 
                                    <?= ($tipo['id_tipo'] == $actividad['id_tipo']) 
                                        ? 'bg-primary text-on-primary shadow-sm' 
                                        : 'bg-surface-container-highest text-on-surface-variant hover:bg-surface-variant' ?>"
                                    <?= ($actividad['estado'] != 'pendiente') ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($tipo['nombre_tipo']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="id_tipo" id="id_tipo" value="<?= $actividad['id_tipo'] ?>" required>
                    </div>
                </section>

                <!-- Límites y edades -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Límites de Participantes</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-on-surface-variant font-medium">Mínimo</span>
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                       type="number" name="limite_participantes_min" min="1"
                                       value="<?= $actividad['limite_participantes_min'] ?>"
                                       <?= ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0 && $actividad['estado'] != 'pendiente') ? 'readonly' : '' ?>>
                                <?php if ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0): ?>
                                    <p class="text-xs text-error mt-1">No se puede reducir por debajo de <?= $restricciones['participantes_actuales'] ?> confirmados.</p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="text-xs text-on-surface-variant font-medium">Máximo</span>
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                       type="number" name="limite_participantes_max" min="1"
                                       value="<?= $actividad['limite_participantes_max'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Rango de Edad</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-on-surface-variant font-medium">Edad mínima</span>
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                       type="number" name="edad_minima" min="0" max="99"
                                       value="<?= $actividad['edad_minima'] ?>">
                            </div>
                            <div>
                                <span class="text-xs text-on-surface-variant font-medium">Edad máxima</span>
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                       type="number" name="edad_maxima" min="0" max="99"
                                       value="<?= $actividad['edad_maxima'] ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Inicio <span class="text-error">*</span></label>
                        <input type="datetime-local" name="fecha_inicio" required 
                               class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20"
                               value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_inicio'])) ?>"
                               <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'readonly' : '' ?>>
                        <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']): ?>
                            <p class="text-xs text-error">No puedes modificar la fecha (faltan menos de 48h y hay miembros).</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Fin <span class="text-error">*</span></label>
                        <input type="datetime-local" name="fecha_fin" required 
                               class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20"
                               value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_fin'])) ?>">
                    </div>
                </div>

                <!-- Mapa y dirección (modo auto/manual, sin geocodificación automática al inicio) -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Ubicación <span class="text-error">*</span></label>
                    <div id="map" class="h-64 w-full rounded-xl overflow-hidden border border-outline-variant/30 z-10"></div>
                    <div class="flex justify-end">
                        <button type="button" id="btnMiUbicacion" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-medium hover:bg-primary/20 transition-all">
                            <span class="material-symbols-outlined text-base">my_location</span> Usar mi ubicación
                        </button>
                    </div>

                    <!-- Selector modo dirección -->
                    <div class="mt-4 flex gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="modo_direccion" value="auto" checked class="radio-auto"> 
                            <span>Automática (desde el mapa)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="modo_direccion" value="manual" class="radio-manual"> 
                            <span>Manual (escribir dirección)</span>
                        </label>
                    </div>

                    <!-- Campo automático (solo lectura) -->
                    <div id="direccionAutoContainer" class="block">
                        <div id="direccionAuto" class="w-full p-4 bg-surface-container-low rounded-xl text-on-surface-variant text-sm">
                            <?php 
                            if (!empty($actividad['direccion'])) {
                                echo htmlspecialchars($actividad['direccion']);
                            } else {
                                echo 'Mueve el marcador para obtener la dirección';
                            }
                            ?>
                        </div>
                        <input type="hidden" name="direccion" id="direccionHidden" value="<?= htmlspecialchars($actividad['direccion'] ?? '') ?>">
                    </div>

                    <!-- Campo manual -->
                    <div id="direccionManualContainer" class="hidden">
                        <input type="text" id="direccionManual" name="direccion_manual" class="w-full h-14 px-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-on-surface font-body text-sm" 
                               placeholder="Escribe la dirección completa..." value="<?= htmlspecialchars($actividad['direccion'] ?? '') ?>">
                    </div>

                    <input type="hidden" name="latitud" id="latInput" value="<?= $actividad['latitud'] ?>">
                    <input type="hidden" name="longitud" id="lngInput" value="<?= $actividad['longitud'] ?>">
                    <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
                        <p class="text-xs text-error mt-1">Ubicación bloqueada porque hay miembros y faltan menos de 24h para el inicio.</p>
                    <?php endif; ?>
                </div>

                <!-- Descripción y requisitos -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Descripción</label>
                        <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                                  name="descripcion" rows="4"><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
                        <?php if ($restricciones['bloquear_nombre_desc'] ?? false): ?>
                            <p class="text-xs text-error">No puedes modificar la descripción (faltan menos de 24h).</p>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Requisitos (opcional)</label>
                        <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                                  name="requisitos" rows="3"><?= htmlspecialchars($actividad['requisitos']) ?></textarea>
                        <?php if ($restricciones['bloquear_requisitos'] ?? false): ?>
                            <p class="text-xs text-error">Los requisitos solo se pueden modificar una vez y con al menos 24h de anticipación.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Privacidad -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Privacidad</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 radio-card">
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="publica" class="hidden peer" <?= $actividad['privacidad'] == 'publica' ? 'checked' : '' ?> <?= $actividad['estado'] != 'pendiente' ? 'disabled' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">public</span>
                                <div><p class="font-bold text-on-surface text-sm">Pública</p><p class="text-xs text-on-surface-variant">Cualquiera puede unirse</p></div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="por_aprobacion" class="hidden peer" <?= $actividad['privacidad'] == 'por_aprobacion' ? 'checked' : '' ?> <?= $actividad['estado'] != 'pendiente' ? 'disabled' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">how_to_reg</span>
                                <div><p class="font-bold text-on-surface text-sm">Por aprobación</p><p class="text-xs text-on-surface-variant">Organizador acepta</p></div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="privada" class="hidden peer" <?= $actividad['privacidad'] == 'privada' ? 'checked' : '' ?> <?= $actividad['estado'] != 'pendiente' ? 'disabled' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">lock</span>
                                <div><p class="font-bold text-on-surface text-sm">Privada</p><p class="text-xs text-on-surface-variant">Solo invitados</p></div>
                            </div>
                        </label>
                    </div>
                    <?php if ($actividad['estado'] != 'pendiente'): ?>
                        <p class="text-xs text-error">No puedes cambiar la privacidad después de que la actividad ha iniciado.</p>
                    <?php endif; ?>
                </div>

                <!-- Solicitudes pendientes -->
                <?php if ($actividad['privacidad'] == 'por_aprobacion' && !empty($solicitudes)): ?>
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <h3 class="font-bold text-lg text-on-surface">Solicitudes de participación</h3>
                    <div class="space-y-4">
                        <?php foreach ($solicitudes as $sol): ?>
                            <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl" data-user="<?= $sol['id_usuario'] ?>">
                                <div class="flex items-center gap-3">
                                    <?php if ($sol['foto_base64']): ?>
                                        <img src="<?= $sol['foto_base64'] ?>" class="w-10 h-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary"><?= strtoupper(substr($sol['nombre_completo'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-medium text-on-surface"><?= htmlspecialchars($sol['nombre_completo']) ?></p>
                                        <p class="text-xs text-on-surface-variant">Edad: <?= $sol['edad'] ?> años</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" class="btn-aceptar-solicitud bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm" data-userid="<?= $sol['id_usuario'] ?>">Aceptar</button>
                                    <button type="button" class="btn-rechazar-solicitud bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm" data-userid="<?= $sol['id_usuario'] ?>">Rechazar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Participantes actuales -->
                <?php if (!empty($participantes)): ?>
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <h3 class="font-bold text-lg text-on-surface">Participantes actuales (<?= count($participantes) ?>)</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($participantes as $part): ?>
                            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm text-primary">person</span>
                                <span class="text-sm"><?= htmlspecialchars($part['nombre_completo']) ?></span>
                                <span class="text-xs text-on-surface-variant">(<?= $part['rol'] ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botones -->
                <div class="flex flex-col md:flex-row items-center justify-end gap-4 mt-4">
                    <a href="<?= BASE_URL ?>?c=actividad&a=edicion" class="w-full md:w-auto px-8 py-4 text-primary font-bold hover:bg-surface-container-low rounded-xl transition-all text-center">Cancelar</a>
                    <button type="submit" id="btnGuardar" class="w-full md:w-auto px-10 py-4 bg-gradient-to-br from-primary to-primary-dim text-on-primary font-bold text-lg rounded-xl shadow-[0_8px_24px_rgba(98,54,255,0.3)] hover:scale-[1.02] active:scale-95 transition-all">Guardar cambios</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php include 'includes/bottom-nav.php'; ?>

<script>
    // Variables PHP a JS
    const baseUrl = '<?= BASE_URL ?>';
    const hasDbLocation = <?= json_encode(!is_null($user_lat ?? null) && !is_null($user_lng ?? null)) ?>;
    const dbLat = <?= json_encode($user_lat ?? null) ?>;
    const dbLng = <?= json_encode($user_lng ?? null) ?>;
    const actividadLat = <?= (float)($actividad['latitud'] ?? 0) ?>;
    const actividadLng = <?= (float)($actividad['longitud'] ?? 0) ?>;
    const actividadDireccion = <?= json_encode($actividad['direccion'] ?? '') ?>;

    document.addEventListener('DOMContentLoaded', function() {
        // --- Tipos de actividad ---
        const tipoBtns = document.querySelectorAll('.tipo-btn');
        const tipoInput = document.getElementById('id_tipo');
        tipoBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.hasAttribute('disabled')) return;
                const nuevoTipoId = this.getAttribute('data-id');
                tipoInput.value = nuevoTipoId;
                tipoBtns.forEach(b => {
                    b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                    b.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
                });
                this.classList.remove('bg-surface-container-highest', 'text-on-surface-variant');
                this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            });
        });

        // --- Manejo de imagen (cambiar/eliminar) ---
        const imageContainer = document.getElementById('imageContainer');
        const fotoInput = document.getElementById('fotoInput');
        const btnCambiar = document.getElementById('btnCambiarImagen');
        const btnEliminar = document.getElementById('btnEliminarImagen');
        const eliminarImagenInput = document.getElementById('eliminarImagen');
        const previewDiv = document.getElementById('previewNewImage');
        const existingWrapper = document.getElementById('existingImageWrapper');
        const uploadLabel = document.getElementById('uploadLabel');

        if (btnCambiar) {
            btnCambiar.addEventListener('click', function() {
                fotoInput.click();
            });
        }

        if (btnEliminar) {
            btnEliminar.addEventListener('click', function() {
                if (confirm('¿Eliminar la imagen actual? Se perderá permanentemente.')) {
                    eliminarImagenInput.value = '1';
                    if (existingWrapper) existingWrapper.style.display = 'none';
                    // Mostrar opción para subir nueva igual que en crear
                    const uploadHtml = `<div class="w-full">
                        <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp" id="fotoInputNew" class="hidden">
                        <label for="fotoInputNew" id="uploadLabelNew" class="aspect-video w-full bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center cursor-pointer hover:border-primary/40 transition-all group">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-primary transition-colors">add_photo_alternate</span>
                            <p class="mt-2 text-sm text-on-surface-variant font-medium">Subir nueva imagen (JPG, PNG, WEBP, máx. 5MB)</p>
                        </label>
                    </div>`;
                    imageContainer.innerHTML = uploadHtml;
                    const newFotoInput = document.getElementById('fotoInputNew');
                    const newUploadLabel = document.getElementById('uploadLabelNew');
                    if (newFotoInput) {
                        newFotoInput.addEventListener('change', function(e) {
                            if (e.target.files.length > 0) {
                                const file = e.target.files[0];
                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    previewDiv.innerHTML = `<div class="aspect-video w-full rounded-xl overflow-hidden"><img src="${ev.target.result}" class="w-full h-full object-cover"></div>`;
                                    previewDiv.classList.remove('hidden');
                                    newUploadLabel.style.display = 'none';
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    }
                }
            });
        }

        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        previewDiv.innerHTML = `<div class="aspect-video w-full rounded-xl overflow-hidden"><img src="${ev.target.result}" class="w-full h-full object-cover"></div>`;
                        previewDiv.classList.remove('hidden');
                        if (existingWrapper) existingWrapper.style.display = 'none';
                        if (uploadLabel) uploadLabel.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // --- Mapa y dirección (sin geocodificación automática al inicio) ---
        let map, marker;
        let isFetching = false;
        let loadingOverlay = null;
        const direccionCache = new Map();

        function mostrarLoading(mostrar) {
            if (!loadingOverlay) {
                loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden';
                loadingOverlay.innerHTML = `
                    <div class="bg-white rounded-xl p-6 flex flex-col items-center gap-4 shadow-2xl">
                        <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-on-surface font-medium">Obteniendo dirección, espere...</p>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
            }
            if (mostrar) {
                loadingOverlay.classList.remove('hidden');
                if (marker && marker.dragging) marker.dragging.disable();
                if (map) map.dragging.disable();
            } else {
                loadingOverlay.classList.add('hidden');
                if (marker && marker.dragging) marker.dragging.enable();
                if (map) map.dragging.enable();
            }
        }

        async function obtenerComponentesDireccion(lat, lng) {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`;
                const resp = await fetch(url, { headers: { 'User-Agent': 'MiAppActividades/1.0' } });
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                const addr = data.address || {};
                return {
                    road: addr.road || addr.pedestrian || '',
                    house_number: addr.house_number || '',
                    suburb: addr.suburb || addr.neighbourhood || addr.village || '',
                    city: addr.city || addr.town || addr.municipality || '',
                    state: addr.state || '',
                    postcode: addr.postcode || '',
                    country: addr.country || ''
                };
            } catch (err) {
                console.warn(err);
                return null;
            }
        }

        async function obtenerCallesCercanasOverpass(lat, lng, radioMetros = 45) {
            const query = `[out:json];(way(around:${radioMetros},${lat},${lng})["highway"];);out body;`;
            const url = `https://overpass-api.de/api/interpreter?data=${encodeURIComponent(query)}`;
            try {
                const resp = await fetch(url, { headers: { 'User-Agent': 'MiAppActividades/1.0' } });
                if (!resp.ok) throw new Error(`Overpass HTTP ${resp.status}`);
                const data = await resp.json();
                const nombres = data.elements.map(el => el.tags?.name).filter(n => n && n.trim() !== '');
                const unicos = [...new Set(nombres)];
                return unicos.slice(0, 2);
            } catch (err) {
                console.warn(err);
                return [];
            }
        }

        function construirDireccionFormateada(streets, componentes) {
            if (!componentes) return 'No se pudo obtener la dirección';
            let parteCalles = '';
            if (streets.length >= 2) {
                parteCalles = `${streets[0]} y ${streets[1]}`;
            } else if (streets.length === 1) {
                parteCalles = streets[0];
            } else {
                parteCalles = componentes.road || '';
            }
            if (!parteCalles) return 'Ubicación sin calle';
            const partesRestantes = [componentes.postcode, componentes.city, componentes.state, componentes.country].filter(Boolean);
            return [parteCalles, ...partesRestantes].join(', ');
        }

        async function actualizarDireccionDesdeCoordenadas(lat, lng) {
            const direccionAutoDiv = document.getElementById('direccionAuto');
            const direccionHidden = document.getElementById('direccionHidden');
            if (!direccionAutoDiv) return;
            const clave = `${lat},${lng}`;
            if (direccionCache.has(clave)) {
                const dir = direccionCache.get(clave);
                direccionAutoDiv.textContent = dir;
                if (direccionHidden) direccionHidden.value = dir;
                return;
            }
            isFetching = true;
            mostrarLoading(true);
            direccionAutoDiv.innerHTML = 'Obteniendo dirección...';
            try {
                const [componentes, calles] = await Promise.all([
                    obtenerComponentesDireccion(lat, lng),
                    obtenerCallesCercanasOverpass(lat, lng)
                ]);
                const direccion = construirDireccionFormateada(calles, componentes);
                direccionAutoDiv.textContent = direccion;
                if (direccionHidden) direccionHidden.value = direccion;
                direccionCache.set(clave, direccion);
                if (direccionCache.size > 100) {
                    const firstKey = direccionCache.keys().next().value;
                    direccionCache.delete(firstKey);
                }
            } catch (err) {
                console.error(err);
                direccionAutoDiv.innerHTML = 'Error al obtener dirección.';
            } finally {
                isFetching = false;
                mostrarLoading(false);
            }
        }

        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
        }

        function iniciarMapa(lat, lng) {
            if (map) return;
            map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CartoDB'
            }).addTo(map);
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                      </svg>`,
                iconSize: [28,28]
            });
            marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);
            actualizarCoordenadas(lat, lng);

            // Eventos que actualizan la dirección solo cuando el usuario interactúa
            map.on('click', async function(e) {
                if (isFetching) { alert('Espera a que termine la búsqueda de dirección.'); return; }
                marker.setLatLng(e.latlng);
                actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
                await actualizarDireccionDesdeCoordenadas(e.latlng.lat, e.latlng.lng);
            });
            marker.on('dragend', async function() {
                if (isFetching) { alert('Espera a que termine la búsqueda de dirección.'); return; }
                const pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
                await actualizarDireccionDesdeCoordenadas(pos.lat, pos.lng);
            });

            // NO llamamos a actualizarDireccionDesdeCoordenadas al inicio
            // Solo mostramos la dirección existente de la BD (ya está en el div)
        }

        // Iniciar mapa con coordenadas de la actividad o del usuario
        if (actividadLat && actividadLng) {
            iniciarMapa(actividadLat, actividadLng);
        } else if (hasDbLocation && dbLat && dbLng) {
            iniciarMapa(dbLat, dbLng);
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => iniciarMapa(pos.coords.latitude, pos.coords.longitude),
                () => iniciarMapa(18.4500, -96.3500)
            );
        } else {
            iniciarMapa(18.4500, -96.3500);
        }

        // Botón mi ubicación
        document.getElementById('btnMiUbicacion')?.addEventListener('click', function() {
            if (!navigator.geolocation) { alert('Geolocalización no soportada'); return; }
            if (isFetching) { alert('Espera a que termine la operación actual.'); return; }
            navigator.geolocation.getCurrentPosition(async function(pos) {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                actualizarCoordenadas(lat, lng);
                await actualizarDireccionDesdeCoordenadas(lat, lng);
            }, err => alert('Error al obtener tu ubicación: ' + err.message));
        });

        // Modo dirección auto/manual
        const radioAuto = document.querySelector('input[name="modo_direccion"][value="auto"]');
        const radioManual = document.querySelector('input[name="modo_direccion"][value="manual"]');
        const autoContainer = document.getElementById('direccionAutoContainer');
        const manualContainer = document.getElementById('direccionManualContainer');
        const direccionManualInput = document.getElementById('direccionManual');
        const direccionHidden = document.getElementById('direccionHidden');

        function setModoDireccion() {
            if (radioAuto.checked) {
                autoContainer.classList.remove('hidden');
                manualContainer.classList.add('hidden');
                if (direccionManualInput.value.trim() !== '') {
                    direccionHidden.value = direccionManualInput.value.trim();
                }
            } else {
                autoContainer.classList.add('hidden');
                manualContainer.classList.remove('hidden');
                if (!direccionManualInput.value.trim() && direccionHidden.value) {
                    direccionManualInput.value = direccionHidden.value;
                }
            }
        }

        radioAuto?.addEventListener('change', setModoDireccion);
        radioManual?.addEventListener('change', setModoDireccion);
        setModoDireccion();

        // --- Confirmación cambios críticos (cancelar solicitudes) ---
        const form = document.getElementById('formEditarActividad');
        const confirmacionInput = document.getElementById('confirmar_cancelacion');
        let camposCriticosModificados = false;
        const camposOriginales = {
            fecha_inicio: '<?= $actividad['fecha_inicio'] ?>',
            latitud: '<?= $actividad['latitud'] ?>',
            longitud: '<?= $actividad['longitud'] ?>',
            privacidad: '<?= $actividad['privacidad'] ?>',
            limite_min: '<?= $actividad['limite_participantes_min'] ?>',
            limite_max: '<?= $actividad['limite_participantes_max'] ?>'
        };

        function verificarCambiosCriticos() {
            const nuevaFecha = document.querySelector('input[name="fecha_inicio"]').value;
            const nuevaLat = document.getElementById('latInput').value;
            const nuevaLng = document.getElementById('lngInput').value;
            const nuevaPriv = document.querySelector('input[name="privacidad"]:checked')?.value;
            const nuevoMin = document.querySelector('input[name="limite_participantes_min"]').value;
            const nuevoMax = document.querySelector('input[name="limite_participantes_max"]').value;
            camposCriticosModificados = !(nuevaFecha === camposOriginales.fecha_inicio &&
                nuevaLat === camposOriginales.latitud &&
                nuevaLng === camposOriginales.longitud &&
                nuevaPriv === camposOriginales.privacidad &&
                nuevoMin === camposOriginales.limite_min &&
                nuevoMax === camposOriginales.limite_max);
        }

        form?.addEventListener('submit', function(e) {
            verificarCambiosCriticos();
            if (camposCriticosModificados && confirmacionInput.value !== '1') {
                e.preventDefault();
                if (confirm("Se modificaron campos que cancelarán todas las solicitudes pendientes de participación. ¿Deseas continuar?")) {
                    confirmacionInput.value = '1';
                    form.submit();
                }
            }
        });

        // --- Aceptar/Rechazar solicitudes (AJAX) ---
        const btnAceptar = document.querySelectorAll('.btn-aceptar-solicitud');
        const btnRechazar = document.querySelectorAll('.btn-rechazar-solicitud');

        function responderSolicitud(userId, accion, elemento) {
            const idActividad = document.querySelector('input[name="id_actividad"]').value;
            fetch(baseUrl + '?c=actividad&a=' + (accion === 'aceptar' ? 'aceptarSolicitud' : 'rechazarSolicitud'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_actividad=${idActividad}&id_usuario=${userId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    elemento.closest('[data-user]').remove();
                    alert('Solicitud ' + (accion === 'aceptar' ? 'aceptada' : 'rechazada'));
                } else {
                    alert('Error al procesar la solicitud');
                }
            })
            .catch(err => console.error(err));
        }

        btnAceptar.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-userid');
                responderSolicitud(userId, 'aceptar', this);
            });
        });
        btnRechazar.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-userid');
                responderSolicitud(userId, 'rechazar', this);
            });
        });
    });
</script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
// Variables disponibles desde el controlador:
// $actividad, $tipos, $organizadores, $solicitudes, $participantes, $invitaciones, $restricciones
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<div class="overflow-y-auto flex-1 w-full px-4 md:px-8 pb-24">
    <div class="max-w-4xl mx-auto pt-6">
        <!-- Mensajes de éxito/error -->
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
            <!-- Formulario de edición de datos básicos -->
            <form action="<?= BASE_URL ?>?c=actividad&a=actualizar" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-8">
                <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">

                <!-- Imagen -->
                <section class="bg-white p-6 md:p-8 rounded-xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] space-y-6">
                    <div class="space-y-4">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Imagen de la actividad</label>
                        <div class="relative w-full">
                            <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp" id="fotoInput" class="hidden">
                            <label for="fotoInput" class="aspect-video w-full bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center cursor-pointer hover:border-primary/40 transition-all group">
                                <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-primary transition-colors">add_photo_alternate</span>
                                <p class="mt-2 text-sm text-on-surface-variant font-medium">Cambiar imagen (JPG, PNG, WEBP, máx. 5MB)</p>
                            </label>
                            <div id="previewImage" class="mt-4 relative rounded-xl overflow-hidden">
                                <?php if (!empty($actividad['foto_actividad'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($actividad['foto_actividad']) ?>" class="w-full h-auto rounded-xl max-h-48 object-cover">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Nombre + Tipo -->
                <section class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Nombre de la actividad <span class="text-error">*</span></label>
                        <input class="w-full h-14 px-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-on-surface font-body text-lg transition-all" 
                               type="text" name="nombre" required maxlength="100"
                               value="<?= htmlspecialchars($actividad['nombre']) ?>">
                    </div>

                    <div class="space-y-4">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Clasificación <span class="text-error">*</span></label>
                        <div class="flex flex-wrap gap-2" id="tiposContainer">
                            <?php foreach ($tipos as $tipo): ?>
                                <button type="button" data-id="<?= $tipo['id_tipo'] ?>" 
                                    class="tipo-btn px-5 py-2.5 rounded-full text-sm font-medium transition-all 
                                    <?= ($tipo['id_tipo'] == $actividad['id_tipo']) 
                                        ? 'bg-primary text-on-primary shadow-sm' 
                                        : 'bg-surface-container-highest text-on-surface-variant hover:bg-surface-variant' ?>">
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
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 <?= ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0) ? 'bloqueado bg-gray-100 text-gray-500' : '' ?>" 
                                       type="number" name="limite_participantes_min" min="1"
                                       value="<?= $actividad['limite_participantes_min'] ?>"
                                       <?= ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0) ? 'readonly' : '' ?>>
                                <?php if ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0): ?>
                                    <p class="text-xs text-error mt-1">No se puede reducir por debajo de <?= $restricciones['participantes_actuales'] ?> confirmados.</p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="text-xs text-on-surface-variant font-medium">Máximo</span>
                                <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                       type="number" name="limite_participantes_max" min="1"
                                       value="<?= $actividad['limite_participantes_max'] ?>"
                                       <?= ($restricciones['hay_miembros'] && $restricciones['solo_aumentar_max']) ? 'min="'.$actividad['limite_participantes_max'].'"' : '' ?>>
                                <?php if ($restricciones['hay_miembros'] && $restricciones['solo_aumentar_max']): ?>
                                    <p class="text-xs text-error mt-1">Solo se puede aumentar (ya se alcanzó el límite).</p>
                                <?php endif; ?>
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
                               class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'bg-gray-100 text-gray-500' : '' ?>"
                               value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_inicio'])) ?>"
                               <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'disabled' : '' ?>>
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Fin <span class="text-error">*</span></label>
                        <input type="datetime-local" name="fecha_fin" required 
                               class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'bg-gray-100 text-gray-500' : '' ?>"
                               value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_fin'])) ?>"
                               <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'disabled' : '' ?>>
                    </div>
                </div>

                <!-- Mapa y ubicación -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Ubicación <span class="text-error">*</span></label>
                    <div id="map" class="h-64 w-full rounded-xl overflow-hidden border border-outline-variant/30 z-10"></div>
                    <div class="flex justify-end">
                        <button type="button" id="btnMiUbicacion" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-medium hover:bg-primary/20 transition-all">
                            <span class="material-symbols-outlined text-base">my_location</span> Usar mi ubicación
                        </button>
                    </div>
                    <div id="direccionMostrada" class="text-sm text-on-surface-variant bg-surface-container-low p-3 rounded-lg">Cargando dirección...</div>
                    <input type="hidden" name="latitud" id="latInput" value="<?= $actividad['latitud'] ?>">
                    <input type="hidden" name="longitud" id="lngInput" value="<?= $actividad['longitud'] ?>">
                    <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
                        <p class="text-xs text-error mt-1">Ubicación bloqueada porque hay miembros y la actividad empieza en menos de 6 horas.</p>
                    <?php endif; ?>
                </div>

                <!-- Descripción y requisitos -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Descripción</label>
                        <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                                  name="descripcion" rows="4"><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Requisitos (opcional)</label>
                        <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                                  name="requisitos" rows="3"><?= htmlspecialchars($actividad['requisitos']) ?></textarea>
                    </div>
                </div>

                <!-- Privacidad -->
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Privacidad</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 radio-card">
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="publica" class="hidden peer" <?= $actividad['privacidad'] == 'publica' ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">public</span>
                                <div><p class="font-bold text-on-surface text-sm">Pública</p><p class="text-xs text-on-surface-variant">Cualquiera puede unirse</p></div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="por_aprobacion" class="hidden peer" <?= $actividad['privacidad'] == 'por_aprobacion' ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">how_to_reg</span>
                                <div><p class="font-bold text-on-surface text-sm">Por aprobación</p><p class="text-xs text-on-surface-variant">Organizador acepta</p></div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="privacidad" value="privada" class="hidden peer" <?= $actividad['privacidad'] == 'privada' ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary">lock</span>
                                <div><p class="font-bold text-on-surface text-sm">Privada</p><p class="text-xs text-on-surface-variant">Solo invitados</p></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col md:flex-row items-center justify-end gap-4 mt-4">
                    <a href="<?= BASE_URL ?>?c=actividad&a=edicion" class="w-full md:w-auto px-8 py-4 text-primary font-bold hover:bg-surface-container-low rounded-xl transition-all text-center">Cancelar</a>
                    <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-br from-primary to-primary-dim text-on-primary font-bold text-lg rounded-xl shadow-[0_8px_24px_rgba(98,54,255,0.3)] hover:scale-[1.02] active:scale-95 transition-all">Guardar cambios</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts Leaflet y lógica propia -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php include 'includes/bottom-nav.php'; ?>
<?php include 'Scripts/editarS.php'; ?>

</body>
</html>
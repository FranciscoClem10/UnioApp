<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}

$form_data = $_SESSION['form_actividad'] ?? [];
unset($_SESSION['form_actividad']);

function getOld($field, $default = '') {
    global $form_data;
    return htmlspecialchars($form_data[$field] ?? $default);
}

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<div class="overflow-y-auto flex-1 w-full px-4 md:px-8 pb-24">
    <div class="max-w-4xl mx-auto pt-6">
        <!-- Mostrar errores si existen -->
        <?php if (isset($_SESSION['error_crear_actividad'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-error-container/20 border-l-4 border-error text-on-error-container text-sm font-medium">
                <?= htmlspecialchars($_SESSION['error_crear_actividad']) ?>
                <?php unset($_SESSION['error_crear_actividad']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning_imagen'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-amber-100 border-l-4 border-amber-500 text-amber-800 text-sm font-medium">
                <?= htmlspecialchars($_SESSION['warning_imagen']) ?>
                <?php unset($_SESSION['warning_imagen']); ?>
            </div>
        <?php endif; ?>

        <div class="mb-8 text-center md:text-left">
            <h1 class="text-[2.5rem] md:text-[3.5rem] font-extrabold tracking-tight leading-tight text-on-surface mb-2 font-headline">Crear actividad</h1>
            <p class="text-on-surface-variant max-w-xl">Diseña una experiencia única para tu comunidad.</p>
        </div>

        <form action="<?= BASE_URL ?>?c=actividad&a=guardar" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-8">
            <!-- Sección: Imagen -->
            <section class="bg-white p-6 md:p-8 rounded-xl shadow-[0_8px_32px_rgba(45,47,47,0.04)] space-y-6">
                <div class="space-y-4">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Imagen de la actividad</label>
                    <div class="relative w-full">
                        <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp" id="fotoInput" class="hidden">
                        <label for="fotoInput" class="aspect-video w-full bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center cursor-pointer hover:border-primary/40 transition-all group">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant group-hover:text-primary transition-colors">add_photo_alternate</span>
                            <p class="mt-2 text-sm text-on-surface-variant font-medium">Sube una imagen (JPG, PNG, WEBP, máx. 5MB)</p>
                        </label>
                        <div id="previewImage" class="hidden mt-4 relative rounded-xl overflow-hidden"></div>
                    </div>
                </div>
            </section>

            <!-- Sección: Nombre + Clasificación -->
            <section class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                <div class="space-y-2">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Nombre de la actividad <span class="text-error">*</span></label>
                    <input class="w-full h-14 px-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-on-surface font-body text-lg transition-all" 
                           placeholder="Ej: Clase magistral de acuarela moderna" 
                           type="text" name="nombre" required maxlength="100"
                           value="<?= getOld('nombre') ?>">
                </div>

                <div class="space-y-4">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Clasificación <span class="text-error">*</span></label>
                    <div class="flex flex-wrap gap-2" id="tiposContainer">
                        <?php if (!empty($tipos) && is_array($tipos)): ?>
                            <?php 
                            $selected_tipo = getOld('id_tipo', !empty($tipos) ? $tipos[0]['id_tipo'] : '');
                            foreach ($tipos as $tipo): ?>
                                <button type="button" data-id="<?= $tipo['id_tipo'] ?>" 
                                    class="tipo-btn px-5 py-2.5 rounded-full text-sm font-medium transition-all 
                                    <?= ($tipo['id_tipo'] == $selected_tipo) 
                                        ? 'bg-primary text-on-primary shadow-sm' 
                                        : 'bg-surface-container-highest text-on-surface-variant hover:bg-surface-variant' ?>">
                                    <?= htmlspecialchars($tipo['nombre_tipo']) ?>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-error text-sm">No hay tipos de actividad configurados</p>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="id_tipo" id="id_tipo" value="<?= getOld('id_tipo', !empty($tipos) ? $tipos[0]['id_tipo'] : '') ?>" required>
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
                                   value="<?= getOld('limite_participantes_min', 1) ?>">
                        </div>
                        <div>
                            <span class="text-xs text-on-surface-variant font-medium">Máximo (opcional)</span>
                            <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                   type="number" name="limite_participantes_max" min="1"
                                   value="<?= getOld('limite_participantes_max') ?>">
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
                                   value="<?= getOld('edad_minima', 0) ?>">
                        </div>
                        <div>
                            <span class="text-xs text-on-surface-variant font-medium">Edad máxima</span>
                            <input class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20" 
                                   type="number" name="edad_maxima" min="0" max="99"
                                   value="<?= getOld('edad_maxima', 99) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Inicio <span class="text-error">*</span></label>
                    <input type="datetime-local" name="fecha_inicio" required class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20"
                           value="<?= getOld('fecha_inicio') ?>">
                </div>
                <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Fin <span class="text-error">*</span></label>
                    <input type="datetime-local" name="fecha_fin" required class="w-full h-12 px-4 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20"
                           value="<?= getOld('fecha_fin') ?>">
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
                <div id="direccionMostrada" class="text-sm text-on-surface-variant bg-surface-container-low p-3 rounded-lg">No seleccionada</div>
                <input type="hidden" name="latitud" id="latInput" required value="<?= getOld('latitud') ?>">
                <input type="hidden" name="longitud" id="lngInput" required value="<?= getOld('longitud') ?>">
            </div>

            <!-- Descripción y requisitos -->
            <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-8">
                <div class="space-y-2">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Descripción</label>
                    <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                              name="descripcion" rows="4" placeholder="Describe qué haremos."><?= getOld('descripcion') ?></textarea>
                </div>
                <div class="space-y-2">
                    <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Requisitos (opcional)</label>
                    <textarea class="w-full p-5 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 resize-none" 
                              name="requisitos" rows="3" placeholder="Ej: Ropa cómoda, conocimientos básicos, etc."><?= getOld('requisitos') ?></textarea>
                </div>
            </div>

            <!-- Privacidad -->
            <div class="bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
                <label class="block font-bold text-sm uppercase tracking-widest text-on-surface-variant">Privacidad</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 radio-card">
                    <?php 
                    $privacidad = getOld('privacidad', 'publica');
                    ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="privacidad" value="publica" class="hidden peer" <?= $privacidad === 'publica' ? 'checked' : '' ?>>
                        <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                            <span class="material-symbols-outlined text-primary">public</span>
                            <div>
                                <p class="font-bold text-on-surface text-sm">Pública</p>
                                <p class="text-xs text-on-surface-variant">Cualquiera puede unirse</p>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="privacidad" value="por_aprobacion" class="hidden peer" <?= $privacidad === 'por_aprobacion' ? 'checked' : '' ?>>
                        <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                            <span class="material-symbols-outlined text-primary">how_to_reg</span>
                            <div>
                                <p class="font-bold text-on-surface text-sm">Por aprobación</p>
                                <p class="text-xs text-on-surface-variant">Organizador acepta</p>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="privacidad" value="privada" class="hidden peer" <?= $privacidad === 'privada' ? 'checked' : '' ?>>
                        <div class="p-4 rounded-xl border-2 border-transparent bg-surface-container-low peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-4">
                            <span class="material-symbols-outlined text-primary">lock</span>
                            <div>
                                <p class="font-bold text-on-surface text-sm">Privada</p>
                                <p class="text-xs text-on-surface-variant">Solo invitados</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col md:flex-row items-center justify-end gap-4 mt-4">
                <a href="<?= BASE_URL ?>?c=dashboard" class="w-full md:w-auto px-8 py-4 text-primary font-bold hover:bg-surface-container-low rounded-xl transition-all text-center">Cancelar</a>
                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-br from-primary to-primary-dim text-on-primary font-bold text-lg rounded-xl shadow-[0_8px_24px_rgba(98,54,255,0.3)] hover:scale-[1.02] active:scale-95 transition-all">Publicar actividad</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts Leaflet y demás -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php include 'Scripts/crearS.php'; ?>
<?php include 'includes/bottom-nav.php'; ?>
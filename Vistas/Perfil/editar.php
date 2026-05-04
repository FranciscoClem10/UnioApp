<?php
// Vistas/Perfil/editar.php
if (!isset($usuario)) { header('Location: ' . BASE_URL . '?c=perfil'); exit; }
?>

<?php include 'includes/header.php' ?>

<style>
    main {
        overflow-y: auto !important;
    }
    /* Corrección de bordes en modo oscuro */
    .dark .bg-white,
    .dark .border-surface-container {
        border-color: #2A2A2A !important;
    }
    .dark input,
    .dark select,
    .dark textarea {
        border-color: #44404F !important;
    }
    .dark .file\:bg-primary {
        background-color: #BB86FC !important;
    }
    .dark .file\:text-white {
        color: #000 !important;
    }
</style>

<?php include 'includes/top-nav.php' ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="pt-24 pb-32 px-4 md:px-6 max-w-4xl mx-auto space-y-8">

	<div class="mb-6">
        <h1 class="text-[3.5rem] font-extrabold tracking-tight text-on-surface leading-tight mb-4">
                Actualizar
                <span class="text-primary">Información</span>
            </h1>
        <p class="text-on-surface-variant text-base mt-1">Gestiona tú Información.</p>
    </div>

    <!-- Botón de regresar (flotante o integrado) -->
    <button onclick="history.back()" class="flex items-center gap-2 text-sm font-bold text-primary hover:underline mb-4">
        <span class="material-symbols-outlined">arrow_back</span> Volver
    </button>

    <form action="<?= BASE_URL ?>?c=perfil&a=actualizar" method="POST" enctype="multipart/form-data">
        <!-- Sección: Información Básica -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-extrabold text-on-surface tracking-tight">Información Básica</h2>
                <button type="button" class="text-primary font-bold text-sm flex items-center gap-1 hover:underline" onclick="this.closest('section').querySelectorAll('input,select,textarea').forEach(e => e.removeAttribute('readonly'));">
                    <span class="material-symbols-outlined text-lg">edit</span> Editar
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?= htmlspecialchars($usuario['apellido_paterno']) ?>" required
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?= htmlspecialchars($usuario['apellido_materno'] ?? '') ?>"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Género</label>
                    <select name="genero" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                        <option value="M" <?= $usuario['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= $usuario['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                        <option value="Otro" <?= $usuario['genero'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
                        <option value="Prefiero no decir" <?= $usuario['genero'] == 'Prefiero no decir' ? 'selected' : '' ?>>Prefiero no decir</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-on-surface-variant font-medium text-sm ml-1">Cumpleaños (mes y día)</label>
                    <div class="flex gap-2">
                        <select name="mes_nacimiento" class="flex-1 bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                            <?php
                            $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                            $mes_actual = date('m', strtotime($usuario['fecha_nacimiento']));
                            for($i=1; $i<=12; $i++):
                                $val = str_pad($i,2,'0',STR_PAD_LEFT);
                                echo "<option value=\"$val\" ".($val==$mes_actual?'selected':'').">$meses[$i]</option>";
                            endfor;
                            ?>
                        </select>
                        <select name="dia_nacimiento" class="flex-1 bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                            <?php
                            $dia_actual = date('d', strtotime($usuario['fecha_nacimiento']));
                            for($d=1; $d<=31; $d++):
                                $val = str_pad($d,2,'0',STR_PAD_LEFT);
                                echo "<option value=\"$val\" ".($val==$dia_actual?'selected':'').">$d</option>";
                            endfor;
                            ?>
                        </select>
                    </div>
                    <p class="text-xs text-outline-variant mt-1">El año no puede modificarse.</p>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-surface-container flex justify-end gap-3">
                <a href="#" onclick="history.back()" class="px-6 py-2 rounded-full bg-surface-container-low text-primary font-bold text-sm">Cancelar</a>
                <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white font-bold text-sm">Actualizar</button>
            </div>
        </section>
        <br><br>

        <!-- Correo Electrónico (solo lectura) -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <h2 class="text-xl font-extrabold text-on-surface tracking-tight mb-6">Correo Electrónico</h2>
            <div class="space-y-2">
                <label class="block text-on-surface-variant font-medium text-sm ml-1">Dirección de correo</label>
                <input type="email" value="<?= htmlspecialchars($usuario['correo'] ?? 'usuario@unio.com') ?>" readonly
                       class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium opacity-75 cursor-not-allowed">
                <p class="text-xs text-outline-variant">El correo electrónico no se puede modificar desde aquí.</p>
            </div>
        </section>
        <br><br>

        <!-- Contraseña (solo visual) -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container opacity-60 pointer-events-none">
            <h2 class="text-xl font-extrabold text-on-surface tracking-tight mb-6">Contraseña</h2>
            <div class="space-y-2">
                <label class="block text-on-surface-variant font-medium text-sm ml-1">Contraseña actual</label>
                <input type="password" value="********" disabled
                       class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-on-surface transition-all font-medium">
            </div>
            <p class="text-xs text-outline-variant mt-3">Cambio de contraseña no disponible por ahora.</p>
        </section>
        <br><br>

        <!-- Sobre Mí -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <h2 class="text-xl font-extrabold text-on-surface tracking-tight mb-4">Sobre Mí</h2>
            <textarea name="biografia" placeholder="Cuéntanos un poco sobre ti..." rows="4" maxlength="300"
                      class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all resize-none leading-relaxed"><?= htmlspecialchars($usuario['biografia'] ?? '') ?></textarea>
            <div class="flex justify-between items-center mt-1">
                <span class="text-[11px] font-bold text-outline uppercase tracking-widest" id="contador">0 / 300</span>
                <span class="text-[11px] text-outline-variant">Máximo 300 caracteres</span>
            </div>
            <div class="mt-6 pt-6 border-t border-surface-container flex justify-end gap-3">
                <a href="#" onclick="history.back()" class="px-6 py-2 rounded-full bg-surface-container-low text-primary font-bold text-sm">Cancelar</a>
                <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white font-bold text-sm">Actualizar</button>
            </div>
        </section>
        <br><br>

        <!-- Intereses (estilo suave) -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-on-surface tracking-tight">Mis Intereses</h2>
                <p class="text-sm text-on-surface-variant mt-1">Selecciona las categorías que definen tu estilo de vida.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <?php foreach ($tipos_actividad as $tipo): 
                    $seleccionado = in_array($tipo['id_tipo'], $intereses_usuario);
                ?>
                <label class="cursor-pointer">
                    <input type="checkbox" name="intereses[]" value="<?= $tipo['id_tipo'] ?>" <?= $seleccionado ? 'checked' : '' ?> class="hidden peer">
                    <div class="flex items-center gap-3 p-4 rounded-xl transition-all active:scale-95
                                <?= $seleccionado 
                                    ? 'bg-primary-container text-on-primary-container border-2 border-primary shadow-sm' 
                                    : 'bg-surface-container-lowest text-on-surface border border-surface-container hover:bg-surface-container-low' ?>">
                        <span class="material-symbols-outlined text-lg">interests</span>
                        <span class="text-xs font-bold text-left"><?= htmlspecialchars($tipo['nombre_tipo']) ?></span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 pt-6 border-t border-surface-container flex justify-end gap-3">
                <a href="#" onclick="history.back()" class="px-6 py-2 rounded-full bg-surface-container-low text-primary font-bold text-sm">Cancelar</a>
                <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white font-bold text-sm">Actualizar</button>
            </div>
        </section>
        <br><br>

        <!-- Foto de perfil -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <h2 class="text-xl font-extrabold text-on-surface tracking-tight mb-6">Foto de perfil</h2>
            <div class="flex items-center gap-6">
                <?php if (!empty($usuario['foto_base64'])): ?>
                    <img src="<?= $usuario['foto_base64'] ?>" alt="Foto actual" class="w-24 h-24 rounded-full object-cover border-2 border-surface-container">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-full bg-surface-container-low flex items-center justify-center border-2 border-surface-container">
                        <span class="material-symbols-outlined text-4xl text-outline-variant">person</span>
                    </div>
                <?php endif; ?>
                <div class="flex-1">
                    <label class="block text-on-surface-variant font-medium text-sm mb-2">Seleccionar nueva foto (JPG, PNG, WEBP, máx. 5MB)</label>
                    <input type="file" name="foto_perfil" accept="image/jpeg,image/png,image/webp"
                           class="block w-full text-sm text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-primary file:text-white hover:file:bg-primary-dim transition-all">
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-surface-container flex justify-end gap-3">
                <a href="#" onclick="history.back()" class="px-6 py-2 rounded-full bg-surface-container-low text-primary font-bold text-sm">Cancelar</a>
                <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white font-bold text-sm">Actualizar</button>
            </div>
        </section>
        <br><br>

        <!-- Ubicación en mapa -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-surface-container">
            <h2 class="text-xl font-extrabold text-on-surface tracking-tight mb-6">Ubicación</h2>
            <div id="map" style="height: 300px;" class="rounded-xl overflow-hidden border border-surface-container mb-4"></div>
            <button type="button" id="btnGeo" class="px-4 py-2 rounded-full bg-primary text-white font-bold text-sm flex items-center gap-2 hover:bg-primary-dim transition">
                <span class="material-symbols-outlined">my_location</span> Usar mi ubicación actual
            </button>
            <input type="hidden" name="latitud" id="latitud" value="<?= $usuario['latitud'] ?? '18.4500' ?>">
            <input type="hidden" name="longitud" id="longitud" value="<?= $usuario['longitud'] ?? '-96.3500' ?>">
            <p class="text-xs text-outline-variant mt-2">Arrastra el marcador o haz clic en el mapa para ajustar tu ubicación.</p>
            <div class="mt-6 pt-6 border-t border-surface-container flex justify-end gap-3">
                <a href="#" onclick="history.back()" class="px-6 py-2 rounded-full bg-surface-container-low text-primary font-bold text-sm">Cancelar</a>
                <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white font-bold text-sm">Actualizar</button>
            </div>
        </section>

        <!-- Ya no hay botón "Volver al perfil" fijo, usamos el de regresar superior -->
    </form>
</div>

<script>
    // Modo oscuro desde PHP
    var modoOscuro = <?= $modoOscuro ? '1' : '0' ?>;

    // Definición de capas base
    const osmStandard = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
    const cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 });
    const cartoVoyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 });

    const baseLayers = [
        { name: 'Oscuro', layer: cartoDark, id: 'dark' },
        { name: 'Claro', layer: cartoVoyager, id: 'light' },
        { name: 'Vista de calles', layer: osmStandard, id: 'osm' },
        { name: 'Vista satelital', layer: esriSatellite, id: 'esri' },
    ];

    let activeBaseLayer = null;

    // Control de capas (idéntico al del dashboard)
    const LayerMenuControl = L.Control.extend({
        options: { position: 'bottomleft' },
        onAdd: function(map) {
            const container = L.DomUtil.create('div', 'layer-menu-control');
            container.style.backgroundColor = modoOscuro ? '#0f0f0f' : '#ffffff';
            container.style.color = modoOscuro ? '#e0e0e0' : '#1a1a1a';
            container.style.borderRadius = '30px';
            container.style.boxShadow = '0 2px 8px rgba(0,0,0,0.3)';
            container.style.fontFamily = 'system-ui, sans-serif';
            container.style.fontSize = '14px';
            container.style.fontWeight = '500';
            container.style.cursor = 'pointer';
            container.style.padding = '6px 14px';
            container.style.border = modoOscuro ? '1px solid #1e1e1e' : '1px solid #ccc';
            container.style.transition = 'all 0.2s';
            container.style.zIndex = '1000';

            const button = L.DomUtil.create('div', 'layer-menu-button', container);
            button.innerHTML = 'Capas';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.gap = '6px';
            button.style.userSelect = 'none';

            const dropdown = L.DomUtil.create('div', 'layer-menu-dropdown', container);
            dropdown.style.position = 'absolute';
            dropdown.style.bottom = '100%';
            dropdown.style.left = '0';
            dropdown.style.marginBottom = '8px';
            dropdown.style.backgroundColor = modoOscuro ? '#1e1e1e' : '#f9f9f9';
            dropdown.style.borderRadius = '16px';
            dropdown.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
            dropdown.style.padding = '10px 0';
            dropdown.style.minWidth = '180px';
            dropdown.style.border = modoOscuro ? '1px solid #1e1e1e' : '1px solid #ddd';
            dropdown.style.display = 'none';
            dropdown.style.flexDirection = 'column';
            dropdown.style.gap = '0';
            dropdown.style.zIndex = '1100';

            baseLayers.forEach(layer => {
                const label = L.DomUtil.create('label', 'layer-option', dropdown);
                label.style.display = 'flex';
                label.style.alignItems = 'center';
                label.style.gap = '12px';
                label.style.padding = '8px 16px';
                label.style.cursor = 'pointer';
                label.style.transition = 'background 0.15s';
                label.style.fontWeight = '500';
                label.style.color = modoOscuro ? '#e0e0e0' : '#1a1a1a';
                label.onmouseenter = () => { label.style.backgroundColor = modoOscuro ? '#3a3a4a' : '#eef2f5'; };
                label.onmouseleave = () => { label.style.backgroundColor = 'transparent'; };

                const radio = L.DomUtil.create('input', '', label);
                radio.type = 'radio';
                radio.name = 'baseLayer';
                radio.value = layer.id;
                radio.style.width = '18px';
                radio.style.height = '18px';
                radio.style.cursor = 'pointer';
                radio.style.accentColor = '#5a2af7';
                radio.style.margin = '0';

                const span = L.DomUtil.create('span', '', label);
                span.innerText = layer.name;

                radio.addEventListener('change', () => {
                    if (radio.checked) {
                        switchBaseLayer(layer.layer);
                        dropdown.style.display = 'none';
                    }
                });
            });

            button.addEventListener('click', (e) => {
                L.DomEvent.stopPropagation(e);
                const isVisible = dropdown.style.display === 'flex';
                dropdown.style.display = isVisible ? 'none' : 'flex';
            });

            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            function syncRadio() {
                let activeId = null;
                if (activeBaseLayer === osmStandard) activeId = 'osm';
                else if (activeBaseLayer === esriSatellite) activeId = 'esri';
                else if (activeBaseLayer === cartoDark) activeId = 'dark';
                else if (activeBaseLayer === cartoVoyager) activeId = 'light';
                dropdown.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.checked = (radio.value === activeId);
                });
            }

            const originalSwitch = switchBaseLayer;
            window.switchBaseLayer = function(newLayer) {
                originalSwitch(newLayer);
                syncRadio();
            };
            syncRadio();

            return container;
        }
    });

    function switchBaseLayer(newLayer) {
        if (!map || activeBaseLayer === newLayer) return;
        if (activeBaseLayer) map.removeLayer(activeBaseLayer);
        map.addLayer(newLayer);
        activeBaseLayer = newLayer;
    }

    // Coordenadas iniciales
    var defLat = <?= $usuario['latitud'] ?: 18.4500 ?>;
    var defLng = <?= $usuario['longitud'] ?: -96.3500 ?>;

    // Capa inicial según modo oscuro
    const initialLayer = modoOscuro ? cartoDark : cartoVoyager;
    activeBaseLayer = initialLayer;

    var map = L.map('map').setView([defLat, defLng], 13);
    initialLayer.addTo(map);
    new LayerMenuControl().addTo(map);

    var markerColor = '#2196F3';
	var markerIcon = L.divIcon({
		className: 'custom-marker',
		html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="${markerColor}" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`,
		iconSize: [40, 40],
		iconAnchor: [20, 40],
		popupAnchor: [0, -40]
	});
    var marker = L.marker([defLat, defLng], { icon: markerIcon, draggable: true }).addTo(map);

    function actualizarCoords(lat, lng) {
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
    }

    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        actualizarCoords(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        actualizarCoords(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('btnGeo').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                actualizarCoords(lat, lng);
            }, function(err) { alert("Error obteniendo ubicación"); });
        } else { alert("Geolocalización no soportada"); }
    });

    actualizarCoords(defLat, defLng);

    // Contador de caracteres para biografía
    var textarea = document.querySelector('textarea[name="biografia"]');
    var contador = document.getElementById('contador');
    textarea.addEventListener('input', function() {
        contador.textContent = textarea.value.length + ' / 300';
    });
    contador.textContent = textarea.value.length + ' / 300';
</script>

<?php include 'includes/bottom-nav.php' ?>
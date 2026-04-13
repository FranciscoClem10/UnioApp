<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
// Variables disponibles: $actividad, $tipos, $organizadores, $solicitudes, $participantes, $invitaciones, $restricciones
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar actividad - <?= htmlspecialchars($actividad['nombre']) ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .campo { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        #map { height: 300px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .seccion { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; }
        .lista-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding: 8px 0; }
        .error { color: red; }
        .exito { color: green; }
        .bloqueado { background: #e9ecef; color: #6c757d; cursor: not-allowed; }
        .buscar-usuario { display: flex; gap: 10px; margin-bottom: 10px; }
        .resultado-busqueda { cursor: pointer; padding: 5px; border-bottom: 1px solid #eee; }
        .resultado-busqueda:hover { background: #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <h2>Editar actividad: <?= htmlspecialchars($actividad['nombre']) ?></h2>
    <a href="<?= BASE_URL ?>?c=actividad&a=edicion">← Volver a mis actividades</a>

    <?php if (isset($_SESSION['error_edicion'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_edicion']) ?></div>
        <?php unset($_SESSION['error_edicion']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['exito_edicion'])): ?>
        <div class="exito"><?= htmlspecialchars($_SESSION['exito_edicion']) ?></div>
        <?php unset($_SESSION['exito_edicion']); ?>
    <?php endif; ?>

    <?php if ($restricciones['bloquear_todo']): ?>
        <div class="error">Esta actividad está <?= $actividad['estado'] ?> y no se puede editar.</div>
    <?php else: ?>
        <!-- Formulario de edición de datos básicos -->
        <form action="<?= BASE_URL ?>?c=actividad&a=actualizar" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($actividad['nombre']) ?>" required>
            </div>
            <div class="campo">
                <label>Tipo de actividad</label>
                <select name="id_tipo" required>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= $tipo['id_tipo'] ?>" <?= $tipo['id_tipo'] == $actividad['id_tipo'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre_tipo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4"><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
            </div>
            <div class="campo">
                <label>Requisitos</label>
                <textarea name="requisitos" rows="3"><?= htmlspecialchars($actividad['requisitos']) ?></textarea>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="campo" style="flex:1">
                    <label>Edad mínima</label>
                    <input type="number" name="edad_minima" value="<?= $actividad['edad_minima'] ?>" min="0" max="99">
                </div>
                <div class="campo" style="flex:1">
                    <label>Edad máxima</label>
                    <input type="number" name="edad_maxima" value="<?= $actividad['edad_maxima'] ?>" min="0" max="99">
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="campo" style="flex:1">
                    <label>Mínimo participantes</label>
                    <input type="number" name="limite_participantes_min" value="<?= $actividad['limite_participantes_min'] ?>" min="1" <?= ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0) ? 'readonly class="bloqueado"' : '' ?>>
                    <?php if ($restricciones['hay_miembros'] && $restricciones['participantes_actuales'] > 0): ?>
                        <small>No se puede reducir por debajo de <?= $restricciones['participantes_actuales'] ?> confirmados.</small>
                    <?php endif; ?>
                </div>
                <div class="campo" style="flex:1">
                    <label>Máximo participantes</label>
                    <input type="number" name="limite_participantes_max" value="<?= $actividad['limite_participantes_max'] ?>" min="1" <?= ($restricciones['hay_miembros'] && $restricciones['solo_aumentar_max']) ? 'min="'.$actividad['limite_participantes_max'].'"' : '' ?>>
                    <?php if ($restricciones['hay_miembros'] && $restricciones['solo_aumentar_max']): ?>
                        <small>Solo se puede aumentar (ya se alcanzó el límite).</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="campo">
                <label>Privacidad</label>
                <select name="privacidad">
                    <option value="publica" <?= $actividad['privacidad'] == 'publica' ? 'selected' : '' ?>>Pública</option>
                    <option value="por_aprobacion" <?= $actividad['privacidad'] == 'por_aprobacion' ? 'selected' : '' ?>>Por aprobación</option>
                    <option value="privada" <?= $actividad['privacidad'] == 'privada' ? 'selected' : '' ?>>Privada</option>
                </select>
            </div>
            
            <!-- Ubicación con mapa (siempre visible si no está bloqueado) -->
            <div class="campo">
                <label>Ubicación</label>
                <div id="map"></div>
                <input type="hidden" name="latitud" id="latitud" value="<?= $actividad['latitud'] ?>">
                <input type="hidden" name="longitud" id="longitud" value="<?= $actividad['longitud'] ?>">
                <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
                    <small class="error">Ubicación bloqueada porque hay miembros y la actividad empieza en menos de 6 horas.</small>
                <?php endif; ?>
            </div>
            
            <!-- Fechas -->
            <div style="display: flex; gap: 15px;">
                <div class="campo" style="flex:1">
                    <label>Fecha y hora de inicio</label>
                    <input type="datetime-local" name="fecha_inicio" value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_inicio'])) ?>" <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'disabled' : '' ?>>
                </div>
                <div class="campo" style="flex:1">
                    <label>Fecha y hora de fin</label>
                    <input type="datetime-local" name="fecha_fin" value="<?= date('Y-m-d\TH:i', strtotime($actividad['fecha_fin'])) ?>" <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_fechas']) ? 'disabled' : '' ?>>
                </div>
            </div>
            
            <!-- Foto -->
            <div class="campo">
                <label>Foto de actividad (JPG, PNG, WEBP, máx. 5MB)</label>
                <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp">
                <?php if (!empty($actividad['foto_actividad'])): ?>
                    <div><img src="data:image/jpeg;base64,<?= base64_encode($actividad['foto_actividad']) ?>" style="width: 100px;"></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Guardar cambios</button>
        </form>
    <?php endif; ?>


    <!-- SECCIÓN: ORGANIZADORES -->
<div class="seccion">
    <h3>Organizadores</h3>
    <p>Los organizadores pueden gestionar solicitudes, invitar y expulsar participantes, pero no editar la actividad.</p>
    <div>
        <?php foreach ($organizadores as $org): ?>
            <div class="lista-item">
                <span><?= htmlspecialchars($org['nombre_completo']) ?> (<?= htmlspecialchars($org['email']) ?>)</span>
                <a href="<?= BASE_URL ?>?c=actividad&a=quitarOrganizador&id_actividad=<?= $actividad['id_actividad'] ?>&id_usuario=<?= $org['id_usuario'] ?>" class="btn btn-danger" onclick="return confirm('¿Quitar como organizador?')">Quitar</a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <h4>Agregar nuevo organizador</h4>
    <form action="<?= BASE_URL ?>?c=actividad&a=agregarOrganizador" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
        <div style="display: flex; gap: 10px;">
            <div style="flex:1; position: relative;">
                <input type="text" id="buscar_organizador_input" placeholder="Buscar por nombre, apellido o email..." style="width: 100%; padding: 8px;">
                <div id="resultados_organizador" style="position: absolute; background: white; border: 1px solid #ccc; width: 100%; max-height: 200px; overflow-y: auto; z-index: 1000; display: none;"></div>
                <input type="hidden" name="id_usuario" id="organizador_seleccionado" required>
            </div>
            <button type="button" id="btn_buscar_organizador" class="btn">Buscar</button>
            <button type="submit" class="btn">Agregar</button>
        </div>
    </form>
</div>

    <!-- SECCIÓN: SOLICITUDES PENDIENTES -->
    <?php if ($actividad['privacidad'] == 'por_aprobacion' && !empty($solicitudes)): ?>
    <div class="seccion">
        <h3>Solicitudes de unión pendientes</h3>
        <?php foreach ($solicitudes as $s): ?>
            <div class="lista-item">
                <span><?= htmlspecialchars($s['nombre_completo']) ?> - Solicitó el <?= $s['fecha_solicitud'] ?></span>
                <div>
                    <a href="<?= BASE_URL ?>?c=actividad&a=aceptarSolicitud&id_actividad=<?= $actividad['id_actividad'] ?>&id_usuario=<?= $s['id_usuario'] ?>" class="btn btn-success">Aceptar</a>
                    <a href="<?= BASE_URL ?>?c=actividad&a=rechazarSolicitud&id_actividad=<?= $actividad['id_actividad'] ?>&id_usuario=<?= $s['id_usuario'] ?>" class="btn btn-danger">Rechazar</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- SECCIÓN: PARTICIPANTES ACTUALES -->
    <div class="seccion">
        <h3>Participantes actuales (<?= count($participantes) ?>)</h3>
        <?php foreach ($participantes as $p): ?>
            <div class="lista-item">
                <span><?= htmlspecialchars($p['nombre_completo']) ?> - Rol: <?= $p['rol'] ?></span>
                <?php if ($p['rol'] != 'creador' && $p['rol'] != 'organizador'): ?>
                    <a href="<?= BASE_URL ?>?c=actividad&a=expulsarParticipante&id_actividad=<?= $actividad['id_actividad'] ?>&id_usuario=<?= $p['id_usuario'] ?>" class="btn btn-danger" onclick="return confirm('¿Expulsar a este participante?')">Expulsar</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- SECCIÓN: INVITACIONES -->
    <div class="seccion">
        <h3>Enviar invitaciones</h3>
        <form action="<?= BASE_URL ?>?c=actividad&a=enviarInvitacion" method="POST">
            <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <select name="tipo_invitacion" id="tipo_invitacion" required style="width: 150px;">
                    <option value="usuario">A usuario registrado</option>
                    <option value="email">Por correo electrónico</option>
                </select>
                <div id="destinatario_usuario" style="flex:1;">
                    <input type="text" id="buscar_invitado" placeholder="Buscar usuario por nombre o email..." style="width: 100%;">
                    <div id="resultados_invitacion"></div>
                    <input type="hidden" name="destinatario" id="invitado_hidden">
                </div>
                <div id="destinatario_email" style="flex:1; display: none;">
                    <input type="email" name="destinatario" placeholder="correo@ejemplo.com">
                </div>
                <button type="submit" class="btn">Enviar invitación</button>
            </div>
        </form>
        <h4>Invitaciones enviadas</h4>
        <?php if (empty($invitaciones)): ?>
            <p>No hay invitaciones.</p>
        <?php else: ?>
            <?php foreach ($invitaciones as $inv): ?>
                <div class="lista-item">
                    <span><?= htmlspecialchars($inv['contacto'] ?? $inv['email_invitado']) ?> - Estado: <?= $inv['estado'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // Inicializar mapa (siempre, a menos que esté bloqueado totalmente)
    <?php if (!$restricciones['bloquear_todo']): ?>
        var lat = <?= (float)($actividad['latitud'] ?? 18.4500) ?>;
        var lng = <?= (float)($actividad['longitud'] ?? -96.3500) ?>;
        var map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);
        var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        }
        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            actualizarCoordenadas(pos.lat, pos.lng);
        });
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        });
        actualizarCoordenadas(lat, lng);
        
        // Si la ubicación está bloqueada por restricciones, deshabilitamos el mapa (pero se muestra)
        <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
            marker.dragging.disable();
            map.dragging.disable();
            map.touchZoom.disable();
            map.scrollWheelZoom.disable();
            map.doubleClickZoom.disable();
            marker.bindTooltip("Ubicación bloqueada").openTooltip();
        <?php endif; ?>
    <?php else: ?>
        document.getElementById('map').style.display = 'none';
    <?php endif; ?>

   // Búsqueda de usuarios para agregar organizador
const inputOrg = document.getElementById('buscar_organizador_input');
const btnBuscarOrg = document.getElementById('btn_buscar_organizador');
const resultadosOrgDiv = document.getElementById('resultados_organizador');
const hiddenOrgId = document.getElementById('organizador_seleccionado');

// Función para buscar usuarios (usa el endpoint correcto: buscarAmigos)
async function buscarUsuarios(termino) {
    if (termino.length < 2) return [];
    const response = await fetch(`<?= BASE_URL ?>?c=actividad&a=buscarAmigos&term=${encodeURIComponent(termino)}`);
    return await response.json();
}

// Evento al hacer clic en Buscar
btnBuscarOrg.addEventListener('click', async function() {
    const term = inputOrg.value.trim();
    if (term.length < 2) {
        resultadosOrgDiv.innerHTML = '<div class="error">Ingrese al menos 2 caracteres</div>';
        resultadosOrgDiv.style.display = 'block';
        return;
    }
    const data = await buscarUsuarios(term);
    if (!data || data.length === 0) {
        resultadosOrgDiv.innerHTML = '<div class="error">No se encontraron usuarios</div>';
        resultadosOrgDiv.style.display = 'block';
        return;
    }
    // Mostrar lista de resultados
    let html = '';
    data.forEach(user => {
        html += `<div class="resultado-busqueda" onclick="seleccionarOrganizador(${user.id_usuario}, '${user.nombre_completo.replace(/'/g, "\\'")}')">
                    ${user.nombre_completo} (${user.email})
                </div>`;
    });
    resultadosOrgDiv.innerHTML = html;
    resultadosOrgDiv.style.display = 'block';
});

// Ocultar resultados al hacer clic fuera (opcional)
document.addEventListener('click', function(e) {
    if (!inputOrg.contains(e.target) && !btnBuscarOrg.contains(e.target) && !resultadosOrgDiv.contains(e.target)) {
        resultadosOrgDiv.style.display = 'none';
    }
});

// Función global para seleccionar organizador
window.seleccionarOrganizador = function(id, nombre) {
    hiddenOrgId.value = id;
    inputOrg.value = nombre;
    resultadosOrgDiv.style.display = 'none';
};
    
    
    // Búsqueda para invitaciones (igual que antes)
    const busquedaInv = document.getElementById('buscar_invitado');
    const resultadosInv = document.getElementById('resultados_invitacion');
    const hiddenInv = document.getElementById('invitado_hidden');
    let timeoutInv;
    busquedaInv.addEventListener('input', function() {
        clearTimeout(timeoutInv);
        const term = this.value.trim();
        if (term.length < 2) {
            resultadosInv.innerHTML = '';
            return;
        }
        timeoutInv = setTimeout(() => {
            fetch('<?= BASE_URL ?>?c=actividad&a=buscarUsuario&term=' + encodeURIComponent(term))
                .then(res => res.json())
                .then(data => {
                    resultadosInv.innerHTML = '';
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.textContent = user.nombre_completo + ' (' + user.email + ')';
                        div.className = 'resultado-busqueda';
                        div.onclick = () => {
                            hiddenInv.value = user.id_usuario;
                            busquedaInv.value = user.nombre_completo;
                            resultadosInv.innerHTML = '';
                        };
                        resultadosInv.appendChild(div);
                    });
                });
        }, 300);
    });
    
    // Mostrar/ocultar campos de invitación
    const tipoSelect = document.getElementById('tipo_invitacion');
    const divUsuario = document.getElementById('destinatario_usuario');
    const divEmail = document.getElementById('destinatario_email');
    tipoSelect.addEventListener('change', function() {
        if (this.value === 'usuario') {
            divUsuario.style.display = 'block';
            divEmail.style.display = 'none';
            document.querySelector('#destinatario_email input').removeAttribute('name');
            document.querySelector('#destinatario_usuario input').setAttribute('name', 'buscar_amigo');
        } else {
            divUsuario.style.display = 'none';
            divEmail.style.display = 'block';
            document.querySelector('#destinatario_usuario input').removeAttribute('name');
            document.querySelector('#destinatario_email input').setAttribute('name', 'destinatario');
        }
    }
    );
</script>
</body>
</html>
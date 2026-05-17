<script>
// Cache de direcciones: clave "lat,lng" => texto
const addressCache = {};
const pendingCoords = new Set();

let addressQueue = [];
let addressTimer = null;

function processAddressQueue() {
    if (addressQueue.length === 0) return;
    const { lat, lng, callback } = addressQueue.shift();
    const key = `${lat},${lng}`;
    if (addressCache[key]) {
        callback(addressCache[key]);
        processAddressQueue();
        return;
    }
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=es`, {
        headers: { 'User-Agent': 'UnioDashboard/1.0' }
    })
    .then(response => response.json())
    .then(data => {
        let direccion = (data && data.display_name) ? data.display_name : 'Ubicación no disponible';
        addressCache[key] = direccion;
        callback(direccion);
    })
    .catch(() => {
        callback('Ubicación no disponible');
    })
    .finally(() => {
        addressTimer = setTimeout(processAddressQueue, 1000);
    });
}

function getAddress(lat, lng, callback) {
    const key = `${lat},${lng}`;
    if (addressCache[key]) {
        callback(addressCache[key]);
        return;
    }
    if (!pendingCoords.has(key)) {
        pendingCoords.add(key);
        addressQueue.push({ lat, lng, callback });
        if (!addressTimer) {
            addressTimer = setTimeout(processAddressQueue, 100);
        }
    }
}

function cargarDirecciones(container) {
    const pendientes = container.querySelectorAll('.dir-cargando');
    pendientes.forEach(span => {
        const lat = parseFloat(span.dataset.lat);
        const lng = parseFloat(span.dataset.lng);
        if (isNaN(lat) || isNaN(lng)) {
            span.textContent = 'Sin ubicación';
            span.classList.remove('dir-cargando');
            return;
        }
        getAddress(lat, lng, (direccion) => {
            span.textContent = direccion;
            span.classList.remove('dir-cargando');
        });
    });
}

function renderLista() {
    const container = document.getElementById('listaActividades');
    const filtradas = filtrarActividades();

    if (filtradas.length === 0) {
        container.innerHTML = `
            <div class="bg-surface-container-lowest rounded-xl p-4 text-center text-on-surface-variant text-sm">
                No hay eventos con esos filtros.
            </div>
        `;
        return;
    }

    container.innerHTML = filtradas.map(act => {
        let direccionHtml = '';
        if (act.direccion && act.direccion.trim() !== '') {
            direccionHtml = escapeHtml(act.direccion);
        } else {
            direccionHtml = `<span class="dir-cargando" data-lat="${act.latitud}" data-lng="${act.longitud}">Cargando dirección...</span>`;
        }
        return `
            <div 
                class="bg-surface-container-lowest p-3 rounded-xl shadow-sm hover:translate-x-1 transition-all duration-200 cursor-pointer border border-surface-container-high" 
                onclick="verDetalle(${act.id_actividad})"
            >
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-xl">event_note</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm text-on-surface">
                            ${escapeHtml(act.titulo)}
                        </h3>
                        <p class="text-xs text-on-surface-variant flex flex-col mt-1">
                            <span>${act.fecha || 'Próximamente'}</span>
                            <span class="text-primary font-medium">
                                ${act.hora ? formatearHora12(act.hora) : ''}
                            </span>
                        </p>
                        <div class="flex items-center mt-2 gap-2 text-[10px] text-primary font-semibold">
                            ${direccionHtml}
                            <span>${act.limite_personas}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    cargarDirecciones(container);
}

function precargarTodasDirecciones() {
    const unicas = new Map();
    actividades.forEach(act => {
        if (act.latitud && act.longitud && act.estado !== 'cancelada' && !act.direccion) {
            const key = `${act.latitud},${act.longitud}`;
            if (!addressCache[key] && !unicas.has(key)) {
                unicas.set(key, { lat: act.latitud, lng: act.longitud });
            }
        }
    });
    unicas.forEach((coord, key) => {
        getAddress(coord.lat, coord.lng, () => {});
    });
}
</script>
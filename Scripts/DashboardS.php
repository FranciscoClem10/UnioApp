    <script>
        // --------------------------------------------------------------
        // 1. Variables PHP incrustadas en JavaScript
        // --------------------------------------------------------------
        const actividades = <?= json_encode($actividades) ?>;  // array de actividades
        const currentUserId = <?= $_SESSION['usuario_id'] ?? 0 ?>;
        const BASE_URL = "<?= BASE_URL ?>";

        let mapInstance = null;
        let markerLayer = null;
        let userMarker = null;
        let currentFilter = "";       // filtro por texto (búsqueda)
        let currentCategoria = "";    // filtro por categoría

        // --------------------------------------------------------------
        // 2. Funciones de renderizado de la lista de actividades
        // --------------------------------------------------------------
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function filtrarActividades() {
            return actividades.filter(act => {
                const matchTexto = currentFilter === "" || 
                    act.titulo.toLowerCase().includes(currentFilter) || 
                    act.categoria.toLowerCase().includes(currentFilter);
                const matchCategoria = currentCategoria === "" || act.categoria === currentCategoria;
                return matchTexto && matchCategoria && act.estado !== 'cancelada';
            });
        }

        function renderLista() {
            const container = document.getElementById('listaActividades');
            const filtradas = filtrarActividades();
            if (filtradas.length === 0) {
                container.innerHTML = `<div class="bg-surface-container-lowest rounded-xl p-4 text-center text-on-surface-variant text-sm">No hay eventos con esos filtros.</div>`;
                return;
            }
            container.innerHTML = filtradas.map(act => `
                <div class="bg-surface-container-lowest p-3 rounded-xl shadow-sm hover:translate-x-1 transition-all duration-200 cursor-pointer border border-surface-container-high" onclick="verDetalle(${act.id_actividad})">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-xl">event_note</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-on-surface">${escapeHtml(act.titulo)}</h3>
                            <p class="text-xs text-on-surface-variant">${act.fecha || 'Próximamente'} ${act.hora ? '· ' + act.hora.slice(0,5) : ''}</p>
                            <div class="flex items-center mt-1 gap-2 text-[10px] text-primary font-semibold">
                                <span>📍 ${act.latitud}, ${act.longitud}</span>
                                <span>👥 ${act.limite_personas}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Redirigir al detalle de la actividad
        window.verDetalle = function(id) {
            window.location.href = BASE_URL + "?c=actividad&a=detalle&id=" + id;
        };

        // --------------------------------------------------------------
        // 3. Mapa y marcadores (Leaflet)
        // --------------------------------------------------------------
        function actualizarMapa() {
            if (!mapInstance) return;
            if (markerLayer) mapInstance.removeLayer(markerLayer);
            markerLayer = L.layerGroup().addTo(mapInstance);
            
            const actividadesFiltradas = filtrarActividades();
            actividadesFiltradas.forEach(act => {
                if (act.latitud && act.longitud) {
                    const marker = L.marker([parseFloat(act.latitud), parseFloat(act.longitud)], { riseOnHover: true });
                    marker.bindTooltip(`<strong>${escapeHtml(act.titulo)}</strong><br>${act.categoria} · ${act.fecha || 'Próximo'}`, { sticky: true });
                    marker.on('click', () => verDetalle(act.id_actividad));
                    marker.addTo(markerLayer);
                }
            });
        }

        function initMap() {
            const defaultCenter = [18.4500, -96.3500];
            mapInstance = L.map('map').setView(defaultCenter, 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
            }).addTo(mapInstance);
            
            // Geolocalización
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        mapInstance.setView([userLat, userLng], 13);
                        if (userMarker) mapInstance.removeLayer(userMarker);
                        const customIcon = L.divIcon({ className: 'user-marker', html: '📍', iconSize: [24, 24] });
                        userMarker = L.marker([userLat, userLng], { icon: customIcon }).addTo(mapInstance);
                        userMarker.bindTooltip("Tu ubicación actual", { sticky: true });
                        actualizarMapa();
                    },
                    () => { actualizarMapa(); }
                );
            } else {
                actualizarMapa();
            }
        }

        // Botón "Mi ubicación"
        function centrarEnMiUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        mapInstance.setView([pos.coords.latitude, pos.coords.longitude], 14);
                    },
                    () => alert("No se pudo obtener tu ubicación")
                );
            } else {
                alert("Geolocalización no soportada");
            }
        }

        // --------------------------------------------------------------
        // 4. Eventos de búsqueda y filtros por categoría
        // --------------------------------------------------------------
        function aplicarFiltros() {
            renderLista();
            actualizarMapa();
        }

        // Barra de búsqueda
        const inputBusqueda = document.getElementById('busquedaEventos');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', (e) => {
                currentFilter = e.target.value.toLowerCase().trim();
                aplicarFiltros();
            });
        }

        // Filtros por categoría (etiquetas dinámicas)
        document.querySelectorAll('.filtro-categoria').forEach(el => {
            el.addEventListener('click', (e) => {
                const cat = el.getAttribute('data-categoria');
                if (currentCategoria === cat) {
                    currentCategoria = "";
                    el.classList.remove('bg-primary', 'text-on-primary');
                    el.classList.add('bg-surface-container-highest', 'text-on-surface');
                } else {
                    currentCategoria = cat;
                    document.querySelectorAll('.filtro-categoria').forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-on-primary');
                        btn.classList.add('bg-surface-container-highest', 'text-on-surface');
                    });
                    el.classList.remove('bg-surface-container-highest', 'text-on-surface');
                    el.classList.add('bg-primary', 'text-on-primary');
                }
                aplicarFiltros();
            });
        });

        // Botón reset filtros
        document.getElementById('resetFiltros')?.addEventListener('click', () => {
            currentFilter = "";
            currentCategoria = "";
            if (inputBusqueda) inputBusqueda.value = "";
            document.querySelectorAll('.filtro-categoria').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-on-primary');
                btn.classList.add('bg-surface-container-highest', 'text-on-surface');
            });
            aplicarFiltros();
        });

        // --------------------------------------------------------------
        // 5. Sidebar toggle y eventos adicionales
        // --------------------------------------------------------------
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            sidebar.classList.toggle('collapsed');
            toggleIcon.innerText = sidebar.classList.contains('collapsed') ? 'chevron_right' : 'chevron_left';
            // Forzar redibujo del mapa al cambiar tamaño del sidebar
            setTimeout(() => { if(mapInstance) mapInstance.invalidateSize(); }, 300);
        }

        document.getElementById('btnMiUbicacion')?.addEventListener('click', centrarEnMiUbicacion);
        
        // Inicialización cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            renderLista();
        });
    </script>
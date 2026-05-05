<script>
	const osmStandard = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
	const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
	const cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 });
	const cartoVoyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 });

	const layerMap = {
		'dark': cartoDark,
		'light': cartoVoyager,
		'osm': osmStandard,
		'esri': esriSatellite
	};

	const baseLayers = [
		{ name: 'Claro', layer: cartoVoyager, id: 'light' },
		{ name: 'Oscuro', layer: cartoDark, id: 'dark' },
		{ name: 'Vista de calles', layer: osmStandard, id: 'osm' },
		{ name: 'Vista satelital', layer: esriSatellite, id: 'esri' },
	];

	let activeBaseLayer = null;

	// Guardar estado del mapa (posición, zoom, capa) – se ejecuta siempre
	function guardarEstadoMapa() {
		if (!mapInstance) return;
		const center = mapInstance.getCenter();
		const zoom = mapInstance.getZoom();
		let capaId = null;
		if (activeBaseLayer === osmStandard) capaId = 'osm';
		else if (activeBaseLayer === esriSatellite) capaId = 'esri';
		else if (activeBaseLayer === cartoDark) capaId = 'dark';
		else if (activeBaseLayer === cartoVoyager) capaId = 'light';

		fetch(BASE_URL + "?c=dashboard&a=guardarEstadoMapa", {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'lat=' + encodeURIComponent(center.lat) +
			      '&lng=' + encodeURIComponent(center.lng) +
			      '&zoom=' + encodeURIComponent(zoom) +
			      '&capa_id=' + encodeURIComponent(capaId)
		}).catch(e => console.warn('No se pudo guardar el estado del mapa', e));
	}

	// Cambio de capa (usado internamente y desde el menú)
	function switchBaseLayer(newLayer) {
		if (!mapInstance || activeBaseLayer === newLayer) return;
		if (activeBaseLayer) mapInstance.removeLayer(activeBaseLayer);
		mapInstance.addLayer(newLayer);
		activeBaseLayer = newLayer;
		guardarEstadoMapa(); // guardar cambio de capa
	}

	// Configurar el botón HTML de capas (esquina inferior izquierda)
	function setupLayerButton() {
		const btn = document.getElementById('btnCapas');
		if (!btn) return;

		const oldDropdown = btn.querySelector('.layer-dropdown-html');
		if (oldDropdown) oldDropdown.remove();

		const dropdown = document.createElement('div');
		dropdown.className = 'layer-dropdown-html';
		dropdown.style.position = 'absolute';
		dropdown.style.bottom = '100%';
		dropdown.style.left = '0';
		dropdown.style.marginBottom = '8px';
		dropdown.style.backgroundColor = modoOscuro ? '#1e1e1e' : '#f9f9f9';
		dropdown.style.borderRadius = '16px';
		dropdown.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
		dropdown.style.padding = '10px 0';
		dropdown.style.minWidth = '180px';
		dropdown.style.border = modoOscuro ? '1px solid #2a2a2a' : '1px solid #ddd';
		dropdown.style.display = 'none';
		dropdown.style.flexDirection = 'column';
		dropdown.style.zIndex = '2000';
		btn.appendChild(dropdown);

		baseLayers.forEach(layer => {
			const label = document.createElement('label');
			label.style.display = 'flex';
			label.style.alignItems = 'center';
			label.style.gap = '12px';
			label.style.padding = '8px 16px';
			label.style.cursor = 'pointer';
			label.style.fontWeight = '500';
			label.style.color = modoOscuro ? '#e0e0e0' : '#1a1a1a';
			label.onmouseenter = () => label.style.backgroundColor = modoOscuro ? '#3a3a4a' : '#eef2f5';
			label.onmouseleave = () => label.style.backgroundColor = 'transparent';

			const radio = document.createElement('input');
			radio.type = 'radio';
			radio.name = 'htmlBaseLayer';
			radio.value = layer.id;
			radio.style.width = '18px';
			radio.style.height = '18px';
			// dentro del forEach que crea los labels
			radio.style.accentColor = modoOscuro ? '#BB86FC' : '#5a2af7';
			radio.style.margin = '0';

			const span = document.createElement('span');
			span.textContent = layer.name;

			label.appendChild(radio);
			label.appendChild(span);
			dropdown.appendChild(label);

			// Hacer que todo el label active el radio button
			label.addEventListener('click', (e) => {
				// Si el clic no fue directamente sobre el input, lo activamos
				if (e.target !== radio) {
					radio.checked = true;
					// Disparar el evento change manualmente
					radio.dispatchEvent(new Event('change', { bubbles: true }));
				}
			});

			radio.addEventListener('change', () => {
				if (radio.checked) {
					switchBaseLayer(layer.layer);
					dropdown.style.display = 'none';
					syncRadio();
				}
			});
		});

		function syncRadio() {
			let activeId = null;
			if (activeBaseLayer === osmStandard) activeId = 'osm';
			else if (activeBaseLayer === esriSatellite) activeId = 'esri';
			else if (activeBaseLayer === cartoDark) activeId = 'dark';
			else if (activeBaseLayer === cartoVoyager) activeId = 'light';
			const radios = dropdown.querySelectorAll('input[type="radio"]');
			radios.forEach(r => r.checked = (r.value === activeId));
		}

		btn.addEventListener('click', (e) => {
			e.stopPropagation();
			const isVisible = dropdown.style.display === 'flex';
			dropdown.style.display = isVisible ? 'none' : 'flex';
			if (!isVisible) syncRadio();
		});

		document.addEventListener('click', (e) => {
			if (!btn.contains(e.target)) {
				dropdown.style.display = 'none';
			}
		});
	}

	function actualizarMapa() {
		if (!mapInstance) return;
		if (markerLayer) mapInstance.removeLayer(markerLayer);
		markerLayer = L.layerGroup().addTo(mapInstance);

		const actividadesFiltradas = filtrarActividades();
		actividadesFiltradas.forEach(act => {
			if (act.latitud && act.longitud) {
				const icono = getIconForCategoria(act.categoria);
				const marker = L.marker([parseFloat(act.latitud), parseFloat(act.longitud)], { icon: icono, riseOnHover: true });
				marker.bindTooltip(`<strong>${escapeHtml(act.titulo)}</strong><br>${act.categoria}  ${act.fecha || 'Proximo'}`, { sticky: true });
				marker.on('click', () => verDetalle(act.id_actividad));
				marker.addTo(markerLayer);
			}
		});
	}

	function initMap() {
		let initialLat = 18.4500, initialLng = -96.3500;
		let initialZoom = savedZoom;
		let usedSavedView = false;

		if (savedCenter && savedCenter.lat && savedCenter.lng) {
			initialLat = savedCenter.lat;
			initialLng = savedCenter.lng;
			usedSavedView = true;
		} else {
			if (typeof userSavedLat !== 'undefined' && typeof userSavedLng !== 'undefined' &&
			    userSavedLat && userSavedLng && !isNaN(parseFloat(userSavedLat)) && !isNaN(parseFloat(userSavedLng))) {
				initialLat = parseFloat(userSavedLat);
				initialLng = parseFloat(userSavedLng);
			}
		}

		let initialLayer;
		if (capaSesion && layerMap[capaSesion]) {
			initialLayer = layerMap[capaSesion];
		} else {
			initialLayer = modoOscuro == 1 ? cartoDark : osmStandard;
		}
		activeBaseLayer = initialLayer;

		mapInstance = L.map('map').setView([initialLat, initialLng], initialZoom);
		activeBaseLayer.addTo(mapInstance);

		// Guardar estado al mover el mapa (comportamiento original)
		mapInstance.on('moveend', guardarEstadoMapa);

		// Inicializar el botón HTML de capas (sin controles Leaflet)
		setupLayerButton();

		function placeUserMarker(lat, lng, centerMap = false) {
			if (userMarker) mapInstance.removeLayer(userMarker);
			userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(mapInstance);
			userMarker.bindTooltip("Tu ubicacion actual", { sticky: true });
			if (centerMap) {
				mapInstance.setView([lat, lng], 14);
			}
		}

		const shouldCenterOnUser = !usedSavedView;

		if (typeof userSavedLat !== 'undefined' && typeof userSavedLng !== 'undefined' &&
		    userSavedLat && userSavedLng && !isNaN(parseFloat(userSavedLat)) && !isNaN(parseFloat(userSavedLng))) {
			const lat = parseFloat(userSavedLat);
			const lng = parseFloat(userSavedLng);
			placeUserMarker(lat, lng, shouldCenterOnUser);
		} else if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				(pos) => {
					placeUserMarker(pos.coords.latitude, pos.coords.longitude, shouldCenterOnUser);
				},
				() => {}
			);
		}

		actualizarMapa();
		guardarEstadoMapa(); // guardar estado inicial
	}

	function centrarEnMiUbicacion() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				(pos) => {
					mapInstance.setView([pos.coords.latitude, pos.coords.longitude], 14);
					if (!userMarker) {
						userMarker = L.marker([pos.coords.latitude, pos.coords.longitude], { icon: userIcon }).addTo(mapInstance);
						userMarker.bindTooltip("Tu ubicacion actual", { sticky: true });
					} else {
						userMarker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
					}
				},
				() => alert("No se pudo obtener tu ubicacion")
			);
		} else {
			alert("Geolocalizacion no soportada");
		}
	}

	function aplicarFiltros() {
		renderLista();
		actualizarMapa();
	}

	function toggleSidebar() {
		const sidebar = document.getElementById('sidebar');
		const toggleIcon = document.getElementById('toggleIcon');
		sidebar.classList.toggle('collapsed');
		toggleIcon.innerText = sidebar.classList.contains('collapsed') ? 'chevron_right' : 'chevron_left';
		setTimeout(() => { if(mapInstance) mapInstance.invalidateSize(); }, 300);
	}

	const inputBusqueda = document.getElementById('busquedaEventos');
	if (inputBusqueda) {
		inputBusqueda.addEventListener('input', (e) => {
			currentFilter = e.target.value.toLowerCase().trim();
			aplicarFiltros();
		});
	}

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

	document.getElementById('btnMiUbicacion')?.addEventListener('click', centrarEnMiUbicacion);

	document.addEventListener('DOMContentLoaded', () => {
		initMap();
		renderLista();
		precargarTodasDirecciones();
	});
</script>
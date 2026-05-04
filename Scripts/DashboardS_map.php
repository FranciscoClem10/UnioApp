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
	let layerMenuControl = null;

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

			baseLayers.forEach((layer) => {
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

				radio.addEventListener('change', (e) => {
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

			const closeOnOutsideClick = (e) => {
				if (!container.contains(e.target)) {
					dropdown.style.display = 'none';
				}
			};
			document.addEventListener('click', closeOnOutsideClick);

			function syncRadioFromActiveLayer() {
				let activeId = null;
				if (activeBaseLayer === osmStandard) activeId = 'osm';
				else if (activeBaseLayer === esriSatellite) activeId = 'esri';
				else if (activeBaseLayer === cartoDark) activeId = 'dark';
				else if (activeBaseLayer === cartoVoyager) activeId = 'light';
				const radios = dropdown.querySelectorAll('input[type="radio"]');
				radios.forEach(radio => {
					radio.checked = (radio.value === activeId);
				});
			}

			const originalSwitch = switchBaseLayer;
			window.switchBaseLayer = function(newLayer) {
				originalSwitch(newLayer);
				syncRadioFromActiveLayer();
				guardarEstadoMapa();
			};
			syncRadioFromActiveLayer();

			return container;
		}
	});

	function switchBaseLayer(newLayer) {
		if (!mapInstance || activeBaseLayer === newLayer) return;
		if (activeBaseLayer) mapInstance.removeLayer(activeBaseLayer);
		mapInstance.addLayer(newLayer);
		activeBaseLayer = newLayer;
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

		mapInstance.on('moveend', guardarEstadoMapa);

		layerMenuControl = new LayerMenuControl().addTo(mapInstance);

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
		guardarEstadoMapa();
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
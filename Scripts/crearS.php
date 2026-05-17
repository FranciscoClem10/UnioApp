<script>
	// Botones de tipo (sin cambios)
	const tipoBtns = document.querySelectorAll('.tipo-btn');
	const idTipoInput = document.getElementById('id_tipo');
	if (tipoBtns.length) {
		tipoBtns.forEach(btn => {
			btn.addEventListener('click', function () {
				tipoBtns.forEach(b => {
					b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
					b.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
				});
				this.classList.remove('bg-surface-container-highest', 'text-on-surface-variant');
				this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
				idTipoInput.value = this.dataset.id;
			});
		});
	}

	// Preview imagen 
	const fotoInput = document.getElementById('fotoInput');
	const uploadLabel = document.getElementById('uploadLabel');
	const previewContainer = document.getElementById('previewContainer');
	const previewImage = document.getElementById('previewImage');
	const removeImageBtn = document.getElementById('removeImageBtn');

	function mostrarPreview(file) {
		if (file) {
			const reader = new FileReader();
			reader.onload = function(ev) {
				previewImage.innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
				uploadLabel.classList.add('hidden');   // Ocultar label
				previewContainer.classList.remove('hidden'); // Mostrar preview
			};
			reader.readAsDataURL(file);
		}
	}

	function resetearImagen() {
		fotoInput.value = '';               // Limpiar input file
		uploadLabel.classList.remove('hidden');
		previewContainer.classList.add('hidden');
		previewImage.innerHTML = '';        // Eliminar imagen mostrada
	}

	if (fotoInput) {
		fotoInput.addEventListener('change', function(e) {
			const file = e.target.files[0];
			if (file) {
				mostrarPreview(file);
			} else {
				resetearImagen();
			}
		});
	}

	if (removeImageBtn) {
		removeImageBtn.addEventListener('click', function(e) {
			e.preventDefault();
			resetearImagen();
		});
	}

	// Opcional: hacer clic en la imagen para cambiar (abrir selector de archivos)
	if (previewContainer) {
		previewContainer.addEventListener('click', function(e) {
			// Evitar que el clic en el botón de eliminar también dispare esto
			if (e.target === removeImageBtn || removeImageBtn.contains(e.target)) return;
			fotoInput.click();
		});
	}

	// VALIDACIONES PARA LÍMITES DE PARTICIPANTES Y EDAD MÍNIMA

	const minParticipantes = document.querySelector('input[name="limite_participantes_min"]');
	const maxParticipantes = document.querySelector('input[name="limite_participantes_max"]');
	const edadMinima = document.querySelector('input[name="edad_minima"]');

	function validarLimitesParticipantes() {
		if (minParticipantes && maxParticipantes) {
			let min = parseInt(minParticipantes.value, 10);
			let max = parseInt(maxParticipantes.value, 10);
			if (isNaN(min)) min = 10;
			if (isNaN(max)) max = 15;
			if (max < min) {
				alert("El límite máximo no puede ser menor que el mínimo. Se ajustará el máximo al mínimo.");
				maxParticipantes.value = min;
			}
		}
	}

	function validarEdadMinima() {
		if (edadMinima) {
			let edad = parseInt(edadMinima.value, 10);
			if (isNaN(edad)) edad = 18;
			if (edad < 18) {
				alert("La edad mínima debe ser al menos 18 años.");
				edadMinima.value = 18;
			}
		}
	}

	// Asignar eventos de cambio y blur para validaciones
	if (minParticipantes && maxParticipantes) {
		minParticipantes.addEventListener('change', validarLimitesParticipantes);
		maxParticipantes.addEventListener('change', validarLimitesParticipantes);
		// También al perder el foco por si el usuario escribe un valor inválido
		minParticipantes.addEventListener('blur', validarLimitesParticipantes);
		maxParticipantes.addEventListener('blur', validarLimitesParticipantes);
	}
	if (edadMinima) {
		edadMinima.addEventListener('change', validarEdadMinima);
		edadMinima.addEventListener('blur', validarEdadMinima);
	}

	// PREVENIR ENVÍO DEL FORMULARIO CON TECLA ENTER

	const form = document.querySelector('form');
	if (form) {
		form.addEventListener('keypress', function(e) {
			// Si la tecla presionada es Enter
			if (e.key === 'Enter') {
				// Evitar el envío del formulario
				e.preventDefault();
				// Opcional: podrías querer enviar solo si el foco está en un textarea
				// pero aquí lo evitamos completamente para que el usuario use el botón.
				return false;
			}
		});
	}

	// VALIDACIÓN DE FECHAS 
	const inicio = document.querySelector('input[name="fecha_inicio"]');
	const fin = document.querySelector('input[name="fecha_fin"]');
	if (inicio && fin) {
		const validarFechas = () => {
			if (inicio.value && fin.value && fin.value < inicio.value) {
				alert("La fecha de fin no puede ser anterior a la de inicio.");
				fin.value = "";
			}
		};
		inicio.addEventListener('change', validarFechas);
		fin.addEventListener('change', validarFechas);
	}

	// MAPA Y UBICACIÓN

	const TIERRA_BLANCA_LAT = 18.4500;
	const TIERRA_BLANCA_LNG = -96.3500;

	if (typeof hasDbLocation === 'undefined') window.hasDbLocation = false;
	if (typeof dbLat === 'undefined') window.dbLat = null;
	if (typeof dbLng === 'undefined') window.dbLng = null;

	let map, marker;
	let isFetching = false;
	let loadingOverlay = null;
	const direccionCache = new Map();

	function debounce(func, wait) {
		let timeout;
		return function(...args) {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, args), wait);
		};
	}

	function mostrarLoading(mostrar) {
		if (!loadingOverlay) return;
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

	function actualizarCoordenadas(lat, lng) {
		document.getElementById('latInput').value = lat;
		document.getElementById('lngInput').value = lng;
	}

	async function obtenerComponentesDireccion(lat, lng) {
		try {
			const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`;
			const resp = await fetch(url, {
				headers: { 'User-Agent': 'MiAppActividades/1.0 (contacto@miapp.com)' }
			});
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
			console.warn('Error obteniendo componentes:', err);
			return null;
		}
	}

	async function obtenerCallesCercanasOverpass(lat, lng, radioMetros = 45) {
		const query = `[out:json];(way(around:${radioMetros},${lat},${lng})["highway"];);out body;`;
		const url = `https://overpass-api.de/api/interpreter?data=${encodeURIComponent(query)}`;
		try {
			const resp = await fetch(url, {
				headers: { 'User-Agent': 'MiAppActividades/1.0 (contacto@miapp.com)' }
			});
			if (!resp.ok) throw new Error(`Overpass HTTP ${resp.status}`);
			const data = await resp.json();
			const nombres = data.elements
				.map(el => el.tags?.name)
				.filter(name => name && typeof name === 'string' && name.trim() !== '');
			const unicos = [...new Set(nombres)];
			return unicos.slice(0, 2);
		} catch (err) {
			console.warn('Error en Overpass:', err);
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
		const partesRestantes = [
			componentes.postcode,
			componentes.city,
			componentes.state,
			componentes.country
		].filter(Boolean);
		return [parteCalles, ...partesRestantes].join(', ');
	}

	async function actualizarUIconDireccionYCalles(lat, lng) {
		const direccionAutoDiv = document.getElementById('direccionAuto');
		const direccionHidden = document.getElementById('direccionHidden');
		if (!direccionAutoDiv) return;
		const clave = `${lat},${lng}`;
		if (direccionCache.has(clave)) {
			const direccion = direccionCache.get(clave);
			direccionAutoDiv.textContent = direccion;
			if (direccionHidden) direccionHidden.value = direccion;
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
			direccionAutoDiv.innerHTML = 'Error al obtener dirección. Intenta de nuevo.';
		} finally {
			isFetching = false;
			mostrarLoading(false);
		}
	}

	const actualizarConDebounce = debounce(actualizarUIconDireccionYCalles, 1000);

	function iniciarMapa(lat, lng, mostrarMensajeInicialFlag = false) {
		if (map) return;
		map = L.map('map').setView([lat, lng], 13);
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
		map.on('click', async function(e) {
			if (isFetching) {
				alert('Espera a que termine la búsqueda de dirección.');
				return;
			}
			const lat = e.latlng.lat;
			const lng = e.latlng.lng;
			marker.setLatLng([lat, lng]);
			actualizarCoordenadas(lat, lng);
			await actualizarConDebounce(lat, lng);
		});
		marker.on('dragend', async function() {
			if (isFetching) {
				alert('Espera a que termine la búsqueda de dirección.');
				return;
			}
			const pos = marker.getLatLng();
			actualizarCoordenadas(pos.lat, pos.lng);
			await actualizarConDebounce(pos.lat, pos.lng);
		});
		if (mostrarMensajeInicialFlag) {
			const direccionAutoDiv = document.getElementById('direccionAuto');
			if (direccionAutoDiv) direccionAutoDiv.innerHTML = 'Presiona la ubicación de la actividad en el mapa';
		} else {
			actualizarUIconDireccionYCalles(lat, lng);
		}
	}

	if (hasDbLocation && dbLat !== null && dbLng !== null) {
		iniciarMapa(dbLat, dbLng, false);
	} else {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(pos) {
					iniciarMapa(pos.coords.latitude, pos.coords.longitude, false);
				},
				function() {
					iniciarMapa(TIERRA_BLANCA_LAT, TIERRA_BLANCA_LNG, true);
				}
			);
		} else {
			iniciarMapa(TIERRA_BLANCA_LAT, TIERRA_BLANCA_LNG, true);
		}
	}

	const btnGeo = document.getElementById('btnMiUbicacion');
	if (btnGeo) {
		btnGeo.addEventListener('click', function() {
			if (!navigator.geolocation) {
				alert("Geolocalización no soportada");
				return;
			}
			if (isFetching) {
				alert('Espera a que termine la operación actual.');
				return;
			}
			navigator.geolocation.getCurrentPosition(
				async function(pos) {
					const lat = pos.coords.latitude;
					const lng = pos.coords.longitude;
					map.setView([lat, lng], 15);
					marker.setLatLng([lat, lng]);
					actualizarCoordenadas(lat, lng);
					await actualizarUIconDireccionYCalles(lat, lng);
				},
				function(err) {
					alert("Error al obtener tu ubicación: " + err.message);
				}
			);
		});
	}

	const radioAuto = document.querySelector('input[name="modo_direccion"][value="auto"]');
	const radioManual = document.querySelector('input[name="modo_direccion"][value="manual"]');
	const autoContainer = document.getElementById('direccionAutoContainer');
	const manualContainer = document.getElementById('direccionManualContainer');
	const direccionManualInput = document.getElementById('direccionManual');
	const direccionHidden = document.getElementById('direccionHidden');

	radioAuto?.addEventListener('change', function() {
		if (this.checked) {
			autoContainer.classList.remove('hidden');
			manualContainer.classList.add('hidden');
			if (direccionManualInput.value.trim()) {
				direccionHidden.value = direccionManualInput.value.trim();
			} else if (marker) {
				const pos = marker.getLatLng();
				actualizarUIconDireccionYCalles(pos.lat, pos.lng);
			}
		}
	});

	radioManual?.addEventListener('change', function() {
		if (this.checked) {
			autoContainer.classList.add('hidden');
			manualContainer.classList.remove('hidden');
			if (!direccionManualInput.value.trim() && direccionHidden.value) {
				direccionManualInput.value = direccionHidden.value;
			}
		}
	});

	// Validación antes de enviar (modificada para incluir validaciones adicionales)
	const submitForm = document.querySelector('form');
	submitForm?.addEventListener('submit', function(e) {
		const modoAuto = radioAuto?.checked;
		const direccionAutoDiv = document.getElementById('direccionAuto');

		// Validaciones de límites y edad mínima por si acaso
		validarLimitesParticipantes();
		validarEdadMinima();

		if (modoAuto) {
			const texto = direccionAutoDiv?.textContent.trim() || '';
			if (texto === 'Presiona la ubicación de la actividad en el mapa' || texto === 'Obteniendo dirección...') {
				e.preventDefault();
				alert('Por favor, selecciona una ubicación en el mapa (haz clic o arrastra el marcador).');
				return;
			}
			if (direccionHidden) direccionHidden.value = texto;
		} else {
			if (!direccionManualInput.value.trim()) {
				e.preventDefault();
				alert('Por favor, escribe la dirección manualmente.');
				return;
			}
			direccionHidden.value = direccionManualInput.value.trim();
		}
	});
</script>
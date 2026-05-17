<script>
   document.addEventListener('DOMContentLoaded', function() {
		// --- Selección de tipo ---
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

		// --- Manejo de imagen: si ya hay imagen, ocultar input y mostrar botón cambiar ---
		const imageContainer = document.getElementById('imageContainer');
		const fotoInput = document.getElementById('fotoInput');
		if (imageContainer && imageContainer.querySelector('img')) {
			const btnCambiar = document.getElementById('btnCambiarImagen');
			if (btnCambiar) {
				btnCambiar.addEventListener('click', function() {
					fotoInput.click();
				});
			}
			fotoInput.addEventListener('change', function(e) {
				if (e.target.files.length > 0) {
					const file = e.target.files[0];
					const reader = new FileReader();
					reader.onload = function(ev) {
						const previewDiv = document.getElementById('previewNewImage');
						previewDiv.innerHTML = `<img src="${ev.target.result}" class="max-h-48 rounded-xl object-cover">`;
						previewDiv.classList.remove('hidden');
						// Opcional: ocultar la imagen anterior
						const oldImg = imageContainer.querySelector('img');
						if (oldImg) oldImg.style.display = 'none';
					};
					reader.readAsDataURL(file);
				}
			});
		}

		// --- Mapa y coordenadas ---
		<?php if (!$restricciones['bloquear_todo']): ?>
			var lat = <?= (float)($actividad['latitud'] ?? 18.4500) ?>;
			var lng = <?= (float)($actividad['longitud'] ?? -96.3500) ?>;
			var customIcon = L.divIcon({
				className: 'custom-div-icon',
				html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
				iconSize: [28, 28],
				popupAnchor: [0, -14]
			});
			var map = L.map('map').setView([lat, lng], 15);
			L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
			}).addTo(map);
			var marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);
			
			function actualizarCoordenadas(lat, lng) {
				document.getElementById('latInput').value = lat;
				document.getElementById('lngInput').value = lng;
				actualizarDireccion(lat, lng);
			}
			
			function actualizarDireccion(lat, lng) {
				var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
				fetch(url)
					.then(response => response.json())
					.then(data => {
						if (data && data.display_name) {
							document.getElementById('direccionMostrada').innerHTML = data.display_name;
							document.getElementById('direccionGuardada').value = data.display_name;
						} else {
							document.getElementById('direccionMostrada').innerHTML = 'Dirección no encontrada';
						}
					})
					.catch(() => {
						document.getElementById('direccionMostrada').innerHTML = 'No se pudo cargar la dirección';
					});
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
			
			// Botón mi ubicación
			document.getElementById('btnMiUbicacion').addEventListener('click', function() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(pos) {
						const newLat = pos.coords.latitude;
						const newLng = pos.coords.longitude;
						map.setView([newLat, newLng], 15);
						marker.setLatLng([newLat, newLng]);
						actualizarCoordenadas(newLat, newLng);
					}, function() {
						alert("No se pudo obtener tu ubicación.");
					});
				} else {
					alert("Geolocalización no soportada.");
				}
			});
			
			// Geocodificación manual
			document.getElementById('btnGeocodificar').addEventListener('click', function() {
				var direccion = document.getElementById('direccionManual').value;
				if (!direccion) return;
				fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}&limit=1`)
					.then(res => res.json())
					.then(data => {
						if (data && data.length > 0) {
							var newLat = parseFloat(data[0].lat);
							var newLng = parseFloat(data[0].lon);
							map.setView([newLat, newLng], 15);
							marker.setLatLng([newLat, newLng]);
							actualizarCoordenadas(newLat, newLng);
							document.getElementById('direccionManual').value = data[0].display_name;
						} else {
							alert("No se encontró la dirección.");
						}
					});
			});
			
			<?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
				marker.dragging.disable();
				map.dragging.disable();
				map.touchZoom.disable();
				map.scrollWheelZoom.disable();
				map.doubleClickZoom.disable();
			<?php endif; ?>
		<?php else: ?>
			document.getElementById('map').style.display = 'none';
		<?php endif; ?>

		// --- Confirmación antes de guardar si hay cambios críticos que cancelen solicitudes ---
		const form = document.getElementById('formEditarActividad');
		const confirmacionInput = document.getElementById('confirmar_cancelacion');
		const btnGuardar = document.getElementById('btnGuardar');
		
		// Detectar campos críticos (simplificado, se puede hacer más robusto)
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
			
			if (nuevaFecha !== camposOriginales.fecha_inicio ||
				nuevaLat !== camposOriginales.latitud ||
				nuevaLng !== camposOriginales.longitud ||
				nuevaPriv !== camposOriginales.privacidad ||
				nuevoMin !== camposOriginales.limite_min ||
				nuevoMax !== camposOriginales.limite_max) {
				camposCriticosModificados = true;
			} else {
				camposCriticosModificados = false;
			}
		}
		
		form.addEventListener('submit', function(e) {
			verificarCambiosCriticos();
			if (camposCriticosModificados && confirmacionInput.value !== '1') {
				e.preventDefault();
				if (confirm("Se modificaron campos que cancelarán todas las solicitudes pendientes de participación. ¿Deseas continuar?")) {
					confirmacionInput.value = '1';
					form.submit();
				}
			}
		});
		
		// --- Aceptar/Rechazar solicitudes mediante AJAX ---
		const btnAceptar = document.querySelectorAll('.btn-aceptar-solicitud');
		const btnRechazar = document.querySelectorAll('.btn-rechazar-solicitud');
		
		function responderSolicitud(userId, accion, elemento) {
			const idActividad = document.querySelector('input[name="id_actividad"]').value;
			fetch('<?= BASE_URL ?>?c=actividad&a=' + (accion === 'aceptar' ? 'aceptarSolicitud' : 'rechazarSolicitud'), {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `id_actividad=${idActividad}&id_usuario=${userId}`
			})
			.then(res => res.json())
			.then(data => {
				if (data.success) {
					elemento.closest('[data-user]').remove();
					mostrarToast('Solicitud ' + (accion === 'aceptar' ? 'aceptada' : 'rechazada'));
				} else {
					alert('Error al procesar la solicitud');
				}
			});
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
		
		function mostrarToast(mensaje) {
			// Implementación simple
			alert(mensaje);
		}
	});
</script>
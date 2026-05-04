<script>
	const actividades = <?= json_encode($actividades) ?>;
	const currentUserId = <?= $_SESSION['usuario_id'] ?? 0 ?>;
	const BASE_URL = "<?= BASE_URL ?>";
	const modoOscuro = <?= $_SESSION['modo_oscuro'] ?? ($_SESSION['ajustes']['modo_oscuro'] ?? 0) ?>;
	const capaSesion = <?= json_encode($_SESSION['capaMapa'] ?? null) ?>;
	const savedCenter = <?= isset($_SESSION['mapCenter']) ? json_encode($_SESSION['mapCenter']) : 'null' ?>;
	const savedZoom = <?= $_SESSION['mapZoom'] ?? 13 ?>;

	let mapInstance = null;
	let markerLayer = null;
	let userMarker = null;
	let currentFilter = "";
	let currentCategoria = "";
	
	console.log(actividades);
	//Colores de categorias (debo de modificar esto para que se haga dinamicamente) :p
	const categoriaColores = {
		'creativa y artística': '#e91e63',
		'bienestar y relajación': '#4caf50',
		'aire libre y naturaleza': '#2e7d32',
		'turismo y exploración': '#2196f3',
		'gastronomía': '#d84315',
		'social y comunitaria': '#9c27b0',
		'física y deportiva': '#ff9800',
		'entretenimiento y ocio': '#3f51b5',
		'intelectual y cultural': '#795548',
		'default': '#5a2af7'
	};

	function getSvgIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="${color}"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`;
	}

	function getIconForCategoria(categoria) {
		const catKey = categoria.toLowerCase();
		const color = categoriaColores[catKey] || categoriaColores['default'];
		return L.divIcon({
			className: 'custom-div-icon',
			html: getSvgIcon(color),
			iconSize: [28, 28],
			popupAnchor: [0, -14]
		});
	}

	const userIcon = L.divIcon({
		className: 'user-marker-icon',
		html: getSvgIcon('#2196F3'),
		iconSize: [28, 28],
		popupAnchor: [0, -14]
	});

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

	function formatearHora12(hora) {
		if (!hora) return '';
		const partes = hora.split(':');
		let horas = parseInt(partes[0]);
		const minutos = partes[1];
		const periodo = horas >= 12 ? 'PM' : 'AM';
		horas = horas % 12;
		if (horas === 0) horas = 12;
		return `${horas}:${minutos} ${periodo}`;
	}

	window.verDetalle = function(id) {
		window.location.href = BASE_URL + "?c=actividad&a=detalle&id=" + id;
	};
</script>
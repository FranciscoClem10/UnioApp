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

	// Colores de categorias (dinámicos)
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
		'default': '#ab1ce4'
	};

	// ========== ICONOS SVG CON COLOR DINÁMICO ==========
	function getCreativaIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M576 320C576 320.9 576 321.8 576 322.7C575.6 359.2 542.4 384 505.9 384L408 384C381.5 384 360 405.5 360 432C360 435.4 360.4 438.7 361 441.9C363.1 452.1 367.5 461.9 371.8 471.8C377.9 485.6 383.9 499.3 383.9 513.8C383.9 545.6 362.3 574.5 330.5 575.8C327 575.9 323.5 576 319.9 576C178.5 576 63.9 461.4 63.9 320C63.9 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320zM192 352C192 334.3 177.7 320 160 320C142.3 320 128 334.3 128 352C128 369.7 142.3 384 160 384C177.7 384 192 369.7 192 352zM192 256C209.7 256 224 241.7 224 224C224 206.3 209.7 192 192 192C174.3 192 160 206.3 160 224C160 241.7 174.3 256 192 256zM352 160C352 142.3 337.7 128 320 128C302.3 128 288 142.3 288 160C288 177.7 302.3 192 320 192C337.7 192 352 177.7 352 160zM448 256C465.7 256 480 241.7 480 224C480 206.3 465.7 192 448 192C430.3 192 416 206.3 416 224C416 241.7 430.3 256 448 256z"/></svg>`;
	}

	function getBienestarIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M201.7 291.5C236 310.2 266.2 335.6 290.5 366C301.6 379.9 311.5 394.9 319.9 410.8C328.4 394.9 338.2 380 349.3 366C373.6 335.5 403.8 310.1 438.1 291.5C479.8 268.8 527.5 256 577.8 256L587.7 256C598.8 256 607.8 265 607.8 276.1C607.8 424.1 487.9 544 339.9 544L299.7 544C151.9 544 32 424.1 32 276.1C32 265 41 256 52.1 256L62 256C112.4 256 160.1 268.8 201.7 291.5zM335.9 102C352.8 117.9 397.3 165.4 424.9 244.3C384.8 264.5 349.1 292.5 320 326.4C290.8 292.5 255.2 264.6 215.1 244.3C242.7 165.4 287.3 117.9 304.1 102C308.4 97.9 314.1 96 320 96C325.9 96 331.6 98 335.9 102z"/></svg>`;
	}

	function getAireLibreIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M576 96C576 204.1 499.4 294.3 397.6 315.4C389.7 257.3 363.6 205 325.1 164.5C365.2 104 433.9 64 512 64L544 64C561.7 64 576 78.3 576 96zM64 160C64 142.3 78.3 128 96 128L128 128C251.7 128 352 228.3 352 352L352 544C352 561.7 337.7 576 320 576C302.3 576 288 561.7 288 544L288 384C164.3 384 64 283.7 64 160z"/></svg>`;
	}

	function getTurismoIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M552 264C582.9 264 608 289.1 608 320C608 350.9 582.9 376 552 376L424.7 376L265.5 549.6C259.4 556.2 250.9 560 241.9 560L198.2 560C187.3 560 179.6 549.3 183 538.9L237.3 376L137.6 376L84.8 442C81.8 445.8 77.2 448 72.3 448L52.5 448C42.1 448 34.5 438.2 37 428.1L64 320L37 211.9C34.4 201.8 42.1 192 52.5 192L72.3 192C77.2 192 81.8 194.2 84.8 198L137.6 264L237.3 264L183 101.1C179.6 90.7 187.3 80 198.2 80L241.9 80C250.9 80 259.4 83.8 265.5 90.4L424.7 264L552 264z"/></svg>`;
	}

	function getGastronomiaIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M127.9 78.4C127.1 70.2 120.2 64 112 64C103.8 64 96.9 70.2 96 78.3L81.9 213.7C80.6 219.7 80 225.8 80 231.9C80 277.8 115.1 315.5 160 319.6L160 544C160 561.7 174.3 576 192 576C209.7 576 224 561.7 224 544L224 319.6C268.9 315.5 304 277.8 304 231.9C304 225.8 303.4 219.7 302.1 213.7L287.9 78.3C287.1 70.2 280.2 64 272 64C263.8 64 256.9 70.2 256.1 78.4L242.5 213.9C241.9 219.6 237.1 224 231.4 224C225.6 224 220.8 219.6 220.2 213.8L207.9 78.6C207.2 70.3 200.3 64 192 64C183.7 64 176.8 70.3 176.1 78.6L163.8 213.8C163.3 219.6 158.4 224 152.6 224C146.8 224 142 219.6 141.5 213.9L127.9 78.4zM512 64C496 64 384 96 384 240L384 352C384 387.3 412.7 416 448 416L480 416L480 544C480 561.7 494.3 576 512 576C529.7 576 544 561.7 544 544L544 96C544 78.3 529.7 64 512 64z"/></svg>`;
	}

	function getSocialIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M320 64C355.3 64 384 92.7 384 128C384 163.3 355.3 192 320 192C284.7 192 256 163.3 256 128C256 92.7 284.7 64 320 64zM416 376C416 401 403.3 423 384 435.9L384 528C384 554.5 362.5 576 336 576L304 576C277.5 576 256 554.5 256 528L256 435.9C236.7 423 224 401 224 376L224 336C224 283 267 240 320 240C373 240 416 283 416 336L416 376zM160 96C190.9 96 216 121.1 216 152C216 182.9 190.9 208 160 208C129.1 208 104 182.9 104 152C104 121.1 129.1 96 160 96zM176 336L176 368C176 400.5 188.1 430.1 208 452.7L208 528C208 529.2 208 530.5 208.1 531.7C199.6 539.3 188.4 544 176 544L144 544C117.5 544 96 522.5 96 496L96 439.4C76.9 428.4 64 407.7 64 384L64 352C64 299 107 256 160 256C172.7 256 184.8 258.5 195.9 262.9C183.3 284.3 176 309.3 176 336zM432 528L432 452.7C451.9 430.2 464 400.5 464 368L464 336C464 309.3 456.7 284.4 444.1 262.9C455.2 258.4 467.3 256 480 256C533 256 576 299 576 352L576 384C576 407.7 563.1 428.4 544 439.4L544 496C544 522.5 522.5 544 496 544L464 544C451.7 544 440.4 539.4 431.9 531.7C431.9 530.5 432 529.2 432 528zM480 96C510.9 96 536 121.1 536 152C536 182.9 510.9 208 480 208C449.1 208 424 182.9 424 152C424 121.1 449.1 96 480 96z"/></svg>`;
	}

	function getDeporteIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M481.3 424.1L409.7 419.3C404.5 419 399.4 420.4 395.2 423.5C391 426.6 388 430.9 386.8 436L369.2 505.6C353.5 509.8 337 512 320 512C303 512 286.5 509.8 270.8 505.6L253.2 436C251.9 431 248.9 426.6 244.8 423.5C240.7 420.4 235.5 419 230.3 419.3L158.7 424.1C141.1 396.9 130.2 364.9 128.3 330.5L189 292.3C193.4 289.5 196.6 285.3 198.2 280.4C199.8 275.5 199.6 270.2 197.7 265.4L171 198.8C192 173.2 219.3 153 250.7 140.9L305.9 186.9C309.9 190.2 314.9 192 320 192C325.1 192 330.2 190.2 334.1 186.9L389.3 140.9C420.6 153 448 173.2 468.9 198.8L442.2 265.4C440.3 270.2 440.1 275.5 441.7 280.4C443.3 285.3 446.6 289.5 450.9 292.3L511.6 330.5C509.7 364.9 498.8 396.9 481.2 424.1zM320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM334.1 250.3C325.7 244.2 314.3 244.2 305.9 250.3L258 285C249.6 291.1 246.1 301.9 249.3 311.8L267.6 368.1C270.8 378 280 384.7 290.4 384.7L349.6 384.7C360 384.7 369.2 378 372.4 368.1L390.7 311.8C393.9 301.9 390.4 291.1 382 285L334.1 250.2z"/></svg>`;
	}

	function getEntretenimientoIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M448 128C554 128 640 214 640 320C640 426 554 512 448 512L192 512C86 512 0 426 0 320C0 214 86 128 192 128L448 128zM192 240C178.7 240 168 250.7 168 264L168 296L136 296C122.7 296 112 306.7 112 320C112 333.3 122.7 344 136 344L168 344L168 376C168 389.3 178.7 400 192 400C205.3 400 216 389.3 216 376L216 344L248 344C261.3 344 272 333.3 272 320C272 306.7 261.3 296 248 296L216 296L216 264C216 250.7 205.3 240 192 240zM432 336C414.3 336 400 350.3 400 368C400 385.7 414.3 400 432 400C449.7 400 464 385.7 464 368C464 350.3 449.7 336 432 336zM496 240C478.3 240 464 254.3 464 272C464 289.7 478.3 304 496 304C513.7 304 528 289.7 528 272C528 254.3 513.7 240 496 240z"/></svg>`;
	}

	function getIntelectualIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z"/></svg>`;
	}

	function getDefaultIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}"><path d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z"/></svg>`;
	}

	function getUserIcon(color) {
		return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="${color}" stroke="#ffffff" stroke-width="3" stroke-linejoin="round"><path d="M320 64C355.3 64 384 92.7 384 128C384 163.3 355.3 192 320 192C284.7 192 256 163.3 256 128C256 92.7 284.7 64 320 64zM288 224L352 224C387.3 224 416 252.7 416 288L416 336C416 353.7 401.7 368 384 368L382.2 368L371.1 467.5C369.3 483.7 355.6 496 339.3 496L300.6 496C284.3 496 270.6 483.7 268.8 467.5L257.7 368L255.9 368C238.2 368 223.9 353.7 223.9 336L223.9 288C223.9 252.7 252.6 224 287.9 224zM476.4 464.2C460.3 460 441.6 456.6 421 454L426.3 406.3C449 409.2 470 413 488.4 417.8C510.8 423.6 531 431.1 546.2 441.1C560.9 450.7 576 466 576 488.1C576 510.2 560.9 525.5 546.2 535.1C531 545 510.7 552.6 488.4 558.4C443.3 570.1 383.1 576.2 320 576.2C256.9 576.2 196.7 570.1 151.6 558.4C129.2 552.4 109 544.9 93.8 535C79.1 525.4 64 510.1 64 488C64 465.9 79.1 450.6 93.8 441C109 431.1 129.3 423.5 151.6 417.7C170.1 412.9 191.1 409.1 213.7 406.2L219 454C198.4 456.6 179.7 460.1 163.6 464.2C107 478.8 107 497.1 163.6 511.7C203.5 522 259.4 527.9 320 527.9C380.6 527.9 436.5 522 476.4 511.7C533 497.1 533 478.8 476.4 464.2z"/></svg>`;
	}

	function getIconForCategoria(categoria) {
		const catKey = categoria.toLowerCase();
		const color = categoriaColores[catKey] || categoriaColores['default'];
		let svgHtml = '';

		switch (catKey) {
			case 'creativa y artística':
				svgHtml = getCreativaIcon(color);
				break;
			case 'bienestar y relajación':
				svgHtml = getBienestarIcon(color);
				break;
			case 'aire libre y naturaleza':
				svgHtml = getAireLibreIcon(color);
				break;
			case 'turismo y exploración':
				svgHtml = getTurismoIcon(color);
				break;
			case 'gastronomía':
				svgHtml = getGastronomiaIcon(color);
				break;
			case 'social y comunitaria':
				svgHtml = getSocialIcon(color);
				break;
			case 'física y deportiva':
				svgHtml = getDeporteIcon(color);
				break;
			case 'entretenimiento y ocio':
				svgHtml = getEntretenimientoIcon(color);
				break;
			case 'intelectual y cultural':
				svgHtml = getIntelectualIcon(color);
				break;
			default:
				svgHtml = getDefaultIcon(color);
				break;
		}

		return L.divIcon({
			className: 'custom-div-icon',
			html: svgHtml,
			iconSize: [28, 28],
			popupAnchor: [0, -14]
		});
	}

	const userIcon = L.divIcon({
		className: 'user-marker-icon',
		html: getUserIcon('#5a2af7'),
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
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}

$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unio | Mis Actividades</title>
    <!-- Tailwind CSS + plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts + Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-surface": "#2d2f2f",
                        "surface-tint": "#5a2af7",
                        "outline": "#767777",
                        "on-surface-variant": "#5a5c5c",
                        "secondary-fixed": "#ddc8ff",
                        "on-primary-container": "#220076",
                        "on-tertiary-container": "#63033b",
                        "background": "#f6f6f6",
                        "on-secondary-fixed-variant": "#603aa2",
                        "surface-bright": "#f6f6f6",
                        "secondary-container": "#ddc8ff",
                        "tertiary-fixed": "#ff8cbd",
                        "tertiary-dim": "#8c2a5b",
                        "inverse-on-surface": "#9c9d9d",
                        "secondary-fixed-dim": "#d2b8ff",
                        "inverse-primary": "#927dff",
                        "on-error-container": "#510017",
                        "surface-container": "#e7e8e8",
                        "tertiary-container": "#ff8cbd",
                        "primary-dim": "#4e0bec",
                        "on-tertiary-fixed": "#37001e",
                        "on-secondary-container": "#563098",
                        "error": "#b41340",
                        "surface-dim": "#d3d5d5",
                        "on-tertiary-fixed-variant": "#6f1044",
                        "primary": "#5a2af7",
                        "on-tertiary": "#ffeff2",
                        "surface-container-low": "#f0f1f1",
                        "inverse-surface": "#0c0f0f",
                        "secondary": "#6b46ae",
                        "on-error": "#ffefef",
                        "on-secondary-fixed": "#431783",
                        "surface-variant": "#dbdddd",
                        "primary-container": "#a292ff",
                        "on-primary": "#f6f0ff",
                        "on-background": "#2d2f2f",
                        "error-container": "#f74b6d",
                        "on-primary-fixed-variant": "#2b0090",
                        "tertiary": "#9b3667",
                        "outline-variant": "#acadad",
                        "surface-container-highest": "#dbdddd",
                        "secondary-dim": "#5f39a1",
                        "primary-fixed": "#a292ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-high": "#e1e3e3",
                        "surface": "#f6f6f6",
                        "on-primary-fixed": "#000000",
                        "on-secondary": "#f9efff",
                        "primary-fixed-dim": "#9581ff",
                        "tertiary-fixed-dim": "#f27db0",
                        "error-dim": "#a70138"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    fontFamily: {
                        headline: ["Plus Jakarta Sans"],
                        display: ["Plus Jakarta Sans"],
                        body: ["Plus Jakarta Sans"],
                        label: ["Plus Jakarta Sans"]
                    }
                }
            }
        }
    </script>
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .kinetic-gradient {
        background: linear-gradient(135deg, #5a2af7 0%, #a292ff 100%);
    }
    .glass-effect {
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    /* EVITAR SCROLL HORIZONTAL EN LA PÁGINA */
    html, body {
        height: 100%;
        overflow-x: hidden;
    }
    body {
        overflow-y: auto;
    }

    /* CONTENEDOR DE LA TABLA: scroll horizontal DENTRO */
    .overflow-x-auto {
        overflow-x: auto;
        overflow-y: visible;
        scrollbar-width: thin;
        scrollbar-color: #5a2af7 #f0f1f1;
    }

    /* SCROLLS MORADOS */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f0f1f1;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: #5a2af7;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #4e0bec;
    }
    * {
        scrollbar-width: thin;
        scrollbar-color: #5a2af7 #f0f1f1;
    }

    /* Botón deshabilitado */
    .btn-disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>
</head>
<body class="bg-background text-on-surface selection:bg-primary-container selection:text-on-primary-container">
    <?php include 'includes/top-nav.php';?>

    <main class="flex-1 px-8 py-12 max-w-7xl mx-auto">
        <header class="mb-12">
            <br>
            <br>
            <h1 class="text-[3.5rem] font-extrabold tracking-tight text-on-surface leading-tight mb-4">
                Gestión de
                <span class="text-primary">Actividades</span>
            </h1>
            <p class="text-on-surface-variant text-lg max-w-2xl leading-relaxed">
                Administra y supervisa el ciclo de vida completo de tus actividades programadas.
            </p>
        </header>

        <!-- Mensajes de sesión -->
        <?php if (isset($_SESSION['exito_edicion'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200">
                <?= htmlspecialchars($_SESSION['exito_edicion']) ?>
            </div>
            <?php unset($_SESSION['exito_edicion']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_edicion'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
                <?= htmlspecialchars($_SESSION['error_edicion']) ?>
            </div>
            <?php unset($_SESSION['error_edicion']); ?>
        <?php endif; ?>

        <!-- Tarjeta de la tabla -->
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] overflow-hidden">
            <div class="px-6 py-6 bg-surface-container-low flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-bold text-on-surface">Historial de Actividades</h2>
                <div class="flex gap-2">
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-on-surface-variant text-lg">search</span>
                        <input type="text" id="buscadorActividades" class="pl-10 pr-4 py-2 bg-surface-container-highest text-on-surface text-sm font-medium rounded-full border-none focus:ring-2 focus:ring-primary/20 w-64 transition-all" placeholder="Buscar actividades...">
                    </div>
                    <button id="btnFiltrar" class="px-4 py-2 bg-surface-container-highest text-on-surface text-sm font-medium rounded-full hover:opacity-80 transition-opacity">Buscar</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <?php if (empty($actividades)): ?>
                    <div class="text-center py-16 text-on-surface-variant">
                        <span class="material-symbols-outlined text-5xl mb-3 opacity-50">event_busy</span>
                        <p class="text-lg">No has creado ninguna actividad aún.</p>
                        <a href="<?= BASE_URL ?>?c=dashboard" class="inline-block mt-4 text-primary hover:underline">← Volver al dashboard</a>
                    </div>
                <?php else: ?>
                    <table class="w-full text-left border-collapse" id="tablaActividades">
                        <thead>
                            <tr class="bg-surface-container-lowest border-b border-outline-variant/10">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nombre</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Categoría</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Inicio</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <?php foreach ($actividades as $act): ?>
                                <?php
                                    $estado_clases = [
                                        'pendiente'   => 'text-outline',
                                        'en_curso'    => 'text-primary',
                                        'finalizada'  => 'text-error',
                                        'cancelada'   => 'text-error'
                                    ];
                                    $dot_pulse = ($act['estado'] === 'finalizada') ? 'animate-pulse' : '';
                                    $estado_texto = ucfirst($act['estado']);
                                    $estado_clase = $estado_clases[$act['estado']] ?? 'text-outline';
                                    $eliminar_habilitado = in_array($act['estado'], ['finalizada', 'cancelada']);
                                ?>
                                <tr class="hover:bg-surface-container-low/50 transition-colors fila-actividad">
                                    <td class="px-6 py-5 font-semibold text-on-surface whitespace-nowrap"><?= htmlspecialchars($act['titulo']) ?></td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full">
                                            <?= htmlspecialchars($act['categoria']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-on-surface-variant whitespace-nowrap"><?= htmlspecialchars($act['fecha'] ?? 'Por definir') ?></td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-2 font-medium text-xs <?= $estado_clase ?>">
                                            <span class="w-2 h-2 rounded-full bg-current <?= $dot_pulse ?>"></span>
                                            <?= $estado_texto ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <!-- Botón editar -->
                                            <a href="<?= BASE_URL ?>?c=actividad&a=editar&id=<?= $act['id_actividad'] ?>" class="p-2 hover:bg-surface-container-high rounded-lg text-on-surface-variant transition-all" title="Editar">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </a>
                                            <!-- Botón fact_check (siempre visible) -->
                                            <button onclick="window.location.href='../Pages/GestionActividad.html'" class="p-2 hover:bg-surface-container-high rounded-lg text-on-surface-variant transition-all" title="Verificar">
                                                <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                            </button>
                                            <!-- Botón eliminar: siempre visible pero desactivado si no corresponde -->
                                            <?php if ($eliminar_habilitado): ?>
                                                <a href="<?= BASE_URL ?>?c=actividad&a=eliminarActividad&id=<?= $act['id_actividad'] ?>" class="p-2 hover:bg-surface-container-high rounded-lg text-on-surface-variant hover:text-red-600 hover:bg-red-100 transition-all" title="Eliminar" onclick="return confirm('¿Eliminar definitivamente esta actividad?')">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="p-2 rounded-lg text-on-surface-variant btn-disabled" title="Solo se pueden eliminar actividades finalizadas o canceladas">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <br>
        <br>
    </main>

    <?php include 'includes/bottom-nav.php'; ?>

<script>
    function cerrarSesion() {
        window.location.href = "<?= BASE_URL ?>?c=login&a=logout";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscadorActividades');
        const btnFiltrar = document.getElementById('btnFiltrar');
        const tabla = document.getElementById('tablaActividades');
        if (!tabla) return;

        // Crear el mensaje de "sin resultados" una sola vez, oculto inicialmente
        let mensajeNoResultados = tabla.parentNode.querySelector('.no-resultados');
        if (!mensajeNoResultados) {
            mensajeNoResultados = document.createElement('div');
            mensajeNoResultados.className = 'no-resultados text-center py-8 text-on-surface-variant';
            mensajeNoResultados.innerHTML = '<span class="material-symbols-outlined text-4xl mb-2">search_off</span><p>No se encontraron actividades</p>';
            mensajeNoResultados.style.display = 'none';
            tabla.parentNode.appendChild(mensajeNoResultados);
        }

        function filtrarActividades() {
            const filtro = inputBuscar.value.toLowerCase().trim();
            const filas = tabla.querySelectorAll('tbody .fila-actividad');
            let algunaVisible = false;
            filas.forEach(fila => {
                const celdas = fila.querySelectorAll('td');
                if (celdas.length >= 3) {
                    const nombre = celdas[0].textContent.toLowerCase();
                    const categoria = celdas[1].textContent.toLowerCase();
                    const fecha = celdas[2].textContent.toLowerCase();
                    const coincide = nombre.includes(filtro) || categoria.includes(filtro) || fecha.includes(filtro);
                    fila.style.display = coincide ? '' : 'none';
                    if (coincide) algunaVisible = true;
                }
            });
            // Mostrar u ocultar el mensaje estático
            mensajeNoResultados.style.display = algunaVisible ? 'none' : 'block';
        }

        inputBuscar.addEventListener('input', filtrarActividades);
        btnFiltrar.addEventListener('click', filtrarActividades);
    });
</script>
</body>
</html>
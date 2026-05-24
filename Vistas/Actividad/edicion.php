<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}

if (!isset($actividades)) {
    $actividades = [];
}

include 'includes/header.php';
?>

<?php include 'includes/top-nav.php'; ?>

<!-- Contenedor con scroll para el contenido principal -->
<div class="flex-1 overflow-y-auto px-8 py-6 max-w-7xl mx-auto w-full">
    
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
            <div class="mb-8 text-center md:text-left">
                <h1 class="text-[3.5rem] font-extrabold tracking-tight text-on-surface leading-tight mb-4">
                    Edición de
                    <span class="text-primary">Actividades</span>
                </h1>
                <p class="text-on-surface-variant max-w-xl">Edita tus actividades para tu comunidad.</p>
            </div>
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
                    <p class="text-lg">No tienes ninguna actividad como creador u organizador.</p>
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
                                $es_creador = ($act['rol'] === 'creador');
                                $es_organizador = ($act['rol'] === 'organizador');
                                $eliminar_habilitado = $es_creador && in_array($act['estado'], ['finalizada', 'cancelada']);
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
                                    <!-- Contenedor con ancho mínimo para mantener la posición -->
                                    <div class="flex justify-end gap-2 min-w-[120px]">
                                        <!-- Eliminar: solo para creador y estado finalizada/cancelada -->
                                        <?php if ($eliminar_habilitado): ?>
                                            <a href="<?= BASE_URL ?>?c=actividad&a=eliminarActividad&id=<?= $act['id_actividad'] ?>" 
                                               class="p-2 hover:bg-surface-container-high rounded-lg text-on-surface-variant hover:text-red-600 hover:bg-red-100 transition-all" 
                                               title="Eliminar" 
                                               onclick="return confirm('¿Eliminar definitivamente esta actividad?')">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </a>
                                        <?php elseif ($es_creador): ?>
                                            <span class="p-2 rounded-lg text-on-surface-variant opacity-50 cursor-not-allowed" 
                                                  title="Solo se pueden eliminar actividades finalizadas o canceladas">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </span>
                                        <?php endif; ?>

                                        <!-- Editar: solo para creador -->
                                        <?php if ($es_creador): ?>
                                            <a href="<?= BASE_URL ?>?c=actividad&a=editar&id=<?= $act['id_actividad'] ?>" 
                                               class="p-2 hover:bg-surface-container-high rounded-lg text-on-surface-variant transition-all" 
                                               title="Editar">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Gestionar: para creador u organizador (siempre presente para ambos roles) -->
                                        <?php if ($es_creador || $es_organizador): ?>
                                            <a href="<?= BASE_URL ?>?c=gestionActividad&a=index&id=<?= $act['id_actividad'] ?>" 
                                               class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-all" 
                                               title="Gestionar actividad">
                                                <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                            </a>
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
</div>

<script>
    function cerrarSesion() {
        window.location.href = "<?= BASE_URL ?>?c=login&a=logout";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscadorActividades');
        const btnFiltrar = document.getElementById('btnFiltrar');
        const tabla = document.getElementById('tablaActividades');
        if (!tabla) return;

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
            mensajeNoResultados.style.display = algunaVisible ? 'none' : 'block';
        }

        inputBuscar.addEventListener('input', filtrarActividades);
        btnFiltrar.addEventListener('click', filtrarActividades);
    });
</script>

<?php
include 'includes/bottom-nav.php';
?>
<?php
include 'includes/header.php';
include 'includes/top-nav.php';
?>

<div class="flex-1 overflow-y-auto">
    <!-- Hero Header (sin cambios) -->
    <div class="relative">
        <div class="h-64 md:h-80 w-full overflow-hidden relative">
            <div class="w-full h-full bg-[#5a2af7]"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 md:px-12 -mt-16 relative z-10">
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.06)] flex flex-col md:flex-row items-center md:items-end gap-6">
                <!-- Foto de Perfil -->
                <div class="relative">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-3xl overflow-hidden border-4 border-white shadow-xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600">
                        <?php if ($usuario['foto_base64']): ?>
                            <img src="<?= $usuario['foto_base64'] ?>" alt="Foto de perfil" class="w-full h-full object-cover"/>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-white">person</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-black font-headline tracking-tight text-on-surface">
                                <?= htmlspecialchars($usuario['nombre_completo'] ?? $usuario['nombre']) ?>
                            </h1>
                            <div class="text-on-surface-variant font-medium text-sm mt-1">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                <span id="direccion-usuario">
                                    <?= htmlspecialchars($usuario['ubicacion'] ?? 'Obteniendo ubicación...') ?>
                                </span>
                            </div>
                            <?php
                                $fecha_nac = new DateTime($usuario['fecha_nacimiento']);
                                $hoy = new DateTime();
                                $edad = $hoy->diff($fecha_nac)->y;
                            ?>
                            <p class="text-on-surface-variant font-medium"><?= $edad ?> años</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="<?= BASE_URL ?>?c=perfil&a=editar" class="px-6 py-2.5 bg-surface-container-low text-primary font-bold rounded-xl border border-primary/10 flex items-center gap-2 hover:bg-surface-container-high transition-colors active:scale-95">
                                <span class="material-symbols-outlined text-lg">edit</span>
                                Editar perfil
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-center md:justify-start gap-12 mt-6 pt-6 border-t border-outline-variant/10">
                        <div class="flex flex-col items-center md:items-start">
                            <span class="text-2xl font-black text-on-surface"><?= $total_amigos ?></span>
                            <span class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Conexiones</span>
                        </div>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="text-2xl font-black text-on-surface"><?= $total_actividades ?></span>
                            <span class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Actividades</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-6xl mx-auto px-6 md:px-12 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8 pb-24">
        <!-- Left Column -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Sobre mí -->
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <h2 class="text-xl font-bold font-headline mb-4 text-on-surface">Sobre mí</h2>
                <p class="text-on-surface-variant leading-relaxed text-sm">
                    <?= nl2br(htmlspecialchars($usuario['biografia'] ?? '')) ?: "Sin biografía" ?>
                </p>
            </section>

            <!-- NUEVO: Amigos -->
            <?php if (!empty($amigos)): ?>
                <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                    <h2 class="text-xl font-bold font-headline mb-6 text-on-surface">Amigos</h2>
                    <div class="flex items-center gap-4 mb-4">
                        <?php foreach (array_slice($amigos, 0, 4) as $amigo): ?>
                            <div class="relative">
                                <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow">
                                    <?php if (!empty($amigo['foto_base64'])): ?>
                                        <img src="<?= $amigo['foto_base64'] ?>" 
                                             alt="<?= htmlspecialchars($amigo['nombre_completo']) ?>" 
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-violet-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white text-2xl">person</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= BASE_URL ?>?c=amigos&a=index" 
                       class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-surface-container-low text-primary font-bold rounded-xl border border-primary/10 hover:bg-surface-container-high transition-colors text-sm gap-2">
                        <span class="material-symbols-outlined text-lg">groups</span>
                        Ver todas las conexiones
                    </a>
                </section>
            <?php endif; ?>

            <!-- Intereses -->
            <section class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_8px_32px_rgba(45,47,47,0.02)]">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold font-headline text-on-surface">Mis Intereses</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if (!empty($intereses_nombres)): ?>
                        <?php foreach ($intereses_nombres as $interes): ?>
                            <span class="px-4 py-2 bg-primary/10 text-primary text-xs font-bold rounded-lg">
                                <?= htmlspecialchars($interes) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-on-surface-variant">No has seleccionado intereses todavía.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Right Column: Actividades (sin cambios) -->
        <div class="lg:col-span-8 space-y-8">
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black font-headline tracking-tight text-on-surface">Mis Actividades</h2>
                    <div class="bg-surface-container-low p-1 rounded-xl flex gap-1">
                        <button id="btn-proximas" class="px-5 py-1.5 bg-white shadow-sm text-primary font-bold text-sm rounded-lg transition-all" onclick="toggleActivities('proximas')">Próximas</button>
                        <button id="btn-pasadas" class="px-5 py-1.5 text-on-surface-variant hover:text-on-surface font-medium text-sm rounded-lg transition-all" onclick="toggleActivities('pasadas')">Pasadas</button>
                    </div>
                </div>

                <div id="container-proximas" class="grid grid-cols-1 gap-6">
                    <?php if (empty($actividades_proximas)): ?>
                        <p class="text-on-surface-variant text-sm">No tienes actividades próximas.</p>
                    <?php else: ?>
                        <?php foreach ($actividades_proximas as $actividad): ?>
                            <?php include 'tarjeta_actividad.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="container-pasadas" class="grid grid-cols-1 gap-6 hidden">
                    <?php if (empty($actividades_pasadas)): ?>
                        <p class="text-on-surface-variant text-sm">No tienes actividades pasadas.</p>
                    <?php else: ?>
                        <?php foreach ($actividades_pasadas as $actividad): ?>
                            <?php include 'tarjeta_actividad.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include 'includes/bottom-nav.php'; ?>

<script>
    function toggleActivities(type) {
        const containerProximas = document.getElementById('container-proximas');
        const containerPasadas = document.getElementById('container-pasadas');
        const btnProximas = document.getElementById('btn-proximas');
        const btnPasadas = document.getElementById('btn-pasadas');

        if (type === 'proximas') {
            containerProximas.classList.remove('hidden');
            containerPasadas.classList.add('hidden');
            btnProximas.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnProximas.classList.remove('text-on-surface-variant');
            btnPasadas.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnPasadas.classList.add('text-on-surface-variant');
        } else {
            containerProximas.classList.add('hidden');
            containerPasadas.classList.remove('hidden');
            btnPasadas.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnPasadas.classList.remove('text-on-surface-variant');
            btnProximas.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnProximas.classList.add('text-on-surface-variant');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleActivities('proximas');
    });

    // Geocodificación inversa (sin cambios)
    document.addEventListener("DOMContentLoaded", async function () {
        const lat = <?= json_encode($usuario['latitud']) ?>;
        const lng = <?= json_encode($usuario['longitud']) ?>;
        const direccionSpan = document.getElementById("direccion-usuario");

        if (!lat || !lng) {
            direccionSpan.textContent = "Ubicación no disponible";
            return;
        }

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
            );
            const data = await response.json();
            if (data.address) {
                const address = data.address;
                const direccion = [
                    address.road,
                    address.suburb,
                    address.city || address.town || address.village,
                    address.state,
                    address.country
                ].filter(Boolean).join(", ");
                direccionSpan.textContent = direccion || data.display_name;
            } else {
                direccionSpan.textContent = "Dirección no encontrada";
            }
        } catch (error) {
            console.error("Error obteniendo dirección:", error);
            direccionSpan.textContent = "Error al obtener ubicación";
        }
    });
</script>
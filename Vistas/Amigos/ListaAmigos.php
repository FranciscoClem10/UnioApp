<?php
// Variables: $usuario, $nombreCompleto, $amigos, $esPropietario, $id_perfil, $BASE_URL
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/top-nav.php'; ?>

<main class="pt-28 pb-12 px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <header class="mb-12">
            <h1 class="text-4xl font-extrabold tracking-tight text-on-surface mb-2">
                Conexiones de <span class="text-primary"><?= htmlspecialchars($nombreCompleto) ?></span>
            </h1>
            <p class="text-on-surface-variant text-lg">
                <?= $esPropietario ? 'Tus amigos en Unio.' : 'Amigos de ' . htmlspecialchars($nombreCompleto) . ' en la red.' ?>
            </p>
        </header>

        <!-- Search and Filter Section (optional but nice) -->
        <section class="mb-12 bg-surface-container-lowest p-6 rounded-xl shadow-[0_8px_32px_0_rgba(45,47,47,0.04)]">
            <div class="flex flex-col lg:flex-row gap-6 items-center">
                <div class="relative w-full lg:flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <input id="search-friends" class="w-full pl-12 pr-4 py-4 bg-surface-container-low rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-outline" placeholder="Buscar amigos por nombre..." type="text"/>
                </div>
            </div>
        </section>

        <!-- Connection Grid -->
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8" id="friends-container">
            <?php if (empty($amigos)): ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-on-surface-variant">Este usuario aún no tiene amigos.</p>
                </div>
            <?php else: ?>
                <?php foreach ($amigos as $amigo): ?>
                <article class="friend-card bg-surface-container-lowest rounded-xl p-6 transition-all duration-300 hover:shadow-[0_20px_48px_0_rgba(90,42,247,0.08)] group relative" data-name="<?= strtolower(htmlspecialchars($amigo['nombre_completo'])) ?>">
                    <div class="absolute top-4 right-4 z-10">
                        <button class="p-2 text-outline hover:text-primary hover:bg-primary/5 rounded-full transition-colors">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                        <div class="hidden absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl shadow-xl border border-surface-container py-2 z-20 group-focus-within:block">
                            <a href="<?= BASE_URL ?>?c=amigos&a=verPerfil&id=<?= $amigo['id_usuario'] ?>" class="w-full px-6 py-2.5 text-black font-medium rounded-xl hover:bg-surface-container-low hover:text-primary transition-colors flex items-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                                Ver perfil
                            </a>
                            <?php if (!$esPropietario && $amigo['id_usuario'] != $_SESSION['usuario_id']): ?>
                                <!-- Botón para enviar solicitud si no es el dueño del perfil -->
                                <a href="<?= BASE_URL ?>?c=amigos&a=enviarSolicitud&id=<?= $amigo['id_usuario'] ?>" class="w-full px-6 py-2.5 text-black font-medium rounded-xl hover:bg-surface-container-low hover:text-primary transition-colors flex items-center gap-2 cursor-pointer">
                                    <span class="material-symbols-outlined text-lg">person_add</span>
                                    Conectar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-sm group-hover:scale-105 transition-transform">
                            <?php if (!empty($amigo['foto_base64'])): ?>
                                <img class="w-full h-full object-cover" src="<?= $amigo['foto_base64'] ?>" alt="Foto de <?= htmlspecialchars($amigo['nombre_completo']) ?>">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-violet-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-3xl">person</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-on-surface"><?= htmlspecialchars($amigo['nombre_completo']) ?></h3>
                            <!-- Puedes mostrar el email o algo? No mostramos por privacidad -->
                            <div class="flex items-center gap-1.5 py-1 px-3 bg-primary/5 rounded-full w-fit mt-2">
                                <span class="material-symbols-outlined text-primary text-[14px]">groups</span>
                                <span class="text-[11px] font-bold text-on-primary-container">Amigo en Unio</span>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Pagination if needed later -->
        <footer class="mt-16 flex justify-between items-center">
            <p class="text-sm text-outline font-medium">
                Mostrando <?= count($amigos) ?> <?= count($amigos) == 1 ? 'conexión' : 'conexiones' ?>
            </p>
            <!-- Puedes agregar paginación aquí si la lista es larga -->
        </footer>
    </div>
</main>

<script>
    // Simple filtering by name
    const searchInput = document.getElementById('search-friends');
    const cards = document.querySelectorAll('.friend-card');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (name.includes(term)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
</script>

<?php include 'includes/bottom-nav.php'; ?>
</body>
</html>
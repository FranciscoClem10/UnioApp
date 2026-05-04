<?php
if (!isset($ajustes)) {
    die("Error al cargar ajustes.");
}
// Incluir la cabecera que define <html> y <head>
include(__DIR__ . "/../../Scripts/ajustesS.php");
?>
<body class="bg-background text-on-surface antialiased overflow-x-hidden">
    <?php include 'includes/top-nav.php'; // Ajusta la ruta si es necesario ?>

    <div class="flex pt-16 min-h-screen">
        <main class="flex-1 p-4 md:p-12 max-w-5xl mx-auto w-full pb-32 md:pb-12">
            <header class="mb-12">
                <h1 class="text-4xl md:text-5xl font-black tracking-tighter text-on-surface mb-2">Ajustes</h1>
                <p class="text-on-surface-variant max-w-xl">
                    Personaliza cómo interactúas con Unio y gestiona tus notificaciones y ajustes regionales.
                </p>
            </header>

            <form id="form-ajustes" class="space-y-16">
                <!-- 1. MODO OSCURO -->
                <section>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-1.5 h-8 bg-primary rounded-full"></div>
                        <h2 class="text-2xl font-bold font-headline">Apariencia</h2>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] flex items-center justify-between">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">dark_mode</span>
                            </div>
                            <div>
                                <p class="font-bold text-on-surface">Modo Oscuro</p>
                                <p class="text-sm text-on-surface-variant opacity-70">Activa el tema oscuro de la aplicación</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input name="modo_oscuro" value="1" class="sr-only peer ajuste-checkbox" type="checkbox" <?= isset($ajustes['modo_oscuro']) && $ajustes['modo_oscuro'] ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </section>

                <!-- 2. NOTIFICACIONES -->
                <section>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-1.5 h-8 bg-primary rounded-full"></div>
                        <h2 class="text-2xl font-bold font-headline">Preferencias de Notificación</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php
                        $notificaciones = [
                            'notif_mensaje' => ['icono' => 'chat_bubble', 'label' => 'Mensajes de grupo', 'desc' => 'Notificaciones de chats de actividades'],
                            'notif_mensaje_actividad' => ['icono' => 'campaign', 'label' => 'Actualizaciones de actividades', 'desc' => 'Cambios en actividades en las que participas'],
                            'notif_mensaje_privado' => ['icono' => 'mail', 'label' => 'Mensajes privados', 'desc' => 'Notificaciones de mensajes directos'],
                            'notif_amistad' => ['icono' => 'group_add', 'label' => 'Nuevos amigos', 'desc' => 'Cuando alguien acepte tu solicitud'],
                            'notif_solicitud_amistad' => ['icono' => 'person_add', 'label' => 'Solicitudes de amistad', 'desc' => 'Cuando recibas una solicitud'],
                            'notif_actividad' => ['icono' => 'event', 'label' => 'Nuevas actividades cerca', 'desc' => 'Recomendaciones basadas en tu ubicación']
                        ];
                        foreach ($notificaciones as $campo => $info):
                            $valor = isset($ajustes[$campo]) ? $ajustes[$campo] : 1;
                        ?>
                        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] flex items-center justify-between">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined"><?= $info['icono'] ?></span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface"><?= $info['label'] ?></p>
                                    <p class="text-sm text-on-surface-variant opacity-70"><?= $info['desc'] ?></p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input name="<?= $campo ?>" value="1" class="sr-only peer ajuste-checkbox" type="checkbox" <?= $valor ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 3. VISIBILIDAD DEL PERFIL -->
                <section>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-1.5 h-8 bg-tertiary rounded-full"></div>
                        <h2 class="text-2xl font-bold font-headline">Visibilidad del Perfil</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php
                        $visibilidades = [
                            'ubicacion_visibilidad' => ['icono' => 'location_on', 'label' => 'Ubicación', 'desc' => 'Quién puede ver tu ubicación'],
                            'correo_visibilidad'    => ['icono' => 'mail', 'label' => 'Correo electrónico', 'desc' => 'Quién puede ver tu email'],
                            'foto_visibilidad'      => ['icono' => 'photo_camera', 'label' => 'Foto de perfil', 'desc' => 'Quién puede ver tu foto'],
                            'edad_visibilidad'      => ['icono' => 'calendar_today', 'label' => 'Edad', 'desc' => 'Quién puede ver tu edad'],
                            'perfil_visibilidad'    => ['icono' => 'account_circle', 'label' => 'Perfil completo', 'desc' => 'Quién puede ver tu perfil'],
                            'actividades_visibilidad'=> ['icono' => 'event_list', 'label' => 'Actividades', 'desc' => 'Quién puede ver tus actividades'],
                        ];
                        $opciones = ['nadie' => 'Nadie', 'amigos' => 'Solo amigos', 'todos' => 'Todos'];
                        foreach ($visibilidades as $campo => $info):
                            $actual = isset($ajustes[$campo]) ? $ajustes[$campo] : 'todos';
                        ?>
                        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem]">
                            <div class="flex gap-4 items-center mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-tertiary/10 flex items-center justify-center text-tertiary">
                                    <span class="material-symbols-outlined"><?= $info['icono'] ?></span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface"><?= $info['label'] ?></p>
                                    <p class="text-sm text-on-surface-variant opacity-70"><?= $info['desc'] ?></p>
                                </div>
                            </div>
                            <div class="relative">
                                <select name="<?= $campo ?>" class="ajuste-select w-full bg-white border-0 rounded-2xl py-3 px-4 appearance-none focus:ring-2 focus:ring-primary shadow-sm text-sm font-medium">
                                    <?php foreach ($opciones as $valor => $texto): ?>
                                        <option value="<?= $valor ?>" <?= $actual === $valor ? 'selected' : '' ?>><?= $texto ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- BOTÓN RESTAURAR VALORES PREDETERMINADOS -->
                <div class="flex justify-end pt-8">
                    <button type="button" id="restaurar-btn" class="px-8 py-4 rounded-xl bg-surface-container text-on-surface font-bold text-sm hover:bg-surface-container-high transition-colors">
                        Restaurar valores predeterminados
                    </button>
                </div>
            </form>
        </main>
    </div>

    <?php include 'includes/bottom-nav.php'; // Ajusta la ruta si es necesario ?>

    <!-- TOAST DE CONFIRMACIÓN -->
    <div id="toast" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-inverse-surface text-white px-6 py-3 rounded-full text-sm font-medium shadow-lg z-[100] opacity-0 transition-all duration-300">
        Configuración guardada
    </div>

    <script>
        // Muestra un toast temporal
        function mostrarToast(mensaje = 'Configuración guardada', duracion = 2000) {
            const toast = document.getElementById('toast');
            toast.textContent = mensaje;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), duracion);
        }

        // Prepara los datos del formulario incluyendo checkboxes no marcados como 0
        function recogerDatos() {
            const form = document.getElementById('form-ajustes');
            const fd = new FormData(form);
            document.querySelectorAll('.ajuste-checkbox').forEach(cb => {
                if (!cb.checked) {
                    fd.set(cb.name, '0');
                }
            });
            return fd;
        }

        // Envía AJAX para guardar
        function guardarAjustes() {
            const fd = recogerDatos();
            fetch('<?= BASE_URL ?>?c=ajustes&a=guardar', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    mostrarToast(data.mensaje || 'Guardado');
                    // Recargar para que la sesión y el tema se actualicen
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('Error al guardar');
                }
            })
            .catch(() => mostrarToast('Error de conexión'));
        }

        // Asignar eventos de cambio a todos los inputs
        document.querySelectorAll('.ajuste-checkbox, .ajuste-select').forEach(input => {
            input.addEventListener('change', guardarAjustes);
        });

        // Botón restaurar valores predeterminados
        document.getElementById('restaurar-btn').addEventListener('click', () => {
            if (!confirm('¿Restaurar la configuración a los valores de fábrica?')) return;
            fetch('<?= BASE_URL ?>?c=ajustes&a=restaurar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    mostrarToast(data.mensaje);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    mostrarToast('Error al restaurar');
                }
            })
            .catch(() => mostrarToast('Error de conexión'));
        });
    </script>
</body>
</html>
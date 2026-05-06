<?php
// tarjeta_actividad.php — espera la variable $actividad
?>
<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-outline-variant/5 group flex flex-col md:flex-row h-auto md:h-64">
    <div class="md:w-2/5 relative h-48 md:h-full overflow-hidden">
        <?php if (!empty($actividad['foto_base64'])): ?>
            <img alt="Imagen de actividad" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 src="<?= $actividad['foto_base64'] ?>"/>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-primary/50">celebration</span>
            </div>
        <?php endif; ?>
        <div class="absolute top-4 left-4">
            <?php if ($actividad['estado'] == 'en_curso'): ?>
                <span class="px-3 py-1 bg-primary text-on-primary text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg">En curso</span>
            <?php elseif ($actividad['estado'] == 'finalizada'): ?>
                <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg">Finalizada</span>
            <?php else: ?>
                <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg">Por iniciar</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="p-6 md:w-3/5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-secondary"><?= htmlspecialchars($actividad['categoria']) ?></span>
                    <span class="text-outline-variant text-[10px]">•</span>
                    <span class="material-symbols-outlined text-sm text-outline-variant" title="<?= $actividad['privacidad'] == 'privada' ? 'Privado' : 'Público' ?>">
                        <?= $actividad['privacidad'] == 'privada' ? 'lock' : 'public' ?>
                    </span>
                </div>
                <div class="flex items-center gap-1 text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    <span class="text-[11px] font-bold">Min: <?= $actividad['limite_participantes_min'] ?> / Max: <?= $actividad['limite_participantes_max'] ?? '∞' ?></span>
                </div>
            </div>
            <h3 class="text-xl font-bold text-on-surface mb-2 leading-tight"><?= htmlspecialchars($actividad['nombre']) ?></h3>
            <p class="text-on-surface-variant text-sm line-clamp-2"><?= htmlspecialchars($actividad['descripcion']) ?></p>
            <p class="text-xs text-primary font-bold mt-2">Creado por: <?= htmlspecialchars($actividad['creador_nombre']) ?></p>
        </div>
        <div class="mt-4 pt-4 border-t border-outline-variant/10 flex items-center justify-between">
            <div class="flex flex-col">
                <div class="flex items-center gap-1 text-on-surface-variant text-[11px] font-medium">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    <?= date('d M, Y · H:i', strtotime($actividad['fecha_inicio'])) ?>
                </div>
                <div class="flex items-center gap-1 text-outline text-[11px]">
                    <span class="material-symbols-outlined text-[14px]">event_repeat</span>
                    <?= date('d M, Y · H:i', strtotime($actividad['fecha_fin'])) ?>
                </div>
            </div>
            <a href= "<?= BASE_URL ?>?c=actividad&a=detalle&id=<?= $actividad['id_actividad'] ?>" class="text-primary font-bold text-xs flex items-center gap-1 hover:gap-2 transition-all">
                Ver detalles
                <span class="material-symbols-outlined text-xs">arrow_forward</span>
            </a>
        </div>
    </div>
</div>
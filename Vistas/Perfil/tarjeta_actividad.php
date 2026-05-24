<?php
// tarjeta_actividad.php — espera la variable $actividad

// Definir estilos según el rol
$cardStyle = '';
$textColorClass = '';
$textMutedClass = '';
$borderColorClass = '';
$linkDestino = '';

switch ($actividad['rol_usuario']) {
    case 'creador':
        // Fondo morado muy suave (armoniza con #5a2af7)
        $cardStyle = 'background-color: #DFD7FC;'; // equivalente a indigo-50
        $borderColorClass = 'border-indigo-200';
        $textColorClass = 'text-gray-900';
        $textMutedClass = 'text-gray-600';
        $linkDestino = BASE_URL . '?c=GestionActividad&a=index&id=' . $actividad['id_actividad'];
        break;

    case 'organizador':
        // Fondo morado ligeramente más intenso pero suave
        $cardStyle = 'background-color: #f5f3ff;'; // indigo-100
        $borderColorClass = 'border-indigo-300';
        $textColorClass = 'text-gray-900';
        $textMutedClass = 'text-gray-700';
        $linkDestino = BASE_URL . '?c=GestionActividad&a=index&id=' . $actividad['id_actividad'];
        break;
	
	case 'miembro':
        $cardStyle = 'background-color: #ffffff;';
        $borderColorClass = 'border-gray-100';
        $textColorClass = 'text-gray-900';
        $textMutedClass = 'text-gray-500';
        $linkDestino = BASE_URL . '?c=actividad&a=detalle&id=' . $actividad['id_actividad'];
        break;
	
    default:
        $cardStyle = 'background-color: #ffffff;';
        $borderColorClass = 'border-gray-100';
        $textColorClass = 'text-gray-900';
        $textMutedClass = 'text-gray-500';
        $linkDestino = BASE_URL . '?c=actividad&a=detalle&id=' . $actividad['id_actividad'];
        break;
}
?>

<div class="rounded-3xl overflow-hidden border <?= $borderColorClass ?> group flex flex-col md:flex-row h-auto md:h-64 actividad-card"
     style="<?= $cardStyle ?>"
     data-role="<?= $actividad['rol_usuario'] ?>"
     data-date="<?= strtotime($actividad['fecha_inicio']) ?>">
    <div class="md:w-2/5 relative h-48 md:h-full overflow-hidden">
        <?php if (!empty($actividad['foto_base64'])): ?>
            <img alt="Imagen de actividad" class="w-full h-full object-cover"
                 src="<?= $actividad['foto_base64'] ?>"/>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-indigo-400">celebration</span>
            </div>
        <?php endif; ?>
        <div class="absolute top-4 left-4">
            <?php if ($actividad['estado'] == 'en_curso'): ?>
                <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-full shadow-md">En curso</span>
            <?php elseif ($actividad['estado'] == 'finalizada'): ?>
                <span class="px-3 py-1 bg-gray-200 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded-full shadow-md">Finalizada</span>
            <?php else: ?>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-widest rounded-full shadow-md">Por iniciar</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="p-6 md:w-3/5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600">
                        <?= htmlspecialchars($actividad['categoria']) ?>
                    </span>
                    <span class="text-gray-400 text-[10px]">•</span>
                    <span class="material-symbols-outlined text-sm text-gray-500">
                        <?= $actividad['privacidad'] == 'privada' ? 'lock' : 'public' ?>
                    </span>
                </div>
                <div class="flex items-center gap-1 <?= $textMutedClass ?>">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    <span class="text-[11px] font-bold">Min: <?= $actividad['limite_participantes_min'] ?> / Max: <?= $actividad['limite_participantes_max'] ?? '∞' ?></span>
                </div>
            </div>
            <h3 class="text-xl font-bold <?= $textColorClass ?> mb-2 leading-tight"><?= htmlspecialchars($actividad['nombre']) ?></h3>
            <p class="<?= $textMutedClass ?> text-sm line-clamp-2"><?= htmlspecialchars($actividad['descripcion']) ?></p>
            <p class="text-xs font-bold mt-2 <?= $actividad['rol_usuario'] == 'creador' ? 'text-indigo-700' : 'text-indigo-600' ?>">
                Creado por: <?= htmlspecialchars($actividad['creador_nombre']) ?>
            </p>
        </div>
        <div class="mt-4 pt-4 border-t <?= str_replace('border-', 'border-t-', $borderColorClass) ?> flex items-center justify-between">
            <div class="flex flex-col">
                <div class="flex items-center gap-1 <?= $textMutedClass ?> text-[11px] font-medium">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    <?= date('d M, Y · H:i', strtotime($actividad['fecha_inicio'])) ?>
                </div>
                <div class="flex items-center gap-1 text-gray-400 text-[11px]">
                    <span class="material-symbols-outlined text-[14px]">event_repeat</span>
                    <?= date('d M, Y · H:i', strtotime($actividad['fecha_fin'])) ?>
                </div>
            </div>
            <a href="<?= $linkDestino ?>" class="text-indigo-600 font-bold text-xs flex items-center gap-1 hover:gap-2 transition-all">
                Ver detalles
                <span class="material-symbols-outlined text-xs">arrow_forward</span>
            </a>
        </div>
    </div>
</div>
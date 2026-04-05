<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Actividad - UnioApp</title>
    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        header h1 { margin-top: 0; }
        section {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background: #218838; }
        .volver {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .volver:hover { text-decoration: underline; }
        #map {
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .resena-item {
            background: #f9f9f9;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        .calificacion {
            color: #ffc107;
            font-weight: bold;
        }
        .form-resena {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .form-resena select, .form-resena textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .error { color: red; }
        .exito { color: green; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Detalles de la Actividad</h1>
    </header>

    <main>
        <div style="flex: 0 0 200px;">
            <?php if ($actividad['foto_base64']): ?>
                <img src="<?= $actividad['foto_base64'] ?>" alt="Foto de la actividad" style="width: 100%; border-radius: 8px;">
            <?php else: ?>
                <div style="width: 100%; height: 150px; background: #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666;">Sin imagen</div>
            <?php endif; ?>
        </div>

        <!-- Información principal -->
        <section>
            <h2><?= htmlspecialchars($actividad['nombre']) ?></h2>
            <p><strong>Organizado por:</strong> <?= htmlspecialchars($actividad['organizador_nombre']) ?></p>
            <p><strong>Publicado:</strong> <?= $actividad['fecha_publicacion'] ?> <?= $actividad['hora_publicacion'] ?></p>
            <p><strong>Acceso:</strong> <?= $actividad['tipo_acceso_legible'] ?></p>
            <p><strong>Categoría:</strong> <?= htmlspecialchars($actividad['categoria']) ?></p>
        </section>

        <!-- Capacidad -->
        <section>
            <h3>Capacidad</h3>
            <p><strong>Confirmados:</strong> <?= $actividad['asistentes_confirmados'] ?> / <?= $actividad['capacidad_max'] ?></p>
            <p><strong>Mínimo:</strong> <?= $actividad['capacidad_min'] ?></p>
        </section>

        <!-- Horario -->
        <section>
            <h3>Horario</h3>
            <p><strong>Inicio:</strong> <?= $actividad['fecha_inicio'] ?> <?= $actividad['hora_inicio'] ?></p>
            <p><strong>Fin:</strong> <?= $actividad['fecha_fin'] ?> <?= $actividad['hora_fin'] ?></p>
        </section>

        <!-- Rango de edad -->
        <section>
            <h3>Rango de Edad</h3>
            <p><?= $actividad['edad_minima'] ?> - <?= $actividad['edad_maxima'] ?> años</p>
        </section>

        <!-- Mapa de ubicación -->
        <section>
            <h3>Ubicación</h3>
            <div id="map"></div>
            <p class="coordenadas">Coordenadas: <?= $actividad['lat'] ?>, <?= $actividad['lng'] ?></p>
        </section>

        <!-- Descripción y requisitos -->
        <section>
            <h3>Sobre la actividad</h3>
            <p><?= nl2br(htmlspecialchars($actividad['descripcion'])) ?></p>

            <?php if (!empty($actividad['requisitos_array'])): ?>
                <h4>Qué traer / Requisitos</h4>
                <ul>
                    <?php foreach ($actividad['requisitos_array'] as $req): ?>
                        <li><?= htmlspecialchars($req) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (!empty($actividad['requisitos'])): ?>
                <h4>Requisitos</h4>
                <p><?= nl2br(htmlspecialchars($actividad['requisitos'])) ?></p>
            <?php endif; ?>
        </section>

        <!-- Botón de asistencia -->
        <section>
            <?php
            // Verificar si el usuario ya es participante (estado aceptado)
            $modeloCheck = new ModeloActividad(); // Para consulta puntual, pero mejor usar una función aparte. Simplificamos con consulta directa.
            $db = Database::getConexion();
            $sqlCheck = "SELECT estado FROM participantes WHERE id_actividad = :id_act AND id_usuario = :id_user";
            $stmt = $db->prepare($sqlCheck);
            $stmt->execute([':id_act' => $actividad['id_actividad'], ':id_user' => $_SESSION['usuario_id']]);
            $participacion = $stmt->fetch(PDO::FETCH_ASSOC);
            $yaUnido = ($participacion && $participacion['estado'] === 'aceptado');
            $solicitudPendiente = ($participacion && $participacion['estado'] === 'pendiente');
            ?>
            <?php if ($yaUnido): ?>
                <button disabled style="background: #6c757d;">Ya estás unido a esta actividad</button>
            <?php elseif ($solicitudPendiente): ?>
                <button disabled style="background: #ffc107; color: #333;">Solicitud pendiente de aprobación</button>
            <?php else: ?>
                <form action="<?= BASE_URL ?>?c=participacion&a=solicitar" method="POST">
                    <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                    <button type="submit">Confirmar Asistencia</button>
                </form>
            <?php endif; ?>
            <p><?= $actividad['asistentes_extra'] ?> personas más están interesadas o invitadas</p>
        </section>

        <!-- Sección de Reseñas -->
        <section>
            <h3>Reseñas de participantes</h3>

            <?php if (isset($_SESSION['error_resena'])): ?>
                <div class="error"><?= htmlspecialchars($_SESSION['error_resena']) ?></div>
                <?php unset($_SESSION['error_resena']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['exito_resena'])): ?>
                <div class="exito"><?= htmlspecialchars($_SESSION['exito_resena']) ?></div>
                <?php unset($_SESSION['exito_resena']); ?>
            <?php endif; ?>

            <?php if ($puedeResenar): ?>
                <div class="form-resena">
                    <h4>Deja tu reseña</h4>
                    <form action="<?= BASE_URL ?>?c=actividad&a=guardarResena" method="POST">
                        <input type="hidden" name="id_actividad" value="<?= $actividad['id_actividad'] ?>">
                        <label>Calificación (1 a 5):</label>
                        <select name="calificacion" required>
                            <option value="5">5 - Excelente</option>
                            <option value="4">4 - Muy bueno</option>
                            <option value="3">3 - Bueno</option>
                            <option value="2">2 - Regular</option>
                            <option value="1">1 - Malo</option>
                        </select>
                        <label>Comentario:</label>
                        <textarea name="comentario" rows="3" maxlength="1000"></textarea>
                        <button type="submit">Enviar reseña</button>
                    </form>
                </div>
            <?php elseif ($actividad['estado'] == 'finalizada' && !$yaUnido): ?>
                <p>Solo los participantes que asistieron pueden dejar reseñas.</p>
            <?php elseif ($actividad['estado'] != 'finalizada'): ?>
                <p>Las reseñas solo están disponibles después de que la actividad finalice.</p>
            <?php endif; ?>

            <?php if (empty($resenas)): ?>
                <p>No hay reseñas para esta actividad todavía.</p>
            <?php else: ?>
                <?php foreach ($resenas as $r): ?>
                    <div class="resena-item">
                        <strong><?= htmlspecialchars($r['usuario_nombre']) ?></strong>
                        <span class="calificacion">(<?= str_repeat('★', $r['calificacion']) . str_repeat('☆', 5 - $r['calificacion']) ?>)</span>
                        <p><?= nl2br(htmlspecialchars($r['comentario'])) ?></p>
                        <small><?= $r['fecha_resena'] ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <a href="<?= BASE_URL ?>?c=dashboard" class="volver">← Volver al dashboard</a>
</div>

<script>
    // Inicializar mapa con la ubicación de la actividad
    var lat = <?= $actividad['lat'] ?>;
    var lng = <?= $actividad['lng'] ?>;
    var map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);
    var marker = L.marker([lat, lng]).addTo(map);
    marker.bindTooltip("<?= htmlspecialchars($actividad['nombre']) ?>").openTooltip();
</script>
</body>
</html>
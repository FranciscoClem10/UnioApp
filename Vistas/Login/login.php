<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - UnioApp</title>
    <style>
        /* Estilos mínimos solo para orden visual, sin frameworks */
        body { font-family: Arial, sans-serif; margin: 40px; }
        .contenedor { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .campo { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 15px; }
        .enlaces { margin-top: 15px; text-align: center; }
        .enlaces a { margin: 0 10px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($_SESSION['error_login'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error_login']) ?></div>
            <?php unset($_SESSION['error_login']); ?>
        <?php endif; ?>
        <form action="<?= BASE_URL ?>?c=login&a=verificar" method="POST">
            <div class="campo">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div class="campo">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
        <div class="enlaces">
            <a href="<?= BASE_URL ?>?c=registro">¿Registrarse?</a> | 
            <a href="#">¿Olvidó su contraseña?</a>
        </div>
    </div>
</body>
</html>
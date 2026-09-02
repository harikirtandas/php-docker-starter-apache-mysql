<?php

declare(strict_types=1);

// demo descartable: solo sirve para verificar que Apache, PHP y MySQL
// funcionan juntos de punta a punta. se puede borrar sin problema.

require __DIR__ . '/../app/db.php';

$items = [];
$error = null;

try {
    $stmt = db()->query('SELECT id, nombre, creado_en FROM demo_items ORDER BY id');
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>php-docker-starter-apache-mysql</title>
</head>
<body>
    <h1>php-docker-starter-apache-mysql</h1>
    <p>PHP <?= htmlspecialchars(PHP_VERSION) ?></p>

    <?php if ($error !== null): ?>
        <p>Error de conexion: <?= htmlspecialchars($error) ?></p>
    <?php else: ?>
        <ul>
            <?php foreach ($items as $item): ?>
                <li>
                    #<?= htmlspecialchars((string) $item['id']) ?> -
                    <?= htmlspecialchars($item['nombre']) ?> -
                    <?= htmlspecialchars((string) $item['creado_en']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

<?php
// Prueba directa de PostgreSQL
$dsn = 'pgsql:host=localhost;port=5432;dbname=lastking';
$username = 'postgres';
$password = '12052017PostJb.';

try {
    $pdo = new PDO($dsn, $username, $password);
    echo "✓ Conexión exitosa a PostgreSQL.\n\n";
    
    // Ver columnas de users
    $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users' ORDER BY ordinal_position");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columnas en tabla 'users':\n";
    if (count($columns) > 0) {
        foreach ($columns as $col) {
            echo "  - " . $col['column_name'] . " (" . $col['data_type'] . ")\n";
        }
    } else {
        echo "  ✗ No hay columnas (tabla podría no existir)\n";
    }
    
    echo "\n";
    
    // Contar usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de usuarios en tabla: " . $row['count'] . "\n\n";
    
    // Listar usuarios
    $stmt = $pdo->query("SELECT id, email, username FROM users ORDER BY created_at DESC LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "Últimos usuarios:\n";
        foreach ($users as $user) {
            echo "  - ID: " . $user['id'] . ", Email: " . $user['email'] . ", Username: " . $user['username'] . "\n";
        }
    } else {
        echo "✗ No hay usuarios\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
}
?>
<?php
$dsn = 'pgsql:host=localhost;port=5432;dbname=lastking';
$username = 'postgres';
$password = '12052017PostJb.';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insertar usuario directamente
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $result = $stmt->execute([
        'testdirect2',
        'testdirect2@example.com',
        password_hash('password123', PASSWORD_DEFAULT)
    ]);
    
    echo "✓ Insert directo ejecutado\n";
    
    // Verificar si se insertó
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total usuarios después del insert: " . $row['count'] . "\n";
    
    // Mostrar el usuario insertado
    $stmt = $pdo->query("SELECT id, email, username FROM users ORDER BY created_at DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "Último usuario: ID=" . $user['id'] . ", Email=" . $user['email'] . ", Username=" . $user['username'] . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
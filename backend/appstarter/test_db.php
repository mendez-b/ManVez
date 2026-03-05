<?php
$dsn = 'pgsql:host=localhost;port=5432;dbname=lastking';
$username = 'postgres';
$password = '12052017PostJb.';

try {
    $pdo = new PDO($dsn, $username, $password);
    echo "Conexión exitosa a PostgreSQL.\n";
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Usuarios en la tabla users:\n";
    print_r($users);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
}
?>
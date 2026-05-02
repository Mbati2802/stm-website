<?php
// Test CRM Database Connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>CRM Database Connection Test</h1>";

try {
    $config = require __DIR__ . '/config/crm_config.php';
    
    echo "<p><strong>Database Host:</strong> " . $config['db']['host'] . "</p>";
    echo "<p><strong>Database Name:</strong> " . $config['db']['name'] . "</p>";
    echo "<p><strong>Database User:</strong> " . $config['db']['user'] . "</p>";
    
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['options']
    );
    
    echo "<p style='color: green;'><strong>✓ Database connection successful!</strong></p>";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Tables in CRM database:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table . "</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Database connection failed:</strong></p>";
    echo "<p>" . $e->getMessage() . "</p>";
}

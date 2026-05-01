<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/core/helpers.php';

$pdo = Database::getInstance($config['db']);
$stmt = $pdo->query('SELECT id, name FROM programmes');
$update = $pdo->prepare('UPDATE programmes SET abbreviation = ? WHERE id = ?');

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $abbr = generate_program_abbreviation($row['name']);
    echo "Programme ID {$row['id']}: {$row['name']} → {$abbr}\n";
    $update->execute([$abbr, $row['id']]);
}

echo "\nDone! All programme abbreviations have been updated.\n";

<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/app/core/helpers.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = Database::getInstance($config['db']);
$stmt = $pdo->query('SELECT id, name FROM programmes');
$update = $pdo->prepare('UPDATE programmes SET abbreviation = ? WHERE id = ?');

echo "Updating programme abbreviations...\n\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $abbr = generate_program_abbreviation($row['name']);
    echo "Programme ID {$row['id']}: {$row['name']} → {$abbr}\n";
    $update->execute([$abbr, $row['id']]);
}

echo "\nDone! All programme abbreviations have been updated.\n";

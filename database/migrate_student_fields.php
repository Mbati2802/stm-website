<?php
require_once __DIR__ . '/../bootstrap.php';

$pdo = Database::getInstance($config['db']);

// Columns to add
$columns = [
    'national_id' => 'VARCHAR(50) NULL AFTER admission_number',
    'gender' => 'ENUM("Male", "Female") NULL AFTER national_id',
    'date_of_birth' => 'DATE NULL AFTER gender',
    'phone' => 'VARCHAR(20) NULL AFTER date_of_birth',
    'county' => 'VARCHAR(100) NULL AFTER phone',
    'sub_county' => 'VARCHAR(100) NULL AFTER county',
    'guardian_name' => 'VARCHAR(255) NULL AFTER sub_county',
    'guardian_relationship' => 'ENUM("Parent", "Guardian", "Sponsor") NULL AFTER guardian_name',
    'guardian_phone' => 'VARCHAR(20) NULL AFTER guardian_relationship',
    'guardian_email' => 'VARCHAR(255) NULL AFTER guardian_phone',
    'previous_school' => 'VARCHAR(255) NULL AFTER guardian_email',
    'kcse_year' => 'YEAR NULL AFTER previous_school',
    'kcse_grade' => 'VARCHAR(5) NULL AFTER kcse_year',
    'kcse_index' => 'VARCHAR(50) NULL AFTER kcse_grade',
    'preferred_intake' => 'VARCHAR(50) NULL AFTER kcse_index',
    'disability_status' => 'ENUM("None", "Physical", "Visual", "Hearing", "Other") DEFAULT "None" NULL AFTER preferred_intake',
    'referral_source' => 'VARCHAR(255) NULL AFTER disability_status',
    'additional_notes' => 'TEXT NULL AFTER referral_source',
];

echo "Checking and adding columns to student_accounts...\n\n";

// Get existing columns
$stmt = $pdo->query("SHOW COLUMNS FROM student_accounts");
$existingColumns = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $existingColumns[] = $row['Field'];
}

// Add missing columns
foreach ($columns as $columnName => $columnDefinition) {
    if (in_array($columnName, $existingColumns)) {
        echo "Column '$columnName' already exists, skipping.\n";
    } else {
        try {
            $sql = "ALTER TABLE student_accounts ADD COLUMN $columnName $columnDefinition";
            $pdo->exec($sql);
            echo "✓ Added column: $columnName\n";
        } catch (PDOException $e) {
            echo "✗ Failed to add column $columnName: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDone!\n";

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $pdo->query('SELECT 1');
    
    // Check if required tables exist
    $tables = ['departments', 'students', 'staff', 'courses', 'course_topics'];
    $missingTables = [];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() === 0) {
            $missingTables[] = $table;
        }
    }
    
    if (!empty($missingTables)) {
        throw new Exception('Missing tables: ' . implode(', ', $missingTables));
    }
    
    // Check if demo accounts exist
    $staffCount = $pdo->query("SELECT COUNT(*) FROM staff WHERE email = 'staff@example.com'")->fetchColumn();
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students WHERE email = 'student@example.com'")->fetchColumn();
    
    if ($staffCount === 0 || $studentCount === 0) {
        throw new Exception('Demo accounts not found. Please run database.sql');
    }
    
    // All checks passed
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection and schema verification successful',
        'details' => [
            'tables_exist' => true,
            'demo_accounts_exist' => true
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 
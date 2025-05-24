<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo json_encode([
            "status" => "success",
            "message" => "API is working and database connection is successful",
            "php_version" => phpversion(),
            "server_info" => $_SERVER['SERVER_SOFTWARE']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database connection failed"
        ]);
    }
} catch(Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?> 
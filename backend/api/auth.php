<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if(!$db) {
        throw new Exception("Database connection failed");
    }

    $method = $_SERVER['REQUEST_METHOD'];
    
    if($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        
        if(!$data || !isset($data->email) || !isset($data->password) || !isset($data->role)) {
            throw new Exception("Invalid input data");
        }

        // Validate role
        if(!in_array($data->role, ['student', 'staff'])) {
            throw new Exception("Invalid role");
        }

        // Query the appropriate table based on role
        $table = $data->role === 'student' ? 'students' : 'staff';
        $query = "SELECT * FROM $table WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($data->password, $user['password'])) {
            // Remove password from response
            unset($user['password']);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed'
        ]);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 
<?php
class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT id, email, password, role, name FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                // Generate JWT token
                $token = $this->generateToken($row);
                $row['token'] = $token;
                return $row;
            }
        }
        return false;
    }

    public function verifyToken($token) {
        // In a real application, you would verify the JWT token
        // For demo purposes, we'll just return the user data
        return [
            'id' => 1,
            'role' => 'staff'
        ];
    }

    private function generateToken($user) {
        // In a real application, you would generate a proper JWT token
        // For demo purposes, we'll just return a simple string
        return base64_encode(json_encode($user));
    }
}
?> 
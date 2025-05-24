<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

class Department {
    private $conn;
    private $table_name = "departments";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}

$database = new Database();
$db = $database->getConnection();
$department = new Department($db);

$stmt = $department->getAll();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($departments);
?> 
<?php
header('Content-Type: text/plain');

include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Database connection successful!\n\n";
    
    // Test departments table
    $query = "SELECT * FROM departments";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Departments table:\n";
    print_r($departments);
    echo "\n";
    
    // Test users table
    $query = "SELECT * FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users table:\n";
    print_r($users);
    echo "\n";
    
    // Test courses table
    $query = "SELECT * FROM courses";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Courses table:\n";
    print_r($courses);
    echo "\n";
    
    // Test course_topics table
    $query = "SELECT * FROM course_topics";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Course topics table:\n";
    print_r($topics);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 
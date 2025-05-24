<?php
// Test the courses API directly
include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Test getting all courses
    $query = "SELECT * FROM courses";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "Courses in database:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Course: " . $row['name'] . " (Code: " . $row['code'] . ")<br>";
    }
    
    // If no courses found, try to insert a test course
    if ($stmt->rowCount() == 0) {
        echo "No courses found. Inserting a test course...<br>";
        
        // First check if department exists
        $dept_query = "SELECT id FROM departments WHERE name = 'Computer Science'";
        $dept_stmt = $db->prepare($dept_query);
        $dept_stmt->execute();
        $dept = $dept_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dept) {
            $insert_query = "INSERT INTO courses (code, name, credits, department_id, semester, created_by) 
                           VALUES ('CS101', 'Test Course', 4, ?, 1, 1)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([$dept['id']]);
            
            echo "Test course inserted successfully!<br>";
        } else {
            echo "Error: Computer Science department not found.<br>";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
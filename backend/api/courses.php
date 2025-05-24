<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
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
    
    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                // Get single course
                $query = "SELECT c.*, d.code as department_code 
                         FROM courses c 
                         JOIN departments d ON c.department_id = d.id 
                         WHERE c.id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($course) {
                    // Get topics
                    $query = "SELECT id, topic FROM course_topics WHERE course_id = :course_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':course_id', $_GET['id']);
                    $stmt->execute();
                    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $course['topics'] = $topics;
                    
                    echo json_encode($course);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Course not found']);
                }
            } else {
                // Get courses with filters
                $query = "SELECT c.*, d.code as department_code 
                         FROM courses c 
                         JOIN departments d ON c.department_id = d.id 
                         WHERE 1=1";
                $params = [];
                
                if(isset($_GET['department'])) {
                    $query .= " AND d.code = :department";
                    $params[':department'] = $_GET['department'];
                }
                
                if(isset($_GET['semester'])) {
                    $query .= " AND c.semester = :semester";
                    $params[':semester'] = $_GET['semester'];
                }
                
                $query .= " ORDER BY c.semester, c.code";
                
                $stmt = $db->prepare($query);
                foreach($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->execute();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get topics for each course
                foreach($courses as &$course) {
                    $query = "SELECT id, topic FROM course_topics WHERE course_id = :course_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':course_id', $course['id']);
                    $stmt->execute();
                    $course['topics'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                echo json_encode($courses);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents("php://input"));
            
            if(!$data) {
                throw new Exception("Invalid input data");
            }
            
            // Start transaction
            $db->beginTransaction();
            
            try {
                // Insert course
                $query = "INSERT INTO courses (code, name, credits, department_id, semester, created_by) 
                         VALUES (:code, :name, :credits, :department_id, :semester, :created_by)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':code', $data->code);
                $stmt->bindParam(':name', $data->name);
                $stmt->bindParam(':credits', $data->credits);
                $stmt->bindParam(':department_id', $data->department_id);
                $stmt->bindParam(':semester', $data->semester);
                $stmt->bindParam(':created_by', $data->created_by);
                $stmt->execute();
                
                $course_id = $db->lastInsertId();
                
                // Insert topics
                if(isset($data->topics) && is_array($data->topics)) {
                    $query = "INSERT INTO course_topics (course_id, topic) VALUES (:course_id, :topic)";
                    $stmt = $db->prepare($query);
                    
                    foreach($data->topics as $topic) {
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':topic', $topic);
                        $stmt->execute();
                    }
                }
                
                $db->commit();
                echo json_encode(['message' => 'Course created successfully', 'id' => $course_id]);
            } catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;
            
        case 'PUT':
            if(!isset($_GET['id'])) {
                throw new Exception("Course ID is required");
            }
            
            $data = json_decode(file_get_contents("php://input"));
            
            if(!$data) {
                throw new Exception("Invalid input data");
            }
            
            // Start transaction
            $db->beginTransaction();
            
            try {
                // Update course
                $query = "UPDATE courses 
                         SET code = :code, name = :name, credits = :credits, 
                             department_id = :department_id, semester = :semester 
                         WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':code', $data->code);
                $stmt->bindParam(':name', $data->name);
                $stmt->bindParam(':credits', $data->credits);
                $stmt->bindParam(':department_id', $data->department_id);
                $stmt->bindParam(':semester', $data->semester);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                
                // Update topics
                if(isset($data->topics) && is_array($data->topics)) {
                    // Delete existing topics
                    $query = "DELETE FROM course_topics WHERE course_id = :course_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':course_id', $_GET['id']);
                    $stmt->execute();
                    
                    // Insert new topics
                    $query = "INSERT INTO course_topics (course_id, topic) VALUES (:course_id, :topic)";
                    $stmt = $db->prepare($query);
                    
                    foreach($data->topics as $topic) {
                        $stmt->bindParam(':course_id', $_GET['id']);
                        $stmt->bindParam(':topic', $topic);
                        $stmt->execute();
                    }
                }
                
                $db->commit();
                echo json_encode(['message' => 'Course updated successfully']);
            } catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;
            
        case 'DELETE':
            if(!isset($_GET['id'])) {
                throw new Exception("Course ID is required");
            }
            
            $query = "DELETE FROM courses WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
            
            echo json_encode(['message' => 'Course deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 
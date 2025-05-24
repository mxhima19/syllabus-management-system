<?php
$host = "localhost";
$username = "root";
$password = "";

try {
    // Create connection without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read the schema file
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement separately
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...<br>";
            } catch (PDOException $e) {
                echo "Error executing statement: " . substr($statement, 0, 50) . "...<br>";
                echo "Error message: " . $e->getMessage() . "<br><br>";
            }
        }
    }
    
    echo "<br>Database setup completed!";
} catch(PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
}
?> 
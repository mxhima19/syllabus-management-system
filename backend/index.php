<?php
// Basic test file
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Backend is working!',
    'timestamp' => date('Y-m-d H:i:s')
]);
?> 
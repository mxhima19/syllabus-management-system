<?php
// Test script for API endpoints

// Function to make API requests
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Base URL
$baseUrl = 'http://localhost/DBMS/backend/api';

// Test data
$testCourse = [
    'code' => 'CS101',
    'name' => 'Introduction to Programming',
    'credits' => 4,
    'department_id' => 1,
    'semester' => 1,
    'created_by' => 1,
    'topics' => "Introduction to Programming\nVariables and Data Types\nControl Structures\nFunctions"
];

// Test 1: Create a new course
echo "Test 1: Creating a new course\n";
$result = makeRequest($baseUrl . '/courses.php', 'POST', $testCourse);
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get all courses
echo "Test 2: Getting all courses\n";
$result = makeRequest($baseUrl . '/courses.php');
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Get a specific course (if we have an ID)
if (isset($result['response'][0]['id'])) {
    $courseId = $result['response'][0]['id'];
    echo "Test 3: Getting course with ID $courseId\n";
    $result = makeRequest($baseUrl . '/courses.php?id=' . $courseId);
    echo "Status Code: " . $result['code'] . "\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";
}

// Test 4: Update a course (if we have an ID)
if (isset($courseId)) {
    $updateData = $testCourse;
    $updateData['name'] = 'Introduction to Programming (Updated)';
    echo "Test 4: Updating course with ID $courseId\n";
    $result = makeRequest($baseUrl . '/courses.php?id=' . $courseId, 'PUT', $updateData);
    echo "Status Code: " . $result['code'] . "\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";
}

// Test 5: Delete a course (if we have an ID)
if (isset($courseId)) {
    echo "Test 5: Deleting course with ID $courseId\n";
    $result = makeRequest($baseUrl . '/courses.php?id=' . $courseId, 'DELETE');
    echo "Status Code: " . $result['code'] . "\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";
}
?> 
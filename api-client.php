<?php

/**
 * ToDo API Client
 *
 * This script demonstrates how to use cURL to interact with the ToDo API
 */



// API Base URL
$baseUrl = 'http://localhost:93';

// Function to make API requests
function makeRequest(string $endpoint, string $method = 'GET', ?array $data = null, ?string $token = null): array
{
    global $baseUrl;

    $url = $baseUrl . $endpoint;

    if ($token) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'api_token=' . $token;
    }

    echo "Making request to: " . $url . "\n";

    $ch = curl_init($url);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            $jsonData = json_encode($data);
            echo "With data: " . $jsonData . "\n";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
    }

    $response = curl_exec($ch);

    if ($response === false) {
        echo "cURL Error: " . curl_error($ch) . "\n";
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true),
        'raw' => $response
    ];
}

// =============== API TESTING ===============

echo "=== ToDo API Client ===\n";

// 1. Register a new user
echo "\n1. Registering a new user...\n";
$userData = [
    'name' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$registerResponse = makeRequest('/api/auth/register', 'POST', $userData);

if ($registerResponse['code'] === 201) {
    echo "User registered successfully!\n";
    $token = $registerResponse['response']['token'];
    $userId = $registerResponse['response']['user']['id'];
    echo "API Token: $token\n";
} else {
    echo "Registration failed with code: " . $registerResponse['code'] . "\n";
    echo "Error: " . $registerResponse['raw'] . "\n";
    exit(1);
}

// 2. Create a new tag
echo "\n2. Creating a tag...\n";
$tagData = [
    'title' => 'Important'
];

$createTagResponse = makeRequest('/api/tags', 'POST', $tagData, $token);

if ($createTagResponse['code'] === 201) {
    echo "Tag created successfully!\n";
    $tagId = $createTagResponse['response']['id'];
    echo "Tag ID: $tagId\n";
} else {
    echo "Tag creation failed with code: " . $createTagResponse['code'] . "\n";
    echo "Error: " . $createTagResponse['raw'] . "\n";
}

// 3. Create a task
echo "\n3. Creating a task...\n";
$taskData = [
    'title' => 'Test Task',
    'text' => 'This is a test task created via API',
    'tags' => [$tagId]
];

$createTaskResponse = makeRequest('/api/tasks', 'POST', $taskData, $token);

if ($createTaskResponse['code'] === 201) {
    echo "Task created successfully!\n";
    $taskId = $createTaskResponse['response']['id'];
    echo "Task ID: $taskId\n";
} else {
    echo "Task creation failed with code: " . $createTaskResponse['code'] . "\n";
    echo "Error: " . $createTaskResponse['raw'] . "\n";
}

// 4. Get all tasks
echo "\n4. Getting all tasks...\n";
$tasksResponse = makeRequest('/api/tasks', 'GET', null, $token);

if ($tasksResponse['code'] === 200) {
    echo "Tasks retrieved successfully!\n";
    $tasks = $tasksResponse['response'];
    echo "Number of tasks: " . count($tasks) . "\n";
} else {
    echo "Failed to get tasks with code: " . $tasksResponse['code'] . "\n";
    echo "Error: " . $tasksResponse['raw'] . "\n";
}

// 5. Update task
echo "\n5. Updating a task...\n";
$updateTaskData = [
    'title' => 'Updated Task',
    'text' => 'This task has been updated',
    'tags' => [$tagId]
];

$updateTaskResponse = makeRequest('/api/tasks/' . $taskId, 'PUT', $updateTaskData, $token);

if ($updateTaskResponse['code'] === 200) {
    echo "Task updated successfully!\n";
} else {
    echo "Task update failed with code: " . $updateTaskResponse['code'] . "\n";
    echo "Error: " . $updateTaskResponse['raw'] . "\n";
}

// 6. Get a specific task
echo "\n6. Getting a specific task...\n";
$taskResponse = makeRequest('/api/tasks/' . $taskId, 'GET', null, $token);

if ($taskResponse['code'] === 200) {
    echo "Task retrieved successfully!\n";
    $task = $taskResponse['response'];
    echo "Task title: " . $task['title'] . "\n";
    echo "Task text: " . $task['text'] . "\n";
} else {
    echo "Failed to get task with code: " . $taskResponse['code'] . "\n";
    echo "Error: " . $taskResponse['raw'] . "\n";
}

// 7. Delete task
echo "\n7. Deleting a task...\n";
$deleteTaskResponse = makeRequest('/api/tasks/' . $taskId, 'DELETE', null, $token);

if ($deleteTaskResponse['code'] === 204) {
    echo "Task deleted successfully!\n";
} else {
    echo "Task deletion failed with code: " . $deleteTaskResponse['code'] . "\n";
    echo "Error: " . $deleteTaskResponse['raw'] . "\n";
}

echo "\nAPI test completed successfully!\n";

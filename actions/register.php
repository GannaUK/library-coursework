<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$username = $input['username'] ?? null;
$password = $input['password'] ?? null;
$email = $input['email'] ?? null;
$dob = $input['dob'] ?? null;
$isAdmin = isset($input['isAdmin']) ? 1 : 0;
$is_admin_POST = isset($input['isAdmin']) && $input['isAdmin'] ? 1 : 0;

if (!$username || !$password || !$email || !$dob) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Hash the password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if the user already exists
$stmt = $pdo->prepare("SELECT id FROM db_users WHERE username = :username");
$stmt->execute(['username' => $username]);
$stmt->setFetchMode(PDO::FETCH_ASSOC);

if ($stmt->fetch()) {
    http_response_code(409); // Conflict
    echo json_encode(['success' => false, 'message' => 'User already exists.']);
    exit;
}

// Add the user to the database
try {
    $stmt = $pdo->prepare("
        INSERT INTO db_users (username, password, email, dob, is_admin)
        VALUES (:username, :password, :email, :dob, :is_admin)
    ");

    $success = $stmt->execute([
        'username'  => $username,
        'password'  => $hashedPassword,
        'email'     => $email,
        'dob'       => $dob,
        'is_admin'  => $isAdmin ? 1 : 0  
    ]);

    if ($success) {
        http_response_code(200);
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register user']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
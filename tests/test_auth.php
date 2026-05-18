<?php
require_once __DIR__ . '/../includes/auth.php';

function run_auth_tests() {
    $passed = 0;
    $failed = 0;

    // 1. Setup in-memory SQLite database
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        name TEXT NOT NULL,
        is_deleted INTEGER DEFAULT 0
    )");

    // 2. Insert dummy data
    $password = 'password123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, is_deleted) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin_user', $hashedPassword, 'admin', 'Admin Name', 0]);
    $stmt->execute(['driver_user', $hashedPassword, 'driver', 'Driver Name', 0]);
    $stmt->execute(['deleted_user', $hashedPassword, 'driver', 'Deleted Name', 1]);


    // Helper function for assertions
    $assert = function($condition, $testName) use (&$passed, &$failed) {
        if ($condition) {
            echo "✅ PASS: $testName\n";
            $passed++;
        } else {
            echo "❌ FAIL: $testName\n";
            $failed++;
        }
    };

    // 3. Run Tests
    echo "Running Auth Tests...\n\n";

    // Test 1: Empty credentials
    $result = authenticate_user($pdo, '', '');
    $assert($result['success'] === false && $result['error'] === 'Please enter both username and password.', 'Empty credentials');

    $result = authenticate_user($pdo, 'admin_user', '');
    $assert($result['success'] === false && $result['error'] === 'Please enter both username and password.', 'Empty password');

    $result = authenticate_user($pdo, '', 'password123');
    $assert($result['success'] === false && $result['error'] === 'Please enter both username and password.', 'Empty username');

    // Test 2: Invalid username
    $result = authenticate_user($pdo, 'nonexistent_user', 'password123');
    $assert($result['success'] === false && $result['error'] === 'Invalid username or password.', 'Invalid username');

    // Test 3: Invalid password
    $result = authenticate_user($pdo, 'admin_user', 'wrongpassword');
    $assert($result['success'] === false && $result['error'] === 'Invalid username or password.', 'Invalid password');

    // Test 4: Valid admin login
    $result = authenticate_user($pdo, 'admin_user', 'password123');
    $assert($result['success'] === true && $result['user']['username'] === 'admin_user' && $result['user']['role'] === 'admin', 'Valid admin login');

    // Test 5: Valid driver login
    $result = authenticate_user($pdo, 'driver_user', 'password123');
    $assert($result['success'] === true && $result['user']['username'] === 'driver_user' && $result['user']['role'] === 'driver', 'Valid driver login');

    // Test 6: Attempting to log in as a deleted user
    $result = authenticate_user($pdo, 'deleted_user', 'password123');
    $assert($result['success'] === false && $result['error'] === 'Invalid username or password.', 'Login as deleted user');


    echo "\nResults: $passed passed, $failed failed.\n";
    if ($failed > 0) {
        exit(1);
    }
}

run_auth_tests();

<?php
// tests/test_auth.php

require_once __DIR__ . '/../includes/auth.php';

function runTests() {
    echo "Running Auth Tests...\n";

    // Setup in-memory database
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        name TEXT NOT NULL,
        is_deleted INTEGER DEFAULT 0
    )");

    // Insert dummy users
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);

    // Valid user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, is_deleted) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['validuser', $hashedPassword, 'driver', 'Valid User', 0]);

    // Deleted user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, is_deleted) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['deleteduser', $hashedPassword, 'driver', 'Deleted User', 1]);

    $testsPassed = 0;
    $testsTotal = 0;

    // Test 1: Successful authentication
    $testsTotal++;
    $result = authenticateUser($pdo, 'validuser', 'password123');
    if ($result !== false && $result['username'] === 'validuser') {
        echo "✅ Test 1 Passed: Successful authentication.\n";
        $testsPassed++;
    } else {
        echo "❌ Test 1 Failed: Expected valid user array, got false or wrong user.\n";
    }

    // Test 2: Failed authentication (invalid password)
    $testsTotal++;
    $result = authenticateUser($pdo, 'validuser', 'wrongpassword');
    if ($result === false) {
        echo "✅ Test 2 Passed: Failed authentication (invalid password).\n";
        $testsPassed++;
    } else {
        echo "❌ Test 2 Failed: Expected false, got user array.\n";
    }

    // Test 3: Failed authentication (non-existent username)
    $testsTotal++;
    $result = authenticateUser($pdo, 'nonexistent', 'password123');
    if ($result === false) {
        echo "✅ Test 3 Passed: Failed authentication (non-existent username).\n";
        $testsPassed++;
    } else {
        echo "❌ Test 3 Failed: Expected false, got user array.\n";
    }

    // Test 4: Failed authentication (deleted user account)
    $testsTotal++;
    $result = authenticateUser($pdo, 'deleteduser', 'password123');
    if ($result === false) {
        echo "✅ Test 4 Passed: Failed authentication (deleted user account).\n";
        $testsPassed++;
    } else {
        echo "❌ Test 4 Failed: Expected false, got user array.\n";
    }

    echo "\nTests Complete: $testsPassed / $testsTotal passed.\n";

    if ($testsPassed !== $testsTotal) {
        exit(1);
    }
}

runTests();

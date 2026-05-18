<?php

require_once __DIR__ . '/../includes/dashboard_stats.php';

function testDashboardStats() {
    // Setup in-memory SQLite DB
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create required tables
    $pdo->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role TEXT,
            username TEXT,
            password TEXT,
            name TEXT,
            is_deleted INTEGER DEFAULT 0
        );

        CREATE TABLE cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            plate_number TEXT UNIQUE
        );

        CREATE TABLE wages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            driver_id INTEGER,
            amount DECIMAL(10, 2),
            is_paid INTEGER DEFAULT 0
        );

        CREATE TABLE maintenance (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            car_id INTEGER,
            type TEXT,
            maintenance_date DATE,
            next_due_days INTEGER
        );
    ");

    // Insert Users (Drivers)
    $pdo->exec("INSERT INTO users (role, username, password, name, is_deleted) VALUES ('driver', 'driver1', 'pass', 'Driver One', 0)");
    $pdo->exec("INSERT INTO users (role, username, password, name, is_deleted) VALUES ('driver', 'driver2', 'pass', 'Driver Two', 0)");
    $pdo->exec("INSERT INTO users (role, username, password, name, is_deleted) VALUES ('driver', 'driver3_deleted', 'pass', 'Driver Three', 1)"); // Deleted
    $pdo->exec("INSERT INTO users (role, username, password, name, is_deleted) VALUES ('admin', 'admin', 'pass', 'Admin', 0)"); // Admin

    // Insert Cars
    $pdo->exec("INSERT INTO cars (name, plate_number) VALUES ('Car 1', 'ABC-123')");
    $pdo->exec("INSERT INTO cars (name, plate_number) VALUES ('Car 2', 'XYZ-789')");
    $pdo->exec("INSERT INTO cars (name, plate_number) VALUES ('Car 3', 'LMN-456')");

    // Insert Wages (1000 + 500 = 1500 unpaid)
    $pdo->exec("INSERT INTO wages (driver_id, amount, is_paid) VALUES (1, 1000, 0)");
    $pdo->exec("INSERT INTO wages (driver_id, amount, is_paid) VALUES (1, 500, 0)");
    $pdo->exec("INSERT INTO wages (driver_id, amount, is_paid) VALUES (2, 2000, 1)"); // Paid

    // Insert Maintenance Records
    // Due maintenance (today is 'now')
    $pdo->exec("INSERT INTO maintenance (car_id, type, maintenance_date, next_due_days) VALUES (1, 'Oil Change', date('now', '-10 days'), 5)"); // Due (10 days ago, due in 5 days)
    $pdo->exec("INSERT INTO maintenance (car_id, type, maintenance_date, next_due_days) VALUES (2, 'Tire Rotation', date('now', '-5 days'), 10)"); // Not Due (5 days ago, due in 10 days)
    $pdo->exec("INSERT INTO maintenance (car_id, type, maintenance_date, next_due_days) VALUES (3, 'Filter Change', date('now', '-20 days'), 20)"); // Exactly Due today

    $stats = getDashboardStats($pdo);

    // Assertions
    $errors = 0;

    if ($stats['totalDrivers'] != 2) {
        echo "FAIL: Expected 2 total drivers, got " . $stats['totalDrivers'] . "\n";
        $errors++;
    } else {
        echo "PASS: Total drivers is correct.\n";
    }

    if ($stats['totalCars'] != 3) {
        echo "FAIL: Expected 3 total cars, got " . $stats['totalCars'] . "\n";
        $errors++;
    } else {
        echo "PASS: Total cars is correct.\n";
    }

    if ($stats['unpaidWages'] != 1500) {
        echo "FAIL: Expected 1500 unpaid wages, got " . $stats['unpaidWages'] . "\n";
        $errors++;
    } else {
        echo "PASS: Unpaid wages is correct.\n";
    }

    if (count($stats['dueMaintenances']) != 2) {
        echo "FAIL: Expected 2 due maintenance records, got " . count($stats['dueMaintenances']) . "\n";
        $errors++;
    } else {
        echo "PASS: Due maintenances count is correct.\n";
    }

    if ($errors === 0) {
        echo "\nAll dashboard stats tests passed successfully!\n";
    } else {
        echo "\n$errors test(s) failed.\n";
        exit(1);
    }
}

testDashboardStats();

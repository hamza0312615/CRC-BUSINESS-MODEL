<?php
// tests/test_dashboard_stats.php

require_once __DIR__ . '/../includes/dashboard_functions.php';

function runTests() {
    echo "Running Dashboard Stats Tests...\n";

    // Setup an in-memory SQLite database for testing
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create necessary tables
    $pdo->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT,
            password TEXT,
            role TEXT,
            is_deleted INTEGER DEFAULT 0
        );
        CREATE TABLE cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            plate_number TEXT,
            is_deleted INTEGER DEFAULT 0
        );
        CREATE TABLE wages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            driver_id INTEGER,
            amount REAL,
            is_paid INTEGER DEFAULT 0,
            date_recorded DATE
        );
        CREATE TABLE maintenance (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            car_id INTEGER,
            type TEXT,
            maintenance_date DATE,
            next_due_days INTEGER
        );
    ");

    // Insert dummy data

    // 2 valid drivers, 1 deleted driver, 1 admin
    $pdo->exec("
        INSERT INTO users (username, password, role, is_deleted) VALUES ('driver1', 'pass', 'driver', 0);
        INSERT INTO users (username, password, role, is_deleted) VALUES ('driver2', 'pass', 'driver', 0);
        INSERT INTO users (username, password, role, is_deleted) VALUES ('driver3', 'pass', 'driver', 1);
        INSERT INTO users (username, password, role, is_deleted) VALUES ('admin', 'pass', 'admin', 0);
    ");

    // 3 cars
    $pdo->exec("
        INSERT INTO cars (name, plate_number) VALUES ('Car 1', 'ABC 123');
        INSERT INTO cars (name, plate_number) VALUES ('Car 2', 'XYZ 789');
        INSERT INTO cars (name, plate_number) VALUES ('Car 3', 'LMN 456');
    ");

    // Wages (1 unpaid, 1 paid)
    $pdo->exec("
        INSERT INTO wages (driver_id, amount, is_paid) VALUES (1, 1500.50, 0);
        INSERT INTO wages (driver_id, amount, is_paid) VALUES (2, 2000.00, 1);
        INSERT INTO wages (driver_id, amount, is_paid) VALUES (1, 500.00, 0);
    ");

    // Maintenance (1 due, 1 not due)
    // For sqlite test, due means `date(m.maintenance_date, '+' || m.next_due_days || ' days') <= date('now')`
    // We will set one to be 10 days ago with next_due_days = 5 (due)
    // Another one to be today with next_due_days = 30 (not due)
    $pdo->exec("
        INSERT INTO maintenance (car_id, type, maintenance_date, next_due_days)
        VALUES (1, 'Oil Change', date('now', '-10 days'), 5);

        INSERT INTO maintenance (car_id, type, maintenance_date, next_due_days)
        VALUES (2, 'Tire Rotation', date('now'), 30);
    ");

    // Test getDashboardStats
    $stats = getDashboardStats($pdo);

    // Assert Total Drivers
    if ($stats['totalDrivers'] != 2) {
        throw new Exception("Expected 2 total drivers, got " . $stats['totalDrivers']);
    }

    // Assert Total Cars
    if ($stats['totalCars'] != 3) {
        throw new Exception("Expected 3 total cars, got " . $stats['totalCars']);
    }

    // Assert Unpaid Wages
    if ($stats['unpaidWages'] != 2000.50) {
        throw new Exception("Expected 2000.50 unpaid wages, got " . $stats['unpaidWages']);
    }

    // Test getDashboardStats edge case: no unpaid wages
    // Mark all as paid
    $pdo->exec("UPDATE wages SET is_paid = 1");
    $stats = getDashboardStats($pdo);
    if ($stats['unpaidWages'] != 0) {
        throw new Exception("Expected 0 unpaid wages, got " . $stats['unpaidWages']);
    }

    // Test getDueMaintenances
    $due = getDueMaintenances($pdo);

    if (count($due) != 1) {
        throw new Exception("Expected 1 due maintenance alert, got " . count($due));
    }

    if ($due[0]['car_id'] != 1 || $due[0]['type'] !== 'Oil Change') {
        throw new Exception("Due maintenance alert does not match expected values.");
    }

    echo "All tests passed successfully.\n";
}

try {
    runTests();
} catch (Exception $e) {
    echo "Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}

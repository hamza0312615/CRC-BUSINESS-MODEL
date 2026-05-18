<?php

require_once __DIR__ . '/../includes/driver_functions.php';

function test_get_current_assigned_car() {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables
    $pdo->exec("
        CREATE TABLE cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            plate_number TEXT NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE assignments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            car_id INTEGER NOT NULL,
            driver_id INTEGER NOT NULL,
            start_date TEXT NOT NULL,
            end_date TEXT
        )
    ");

    // Insert cars
    $pdo->exec("INSERT INTO cars (id, name, plate_number) VALUES (1, 'Old Car', 'OLD-123')");
    $pdo->exec("INSERT INTO cars (id, name, plate_number) VALUES (2, 'New Car', 'NEW-456')");
    $pdo->exec("INSERT INTO cars (id, name, plate_number) VALUES (3, 'Other Car', 'OTH-789')");

    $driver_id = 1;

    // Test Case 1: No assignments
    $car = get_current_assigned_car($pdo, $driver_id);
    assert($car === false, "Test Case 1 Failed: Should return false when no car is assigned.");

    // Insert a past assignment (has end_date)
    $pdo->exec("INSERT INTO assignments (car_id, driver_id, start_date, end_date) VALUES (1, $driver_id, '2023-01-01', '2023-02-01')");

    // Test Case 2: Only past assignments
    $car = get_current_assigned_car($pdo, $driver_id);
    assert($car === false, "Test Case 2 Failed: Should return false when only past assignments exist.");

    // Insert a current assignment (no end_date)
    $pdo->exec("INSERT INTO assignments (car_id, driver_id, start_date, end_date) VALUES (2, $driver_id, '2023-03-01', NULL)");

    // Test Case 3: Mixed assignments
    $car = get_current_assigned_car($pdo, $driver_id);
    assert($car !== false, "Test Case 3 Failed: Should return an array.");
    assert($car['name'] === 'New Car', "Test Case 3 Failed: Name mismatch. Expected 'New Car', got '{$car['name']}'.");
    assert($car['plate_number'] === 'NEW-456', "Test Case 3 Failed: Plate mismatch. Expected 'NEW-456', got '{$car['plate_number']}'.");

    // Test Case 4: Different driver
    $other_driver_id = 2;
    $pdo->exec("INSERT INTO assignments (car_id, driver_id, start_date, end_date) VALUES (3, $other_driver_id, '2023-03-01', NULL)");
    $car = get_current_assigned_car($pdo, $driver_id);
    assert($car['name'] === 'New Car', "Test Case 4 Failed: Should still get 'New Car' for driver 1.");

    $car2 = get_current_assigned_car($pdo, $other_driver_id);
    assert($car2['name'] === 'Other Car', "Test Case 4 Failed: Should get 'Other Car' for driver 2.");

    echo "All tests passed for get_current_assigned_car!\n";
}

test_get_current_assigned_car();

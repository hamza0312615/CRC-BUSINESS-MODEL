<?php
require_once __DIR__ . '/../includes/car_functions.php';

function setup_db() {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create schema
    $pdo->exec("
        CREATE TABLE cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            model TEXT,
            plate_number TEXT UNIQUE NOT NULL,
            color TEXT,
            private_notes TEXT
        )
    ");
    return $pdo;
}

function test_add_car_duplicate_plate() {
    $pdo = setup_db();

    // Seed database
    $pdo->exec("INSERT INTO cars (name, model, plate_number, color, private_notes) VALUES ('Honda Civic', '2022', 'ABC-123', 'White', 'Good condition')");

    // Attempt duplicate plate
    $result = add_car($pdo, 'Toyota Corolla', '2023', 'ABC-123', 'Black', 'New car');

    if ($result['success'] !== false) {
        echo "FAILED: Duplicate plate check should fail\n";
        return;
    }

    if ($result['error'] !== 'Plate number already exists.') {
        echo "FAILED: Incorrect error message. Expected 'Plate number already exists.', got '{$result['error']}'\n";
        return;
    }

    echo "PASSED: test_add_car_duplicate_plate\n";
}

function test_add_car_missing_fields() {
    $pdo = setup_db();

    // Missing name
    $result = add_car($pdo, '', '2023', 'ABC-123', 'Black', 'New car');
    if ($result['success'] !== false || $result['error'] !== 'Name and Plate Number are required.') {
        echo "FAILED: Missing name check\n";
        return;
    }

    // Missing plate
    $result = add_car($pdo, 'Toyota Corolla', '2023', '', 'Black', 'New car');
    if ($result['success'] !== false || $result['error'] !== 'Name and Plate Number are required.') {
        echo "FAILED: Missing plate check\n";
        return;
    }

    echo "PASSED: test_add_car_missing_fields\n";
}

function test_add_car_success() {
    $pdo = setup_db();

    $result = add_car($pdo, 'Toyota Corolla', '2023', 'XYZ-987', 'Black', 'New car');
    if ($result['success'] !== true || !isset($result['message']) || $result['message'] !== 'Car added successfully.') {
        echo "FAILED: Success case\n";
        return;
    }

    // Verify insertion
    $stmt = $pdo->query("SELECT * FROM cars WHERE plate_number = 'XYZ-987'");
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$car || $car['name'] !== 'Toyota Corolla') {
        echo "FAILED: Car not inserted properly\n";
        return;
    }

    echo "PASSED: test_add_car_success\n";
}

test_add_car_duplicate_plate();
test_add_car_missing_fields();
test_add_car_success();

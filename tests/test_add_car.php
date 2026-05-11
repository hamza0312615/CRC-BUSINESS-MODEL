<?php

require_once __DIR__ . '/../includes/cars.php';

function setup_db() {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE cars (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        model TEXT,
        plate_number TEXT UNIQUE NOT NULL,
        color TEXT,
        private_notes TEXT
    )");
    return $pdo;
}

echo "Testing add_car()...\n";

$pdo = setup_db();

// Test 1: Successful car insertion
$data1 = [
    'name' => 'Toyota Camry',
    'model' => '2023',
    'plate_number' => 'ABC-1234',
    'color' => 'Silver',
    'private_notes' => 'New car'
];

$result1 = add_car($pdo, $data1);
if ($result1['success'] === true && $result1['message'] === 'Car added successfully.') {
    echo "Test 1 Passed: Successful insertion.\n";
} else {
    echo "Test 1 Failed: Expected success.\n";
    print_r($result1);
}

// Verify database state for Test 1
$stmt = $pdo->query("SELECT * FROM cars WHERE plate_number = 'ABC-1234'");
$car = $stmt->fetch(PDO::FETCH_ASSOC);
if ($car && $car['name'] === 'Toyota Camry') {
    echo "Test 1 Database Verification Passed.\n";
} else {
    echo "Test 1 Database Verification Failed.\n";
}

// Test 2: Collision - plate number already exists
$data2 = [
    'name' => 'Honda Accord',
    'plate_number' => 'ABC-1234' // Duplicate
];

$result2 = add_car($pdo, $data2);
if ($result2['success'] === false && $result2['error'] === 'Plate number already exists.') {
    echo "Test 2 Passed: Plate number collision detected.\n";
} else {
    echo "Test 2 Failed: Expected collision error.\n";
    print_r($result2);
}

// Test 3: Validation error - missing name
$data3 = [
    'plate_number' => 'XYZ-9876'
];

$result3 = add_car($pdo, $data3);
if ($result3['success'] === false && $result3['error'] === 'Name and Plate Number are required.') {
    echo "Test 3 Passed: Validation error for missing name detected.\n";
} else {
    echo "Test 3 Failed: Expected validation error.\n";
    print_r($result3);
}

// Test 4: Validation error - missing plate number
$data4 = [
    'name' => 'Ford Mustang'
];

$result4 = add_car($pdo, $data4);
if ($result4['success'] === false && $result4['error'] === 'Name and Plate Number are required.') {
    echo "Test 4 Passed: Validation error for missing plate number detected.\n";
} else {
    echo "Test 4 Failed: Expected validation error.\n";
    print_r($result4);
}

echo "Testing add_car() complete.\n";

?>

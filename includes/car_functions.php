<?php

function add_car($pdo, $name, $model, $plate_number, $color, $private_notes) {
    if (empty($name) || empty($plate_number)) {
        return ['success' => false, 'error' => 'Name and Plate Number are required.'];
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE plate_number = ?");
    $stmt->execute([$plate_number]);
    if ($stmt->fetchColumn() > 0) {
        return ['success' => false, 'error' => 'Plate number already exists.'];
    }

    $stmt = $pdo->prepare("INSERT INTO cars (name, model, plate_number, color, private_notes) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $model, $plate_number, $color, $private_notes])) {
        return ['success' => true, 'message' => 'Car added successfully.'];
    }

    return ['success' => false, 'error' => 'Failed to add car.'];
}

<?php

function add_car(PDO $pdo, array $data): array {
    $name = $data['name'] ?? '';
    $model = $data['model'] ?? '';
    $plate_number = $data['plate_number'] ?? '';
    $color = $data['color'] ?? '';
    $private_notes = $data['private_notes'] ?? '';

    if ($name && $plate_number) {
        // Check plate number
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE plate_number = ?");
        $stmt->execute([$plate_number]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'error' => 'Plate number already exists.'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO cars (name, model, plate_number, color, private_notes) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $model, $plate_number, $color, $private_notes])) {
                return ['success' => true, 'message' => 'Car added successfully.'];
            } else {
                return ['success' => false, 'error' => 'Failed to add car.'];
            }
        }
    } else {
        return ['success' => false, 'error' => 'Name and Plate Number are required.'];
    }
}

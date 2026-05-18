<?php

function get_current_assigned_car(PDO $pdo, $driver_id) {
    $stmt = $pdo->prepare("
        SELECT c.name, c.plate_number
        FROM assignments a
        JOIN cars c ON a.car_id = c.id
        WHERE a.driver_id = ? AND a.end_date IS NULL
    ");
    $stmt->execute([$driver_id]);
    return $stmt->fetch();
}

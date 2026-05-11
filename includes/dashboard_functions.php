<?php

function getDashboardStats(PDO $pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'driver' AND is_deleted = 0");
    $totalDrivers = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
    $totalCars = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT SUM(amount) FROM wages WHERE is_paid = 0");
    $unpaidWages = $stmt->fetchColumn() ?: 0;

    return [
        'totalDrivers' => $totalDrivers,
        'totalCars' => $totalCars,
        'unpaidWages' => $unpaidWages
    ];
}

function getDueMaintenances(PDO $pdo) {
    // We replace DATE_ADD(..., INTERVAL ... DAY) <= CURDATE() with a standard approach for cross-DB support if needed.
    // However, DATE_ADD is MySQL specific. In SQLite, we use date(maintenance_date, '+' || next_due_days || ' days') <= date('now').
    // Since we want this to run both on MySQL (production) and SQLite (testing), let's refactor slightly, or mock it in test.
    // For now we will keep the current MySQL query and maybe alter it for SQLite later, but a cross-DB approach is better:

    // Check if the connection is sqlite
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'sqlite') {
        $query = "SELECT m.*, c.plate_number, c.name FROM maintenance m
                  JOIN cars c ON m.car_id = c.id
                  WHERE date(m.maintenance_date, '+' || m.next_due_days || ' days') <= date('now')";
    } else {
        $query = "SELECT m.*, c.plate_number, c.name FROM maintenance m
                  JOIN cars c ON m.car_id = c.id
                  WHERE DATE_ADD(m.maintenance_date, INTERVAL m.next_due_days DAY) <= CURDATE()";
    }

    $stmt = $pdo->query($query);
    return $stmt->fetchAll();
}

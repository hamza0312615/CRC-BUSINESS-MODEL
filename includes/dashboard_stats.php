<?php

function getDashboardStats(PDO $pdo) {
    // Determine the database driver
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    // Total Drivers
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'driver' AND is_deleted = 0");
    $totalDrivers = $stmt->fetchColumn();

    // Total Cars
    $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
    $totalCars = $stmt->fetchColumn();

    // Unpaid Wages
    $stmt = $pdo->query("SELECT SUM(amount) FROM wages WHERE is_paid = 0");
    $unpaidWages = $stmt->fetchColumn() ?: 0;

    // Maintenance Due
    if ($driver === 'sqlite') {
        // SQLite doesn't have DATE_ADD or CURDATE() built-in exactly like MySQL, so use date/datetime functions
        $query = "SELECT m.*, c.plate_number, c.name FROM maintenance m
                  JOIN cars c ON m.car_id = c.id
                  WHERE date(m.maintenance_date, '+' || m.next_due_days || ' days') <= date('now')";
    } else {
        // Default to MySQL syntax
        $query = "SELECT m.*, c.plate_number, c.name FROM maintenance m
                  JOIN cars c ON m.car_id = c.id
                  WHERE DATE_ADD(m.maintenance_date, INTERVAL m.next_due_days DAY) <= CURDATE()";
    }

    $stmt = $pdo->query($query);
    $dueMaintenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'totalDrivers' => $totalDrivers,
        'totalCars' => $totalCars,
        'unpaidWages' => $unpaidWages,
        'dueMaintenances' => $dueMaintenances
    ];
}

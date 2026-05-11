<?php
// includes/auth.php

/**
 * Authenticates a user against the database.
 *
 * @param PDO $pdo The database connection.
 * @param string $username The username to authenticate.
 * @param string $password The plain text password.
 * @return array|false The user record if authentication is successful, false otherwise.
 */
function authenticateUser(PDO $pdo, string $username, string $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_deleted = 0");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

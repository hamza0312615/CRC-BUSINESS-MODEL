<?php

/**
 * Authenticates a user based on username and password.
 *
 * @param PDO $pdo The database connection.
 * @param string $username The username to authenticate.
 * @param string $password The password to verify.
 * @return array An array containing 'success' boolean, and either 'user' data or an 'error' message.
 */
function authenticate_user(PDO $pdo, string $username, string $password): array {
    $username = trim($username);

    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Please enter both username and password.'];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_deleted = 0");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return ['success' => true, 'user' => $user];
    }

    return ['success' => false, 'error' => 'Invalid username or password.'];
}

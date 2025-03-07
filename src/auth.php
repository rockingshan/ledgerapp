<?php
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function login($username, $password) {
    $db = getDb();
    $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $hashedPassword = $stmt->fetchColumn();

    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        $_SESSION['logged_in'] = true;
        return true;
    }
    return false;
}
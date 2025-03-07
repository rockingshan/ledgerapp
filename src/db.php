<?php
function getDb() {
    $dbPath = __DIR__ . '/../db/ledger.db';
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they don’t exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            amount REAL NOT NULL,
            remaining_amount REAL NOT NULL,
            date TEXT NOT NULL,
            source TEXT NOT NULL,
            is_fulfilled INTEGER DEFAULT 0
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            amount REAL NOT NULL,
            date TEXT NOT NULL,
            details TEXT NOT NULL
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS payment_expense_mapping (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            payment_id INTEGER,
            expense_id INTEGER,
            allocated_amount REAL NOT NULL,
            FOREIGN KEY (payment_id) REFERENCES payments(id),
            FOREIGN KEY (expense_id) REFERENCES expenses(id)
        )
    ");

    // Seed default user if none exists (username: admin, password: password123)
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, password) VALUES ('admin', '$hashedPassword')");
    }

    return $db;
}
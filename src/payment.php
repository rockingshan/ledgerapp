<?php
require_once 'db.php';

function addPayment($amount, $date, $source) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO payments (amount, remaining_amount, date, source) VALUES (?, ?, ?, ?)");
    $stmt->execute([$amount, $amount, $date, $source]);
}

function getPaymentsPaginated($offset, $limit) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM payments ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalPayments() {
    $db = getDb();
    $stmt = $db->query("SELECT COUNT(*) FROM payments");
    return $stmt->fetchColumn();
}

function deletePayment($id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT remaining_amount, amount FROM payments WHERE id = ?");
    $stmt->execute([$id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment && $payment['remaining_amount'] == $payment['amount']) {
        $stmt = $db->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->execute([$id]);
    }
}

function getAvailableBalance() {
    $db = getDb();
    $stmt = $db->query("SELECT SUM(remaining_amount) FROM payments");
    $balance = $stmt->fetchColumn();
    return $balance !== false ? (float)$balance : 0.0; // Ensure float return, default to 0 if no records
}
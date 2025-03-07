<?php
require_once 'db.php';

function addExpense($amount, $date, $details) {
    $db = getDb();

    // Insert expense
    $stmt = $db->prepare("INSERT INTO expenses (amount, date, details) VALUES (?, ?, ?)");
    $stmt->execute([$amount, $date, $details]);
    $expenseId = $db->lastInsertId();

    // Allocate to payments (FIFO)
    $remaining = $amount;
    $stmt = $db->query("SELECT * FROM payments WHERE remaining_amount > 0 ORDER BY date ASC");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($payments as $payment) {
        if ($remaining <= 0) break;

        $allocatable = min($remaining, $payment['remaining_amount']);
        $remaining -= $allocatable;

        // Update payment
        $newRemaining = $payment['remaining_amount'] - $allocatable;
        $isFulfilled = $newRemaining == 0 ? 1 : 0;
        $stmt = $db->prepare("UPDATE payments SET remaining_amount = ?, is_fulfilled = ? WHERE id = ?");
        $stmt->execute([$newRemaining, $isFulfilled, $payment['id']]);

        // Link payment to expense
        $stmt = $db->prepare("INSERT INTO payment_expense_mapping (payment_id, expense_id, allocated_amount) VALUES (?, ?, ?)");
        $stmt->execute([$payment['id'], $expenseId, $allocatable]);
    }
}

function getExpensesPaginated($offset, $limit) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM expenses ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalExpenses() {
    $db = getDb();
    $stmt = $db->query("SELECT COUNT(*) FROM expenses");
    return $stmt->fetchColumn();
}

function getPaymentMappings($expenseId) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM payment_expense_mapping WHERE expense_id = ?");
    $stmt->execute([$expenseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
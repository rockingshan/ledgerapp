<?php
session_start();
require_once '../src/auth.php';
require_once '../src/payment.php';
require_once '../src/expense.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    addPayment($_POST['amount'], $_POST['date'], $_POST['source']);
}

// Handle expense form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    addExpense($_POST['amount'], $_POST['date'], $_POST['details']);
}

// Pagination settings
$recordsPerPage = 10;
$paymentPage = isset($_GET['payment_page']) ? (int)$_GET['payment_page'] : 1;
$expensePage = isset($_GET['expense_page']) ? (int)$_GET['expense_page'] : 1;
$paymentOffset = ($paymentPage - 1) * $recordsPerPage;
$expenseOffset = ($expensePage - 1) * $recordsPerPage;

// Get paginated data and available balance
$payments = getPaymentsPaginated($paymentOffset, $recordsPerPage);
$expenses = getExpensesPaginated($expenseOffset, $recordsPerPage);
$totalPayments = getTotalPayments();
$totalExpenses = getTotalExpenses();
$totalPaymentPages = ceil($totalPayments / $recordsPerPage);
$totalExpensePages = ceil($totalExpenses / $recordsPerPage);
$availableBalance = getAvailableBalance();

// Today’s date for default input
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <h2>Ledger Dashboard</h2>
        <div class="nav-right">
            <a href="logout.php">Logout</a>
            <span class="balance">Available Balance: &#8377; <?php echo number_format($availableBalance, 2); ?></span>
        </div>
    </nav>
    <div class="container">
        <!-- Add Payment -->
        <h3>Add Payment</h3>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" step="0.01" required>
            <input type="date" name="date" value="<?php echo $today; ?>" required>
            <input type="text" name="source" placeholder="Source" required>
            <button type="submit" name="add_payment">Add Payment</button>
        </form>

        <!-- Add Expense -->
        <h3>Add Expense</h3>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" step="0.01" required>
            <input type="date" name="date" value="<?php echo $today; ?>" required>
            <input type="text" name="details" placeholder="Details" required>
            <button type="submit" name="add_expense">Add Expense</button>
        </form>

        <!-- Payments List -->
        <h3>Payments (Page <?php echo $paymentPage; ?> of <?php echo $totalPaymentPages; ?>)</h3>
        <table>
            <tr><th>ID</th><th>Amount</th><th>Remaining</th><th>Date</th><th>Source</th><th>Actions</th></tr>
            <?php foreach ($payments as $p): ?>
                <tr class="<?php echo $p['is_fulfilled'] ? 'fulfilled' : ''; ?>">
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo $p['amount']; ?></td>
                    <td><?php echo $p['remaining_amount']; ?></td>
                    <td><?php echo $p['date']; ?></td>
                    <td><?php echo $p['source']; ?></td>
                    <td>
                        <?php if ($p['remaining_amount'] == $p['amount']): ?>
                            <a href="?delete_payment=<?php echo $p['id']; ?>&payment_page=<?php echo $paymentPage; ?>&expense_page=<?php echo $expensePage; ?>">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="pagination">
            <?php if ($paymentPage > 1): ?>
                <a href="?payment_page=<?php echo $paymentPage - 1; ?>&expense_page=<?php echo $expensePage; ?>">Previous</a>
            <?php endif; ?>
            <?php if ($paymentPage < $totalPaymentPages): ?>
                <a href="?payment_page=<?php echo $paymentPage + 1; ?>&expense_page=<?php echo $expensePage; ?>">Next</a>
            <?php endif; ?>
        </div>

        <!-- Expenses List -->
        <h3>Expenses (Page <?php echo $expensePage; ?> of <?php echo $totalExpensePages; ?>)</h3>
        <table>
            <tr><th>ID</th><th>Amount</th><th>Date</th><th>Details</th><th>Linked Payments</th></tr>
            <?php foreach ($expenses as $e): ?>
                <tr>
                    <td><?php echo $e['id']; ?></td>
                    <td><?php echo $e['amount']; ?></td>
                    <td><?php echo $e['date']; ?></td>
                    <td><?php echo $e['details']; ?></td>
                    <td>
                        <?php
                        $mappings = getPaymentMappings($e['id']);
                        foreach ($mappings as $m) {
                            echo "Payment #{$m['payment_id']}: {$m['allocated_amount']}<br>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="pagination">
            <?php if ($expensePage > 1): ?>
                <a href="?payment_page=<?php echo $paymentPage; ?>&expense_page=<?php echo $expensePage - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php if ($expensePage < $totalExpensePages): ?>
                <a href="?payment_page=<?php echo $paymentPage; ?>&expense_page=<?php echo $expensePage + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
if (isset($_GET['delete_payment'])) {
    deletePayment($_GET['delete_payment']);
    header("Location: index.php?payment_page=$paymentPage&expense_page=$expensePage");
    exit;
}
?>
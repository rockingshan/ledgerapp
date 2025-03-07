Ledger Tracking Web App


A simple, local ledger app to track payments and expenses with FIFO allocation.

Overview
The Ledger Tracking Web App is a lightweight, PHP-based application designed to help you manage your bank account transactions locally. Built with PHP 8.1 and SQLite, it allows you to manually track payments (income) and expenses, automatically allocating expenses to the oldest available payments using a First-In-First-Out (FIFO) approach. All data is stored securely in a local SQLite database, ensuring privacy and simplicity.

Whether you're tracking business income, personal gifts, or miscellaneous deposits, this app provides a clean interface to monitor your finances with ease.

Features
Authentication: Secure login with a stunning, gradient-themed login page.
Payment Tracking: Add, edit, and delete payments (with restrictions on used payments).
Expense Tracking: Record expenses with automatic FIFO allocation to payments.
Pagination: Displays the latest 20 payments and expenses per page, sorted by creation order.
Available Balance: Real-time sum of remaining payment balances in the header.
Visual Cues: Fulfilled payments highlighted in light green for quick identification.
Local Storage: All data stored in a single SQLite database file (ledger.db).
Responsive Design: Simple, navigable UI with a modern look.
Tech Stack
Backend: PHP 8.1
Database: SQLite
Frontend: HTML, CSS (custom styling)
Server: PHP’s built-in development server
Project Structure

/ledger-app
  /db
    ledger.db          # SQLite database file
  /public
    index.php         # Dashboard and main app
    login.php         # Stunning login page
    logout.php        # Logout script
    styles.css        # Custom CSS styling
  /src
    db.php            # Database connection and schema
    auth.php          # Authentication logic
    payment.php       # Payment CRUD operations
    expense.php       # Expense CRUD and allocation logic
  README.md           # This file
Setup Instructions
Prerequisites
PHP 8.1+ with SQLite extension enabled
A web browser (e.g., Chrome, Firefox)
Git (optional, for cloning)
Installation
Clone the Repository (if using Git):

Or download and extract the project files manually.
Start the PHP Server:
php -S localhost:8000 -t public
Access the App:
Open your browser and go to http://localhost:8000/login.php.
Log in with the default credentials:
Username: admin
Password: password123
Clear Test Data (Optional):
To start fresh with real data, see the Resetting the Database section.
Usage
Log In:
Use the default credentials or modify src/db.php to change the user.
Add Payments:
Enter the amount, date (defaults to today), and source (e.g., "Business", "Gift").
Click "Add Payment".
Add Expenses:
Enter the amount, date (defaults to today), and details (e.g., "Groceries").
Click "Add Expense". Expenses will auto-allocate to the oldest payments by date.
View Data:
Payments and expenses are listed in creation order (by ID).
Use "Previous" and "Next" links to navigate pages.
Check the "Available Balance" in the header.
Delete Payments:
Only payments with full remaining balance can be deleted (click "Delete").


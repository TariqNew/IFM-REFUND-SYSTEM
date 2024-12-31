<?php
include_once '../../DB_connection.php';
session_start();

if (isset($_SESSION['student_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        $student_id = $_SESSION['student_id'];
    }
}
else {
    header("location: ../../req/login.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $refund_source = $_POST['source'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $amount = floatval($_POST['amount']); // Cast to float for numeric validation

    // Validate form data
    if (empty($refund_source) || empty($bank_name) || empty($account_number) || $amount <= 0) {
        echo "All fields are required, and the amount must be greater than 0.";
    } else {
        try {
            // Prepare and execute insert query using PDO
            $query = "INSERT INTO refund (student_id, refund_source, bank_name, account_number, amount) 
                      VALUES (:student_id, :refund_source, :bank_name, :account_number, :amount)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':student_id' => $student_id,
                ':refund_source' => $refund_source,
                ':bank_name' => $bank_name,
                ':account_number' => $account_number,
                ':amount' => $amount,
            ]);

            // Redirect on success
            header("Location: ../RefundView.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Application Form</title>
    <link rel="stylesheet" href="../../css/refund.css">
</head>
<body>
    <h2>Refund Application Form</h2>
    <form action="RefundForm.php" method="POST">

        <label for="source">Refund Source:</label>
        <select id="source" name="source" required>
            <option value="Fee Excess">Fee Excess</option>
            <option value="Graduation">Graduation</option>
            <option value="Exam Appeal">Exam Appeal</option>
        </select>

        <label for="bank_name">Bank Name:</label>
        <select id="bank_name" name="bank_name" required>
            <option value="CRDB">CRDB</option>
            <option value="NMB">NMB</option>
            <option value="NBC">NBC</option>
        </select>

        <label for="account_number">Account Number:</label>
        <input type="text" id="account_number" name="account_number" required placeholder="Enter your account number">

        <label for="amount">Refund Amount (in USD):</label>
        <input type="number" id="amount" name="amount" required placeholder="Enter the refund amount" min="0" step="0.01">

        <button type="submit">Submit Refund Request</button>
    </form>
</body>
</html>

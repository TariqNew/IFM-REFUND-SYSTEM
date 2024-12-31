<?php 
session_start();
if (isset($_SESSION['student_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Student') {
     include "../DB_connection.php";


     $student_id = $_SESSION['student_id'];

     }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student - Grade Summary</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../logo.png">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php 
        include "inc/navbar.php";

     ?>
<body>
<div class="container mt-4" style="background:#faf8f4; padding: 10px; border-radius: 5px;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Invoices and Payments</h2>
    </div>

    <!-- Current Invoices -->
    <div class="mb-4">
        <h5>2024/2025 Invoices</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>SN</th>
                    <th>INVOICE #</th>
                    <th>CONTROL #</th>
                    <th>YOS</th>
                    <th>INV AMOUNT</th>
                    <th>PAID</th>
                    <th>BALANCE</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        PF138S196602392018 <br>
                        <small><a href="#" class="text-decoration-none">Programme Fee</a></small> <br>
                        <small><a href="#" class="text-decoration-none">Download Invoice</a></small>
                    </td>
                    <td><a href="#" class="text-decoration-none">Request control number</a></td>
                    <td>Third Year Semester 1</td>
                    <td>50,000.00 TZS</td>
                    <td>0.00 TZS</td>
                    <td>50,000.00 TZS</td>
                    <td class="text-danger">Unpaid</td>
                    <td><a href="#" class="text-decoration-none">View Payments</a></td>
                </tr>
            </tbody>
        </table>

        <!-- Programme Fee Summary -->
        <div class="border p-3 mt-3">
            <h6>Programme Fee Summary</h6>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Total Invoices</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Actual Balance</td>
                        <td>50,000.00</td>
                    </tr>
                    <tr>
                        <td>Partial Balance (With Loan Allocation Consideration)</td>
                        <td>50,000.00</td>
                    </tr>
                </tbody>
            </table>
            <div class="text-end">
                <a href="./data/RefundForm.php" class="btn btn-primary">Request OverPayment</a>
            </div>

        </div>
    </div>

    <!-- Previous Invoices -->
    <div class="mb-4">
        <h5>2023/2024 Invoices</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>SN</th>
                    <th>INVOICE #</th>
                    <th>CONTROL #</th>
                    <th>YOS</th>
                    <th>INV AMOUNT</th>
                    <th>PAID</th>
                    <th>BALANCE</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>PF225S196602392018</td>
                    <td>Control Number</td>
                    <td>Second Year</td>
                    <td>50,000.00 TZS</td>
                    <td>50,000.00 TZS</td>
                    <td>0.00 TZS</td>
                    <td class="text-success">Paid</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php 

}else {
	header("Location: ../login.php?error=first reject");
	exit;
} 

?>
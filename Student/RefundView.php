<?php 
session_start();
if (isset($_SESSION['student_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Student') {
       include "../DB_connection.php";

       $student_id = $_SESSION['student_id']; // Current logged-in student's ID

       // Fetch refund records along with the student's first and last name
       try {
           $query = "
               SELECT refund.*, CONCAT(students.fname, ' ', students.lname) AS full_name
               FROM refund
               JOIN students ON refund.student_id = students.student_id
               WHERE refund.student_id = :student_id
           ";
           $stmt = $conn->prepare($query);
           $stmt->execute([':student_id' => $student_id]);
           $refunds = $stmt->fetchAll(PDO::FETCH_ASSOC);
       } catch (PDOException $e) {
           echo "Error: " . $e->getMessage();
           exit;
       }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Refund Applications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include "inc/navbar.php"; ?>

    <div class="container mt-5">
        <h3>Refund History</h3>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= $_GET['error'] ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success mt-3" role="alert">
                <?= $_GET['success'] ?>
            </div>
        <?php } ?>

        <?php if ($refunds) { ?>
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Refund Source</th>
                        <th scope="col">Bank Name</th>
                        <th scope="col">Account Number</th>
                        <th scope="col">Amount (USD)</th>
                        <th scope="col">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($refunds as $refund) { $i++; ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= htmlspecialchars($refund['full_name']) ?></td>
                        <td><?= htmlspecialchars($refund['refund_source']) ?></td>
                        <td><?= htmlspecialchars($refund['bank_name']) ?></td>
                        <td><?= htmlspecialchars($refund['account_number']) ?></td>
                        <td><?= number_format($refund['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($refund['created_at']) ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <div class="alert alert-info mt-3" role="alert">
                No refund applications found.
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
<?php 

  } else {
    header("Location: ../login.php");
    exit;
  } 
} else {
    header("Location: ../login.php");
    exit;
} 

?>

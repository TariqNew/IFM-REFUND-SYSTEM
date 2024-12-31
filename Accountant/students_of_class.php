<?php
session_start();

if (isset($_SESSION['teacher_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Teacher') {
    include "../DB_connection.php";

    // Initialize variables
    $refunds = [];
    $pendingRefunds = [];

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $refundId = $_POST['refund_id'] ?? null;
        $reason = $_POST['reason'] ?? null;

        try {
            if ($action == 'approve' && $refundId) {
                // Approve refund and move to pending batch
                $sql = "UPDATE refund SET status = 'Pending Batch' WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $refundId, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = "Refund approved and moved to pending batch.";
            } elseif ($action == 'reject' && $refundId) {
                if (empty($reason)) {
                    throw new Exception("Rejection reason is required.");
                }

                // Fetch refund details
                $sql = "SELECT * FROM refund WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $refundId, PDO::PARAM_INT);
                $stmt->execute();
                $refund = $stmt->fetch();

                if ($refund) {
                    // Insert into refund_rejected
                    $sql = "INSERT INTO refund_rejected (student_id, student_name, refund_source, account_number, 
                                                        amount, bank_name, reason) 
                            VALUES (:student_id, :student_name, :refund_source, :account_number, 
                                    :amount, :bank_name, :reason)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':student_id' => $refund['student_id'],
                        ':student_name' => $refund['student_name'],
                        ':refund_source' => $refund['refund_source'],
                        ':account_number' => $refund['account_number'],
                        ':amount' => $refund['amount'],
                        ':bank_name' => $refund['bank_name'],
                        ':reason' => $reason
                    ]);

                    // Delete from refund table
                    $sql = "DELETE FROM refund WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $refundId, PDO::PARAM_INT);
                    $stmt->execute();
                    $_SESSION['success'] = "Refund rejected and moved to the rejected list.";
                }
            } elseif ($action == 'submit_batch') {
                // Create new batch
                $conn->beginTransaction();
                $sql = "INSERT INTO refund_batch () VALUES ()";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $batchId = $conn->lastInsertId();

                // Link refunds to the batch
                $sql = "UPDATE refund SET status = 'Submitted', batch_id = :batch_id 
                        WHERE status = 'Pending Batch'";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':batch_id', $batchId, PDO::PARAM_INT);
                $stmt->execute();
                $conn->commit();

                $_SESSION['success'] = "Batch submitted successfully.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch all refunds except those already approved and in the 'Pending Batch'
    $sql = "SELECT r.*, s.fname AS first_name, s.lname AS last_name 
            FROM refund r
            JOIN students s ON r.student_id = s.student_id 
            WHERE r.status != 'Submitted' AND r.status != 'Pending Batch'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $refunds = $stmt->fetchAll();

    // Fetch pending batch refunds
    $sql = "SELECT r.*, s.fname AS first_name, s.lname AS last_name 
            FROM refund r
            JOIN students s ON r.student_id = s.student_id 
            WHERE r.status = 'Pending Batch'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pendingRefunds = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "inc/navbar.php"; ?>

    <div class="container mt-5">
        <h2>Refund Management</h2>

        <!-- Notifications -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); } ?>
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); } ?>

        <!-- Refunds Table -->
        <h3>All Refunds</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($refunds)) { ?>
                    <?php foreach ($refunds as $index => $refund) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($refund['first_name']) . ' ' . htmlspecialchars($refund['last_name']) ?></td>
                            <td><?= htmlspecialchars($refund['amount']) ?></td>
                            <td><?= htmlspecialchars($refund['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="refund_id" value="<?= htmlspecialchars($refund['id']) ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <button class="btn btn-danger btn-sm btn-reject" data-refund-id="<?= htmlspecialchars($refund['id']) ?>">Reject</button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No refunds found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pending Batch -->
        <h3>Pending Batch Refunds</h3>
        <form method="POST">
            <button type="submit" name="action" value="submit_batch" class="btn btn-primary mb-3">Submit Batch</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pendingRefunds)) { ?>
                    <?php foreach ($pendingRefunds as $index => $refund) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($refund['first_name']) . ' ' . htmlspecialchars($refund['last_name']) ?></td>
                            <td><?= htmlspecialchars($refund['amount']) ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3">No pending batch refunds.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.btn-reject').forEach(button => {
            button.addEventListener('click', function () {
                const refundId = this.dataset.refundId;
                const reason = prompt("Enter rejection reason:");
                if (reason) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    form.innerHTML = `
                        <input type="hidden" name="refund_id" value="${refundId}">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="reason" value="${reason}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>

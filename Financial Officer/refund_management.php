<?php
session_start();
include "../DB_connection.php";
require_once('../tcpdf/tcpdf.php'); // Include TCPDF for generating reports

if (isset($_SESSION['admin_id'])) {
    // Fetch all batches
    $sql = "SELECT * FROM refund_batch";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $batches = $stmt->fetchAll();

    // Mark refund as paid or rejected
    if (isset($_POST['update_refund_status'])) {
        $refundId = $_POST['refund_id'];
        $status = $_POST['status'];

        $sqlUpdate = "UPDATE refund SET status = :status WHERE id = :refund_id";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':refund_id', $refundId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Generate PDF report when requested
    if (isset($_GET['generate_report'])) {
        $batchId = $_GET['batch_id'];

        $sqlRefunds = "SELECT r.status, r.reason FROM refund r WHERE r.batch_id = :batch_id";
        $stmt = $conn->prepare($sqlRefunds);
        $stmt->bindParam(':batch_id', $batchId, PDO::PARAM_INT);
        $stmt->execute();
        $refunds = $stmt->fetchAll();

        $paidCount = 0;
        $rejectedCount = 0;
        $rejectedReasons = [];

        foreach ($refunds as $refund) {
            if ($refund['status'] == 'Paid') {
                $paidCount++;
            } elseif ($refund['status'] == 'Rejected') {
                $rejectedCount++;
                $rejectedReasons[] = $refund['reason'];
            }
        }

        // Generate PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        $pdf->Cell(0, 10, "Batch Report for Batch ID: $batchId", 0, 1, 'C');
        $pdf->Cell(0, 10, "Total Paid Refunds: $paidCount", 0, 1);
        $pdf->Cell(0, 10, "Total Rejected Refunds: $rejectedCount", 0, 1);

        if ($rejectedCount > 0) {
            $pdf->Cell(0, 10, "Reasons for Rejection:", 0, 1);
            foreach ($rejectedReasons as $reason) {
                $pdf->Cell(0, 10, "- $reason", 0, 1);
            }
        }

        $pdf->Output('batch_report.pdf', 'I');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .batch-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .no-batches {
            text-align: center;
            padding: 50px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4" style="font-weight: 700;">Batch Management</h2>

        <?php if (empty($batches)) { ?>
            <div class="no-batches">
                <h4>No available batches at the moment</h4>
                <p>Please check back later or contact the administrator for more information.</p>
            </div>
        <?php } else { ?>
            <?php foreach ($batches as $batch) { ?>
                <div class="batch-container">
                    <h3>Batch ID: <?= $batch['id'] ?> | Date: <?= $batch['batch_date'] ?> | Status: <?= $batch['financial_officer_status'] ?></h3>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Refund ID</th>
                                <th>Account Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlRefunds = "SELECT * FROM refund WHERE batch_id = :batch_id";
                            $stmt = $conn->prepare($sqlRefunds);
                            $stmt->bindParam(':batch_id', $batch['id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $refunds = $stmt->fetchAll();

                            foreach ($refunds as $refund) { ?>
                                <tr>
                                    <td><?= $refund['id'] ?></td>
                                    <td><?= $refund['account_number'] ?></td>
                                    <td><?= $refund['status'] ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                                            <button type="submit" name="update_refund_status" value="Paid" class="btn btn-success btn-sm">Mark Paid</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                                            <button type="submit" name="update_refund_status" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <a href="?generate_report=true&batch_id=<?= $batch['id'] ?>" class="btn btn-primary">Generate Report</a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>


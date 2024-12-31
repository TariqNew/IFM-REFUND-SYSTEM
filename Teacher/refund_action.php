<?php
session_start();
if (isset($_SESSION['teacher_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Teacher') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include "../DB_connection.php";

        $refund_id = $_POST['refund_id'];
        $action = $_POST['action'];

        // Determine new status based on action
        $new_status = ($action == 'approve') ? 'approved' : 'rejected';

        // Update the refund status in the database
        $sql = "UPDATE refund SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $refund_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Refund successfully $new_status.";
        } else {
            $_SESSION['error'] = "Failed to update refund status.";
        }

        header("Location: teacher_refunds.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>

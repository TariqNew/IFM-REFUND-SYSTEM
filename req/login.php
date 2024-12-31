<?php 
session_start();

if (isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['role'])) {
    include "../DB_connection.php";

    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    // Validate input
    if (empty($uname)) {
        $em = "Username is required";
        header("Location: ../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "An error occurred";
        header("Location: ../login.php?error=$em");
        exit;
    } else {
        try {
            // Role-based SQL query
            if ($role == '1') {
                $sql = "SELECT * FROM admin WHERE username = :uname";
                $roleName = "Admin";
            } else if ($role == '2') {
                $sql = "SELECT * FROM teachers WHERE username = :uname";
                $roleName = "Teacher";
            } else if ($role == '3') {
                $sql = "SELECT * FROM students WHERE username = :uname";
                $roleName = "Student";
            } else if ($role == '4') {
                $sql = "SELECT * FROM registrar_office WHERE username = :uname";
                $roleName = "Registrar Office";
            } else {
                $em = "Invalid role selection.";
                header("Location: ../login.php?error=$em");
                exit;
            }

            // Prepare and execute query
            $stmt = $conn->prepare($sql);
            $stmt->execute([':uname' => $uname]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $dbPassword = $user['password']; // Password stored in the database

                // Verify password (assumes passwords are hashed using password_hash())
                if ($pass === $dbPassword) {
                    $_SESSION['role'] = $roleName;

                    // Set session variables and redirect based on role
                    if ($roleName === 'Admin') {
                        $_SESSION['admin_id'] = $user['admin_id'];
                        header("Location: ../Financial Officer/index.php");
                    } elseif ($roleName === 'Teacher') {
                        $_SESSION['teacher_id'] = $user['teacher_id'];
                        header("Location: ../Accountant/index.php");
                    } elseif ($roleName === 'Student') {
                        $_SESSION['student_id'] = $user['student_id'];
                        header("Location: ../Student/index.php");
                    } elseif ($roleName === 'Registrar Office') {
                        $_SESSION['r_user_id'] = $user['r_user_id'];
                        header("Location: ../RegistrarOffice/index.php");
                    }
                    exit;
                } else {
                    $em = "Incorrect Password";
                    header("Location: ../login.php?error=$em");
                    exit;
                }
            } else {
                $em = "Incorrect Username or Password";
                header("Location: ../login.php?error=$em");
                exit;
            }
        } catch (PDOException $e) {
            $em = "Database error: " . $e->getMessage();
            header("Location: ../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>

<?php
session_start();
include 'db_connect.php'; // your database connection

if(isset($_POST['username'], $_POST['password'], $_POST['role'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            // Correct login
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../index.php"); // go to home
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No user found with that username and role.";
    }
} else {
    $error = "Please fill all fields.";
}

// Optional: send back error to login page
$_SESSION['login_error'] = $error;
header("Location: ../login.html");
exit;
?>

<?php
session_start();
include 'config.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = trim($_POST['password']);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already exists!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $hashed_password, $role);

        if ($insert->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            switch($role){
                case 'user':
                    header("Location: ../dashboards/user/user_dashboard.php");
                    break;
                case 'player':
                    header("Location: ../dashboards/player/player_dashboard.php");
                    break;
                case 'team':
                    header("Location: ../dashboards/team/team_dashboard.php");
                    break;
                case 'admin':
                    header("Location: ../dashboards/admin/admin_dashboard.php");
                    break;
            }
            exit();
        } else {
            echo "Error: " . $conn->error;
        }

        $insert->close();
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
session_start();
include('php/db_connect.php'); // Make sure path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username && $password && $role) {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            echo "<script>alert('Username already exists!');</script>";
        } else {
            $stmt->close();

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $role);
            $stmt->execute();
            $stmt->close();

            // Log the user in
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect to index.php
            header("Location: index.php");
            exit;
        }
    } else {
        echo "<script>alert('Please fill in all fields!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
header { background: #1B1464; color: #fff; padding: 20px 0; text-align: center; }
header nav a { color: #fff; margin: 0 15px; text-decoration: none; font-weight: bold; }
header nav a:hover { color: #FFB400; }

.form-container { background: #fff; padding: 30px; max-width: 400px; margin: 50px auto; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.2); }
.form-container h2 { text-align: center; color: #1B1464; margin-bottom: 20px; }
.form-container label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-container input, .form-container select { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
.form-container button { width: 100%; padding: 12px; background: #37003c; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
.form-container button:hover { background: #d0008f; }
.form-container p { text-align: center; }
.form-container p a { color: #37003c; text-decoration: none; font-weight: bold; }
.form-container p a:hover { color: #d0008f; }

footer { text-align: center; padding: 20px; background: #1B1464; color: #fff; margin-top: 50px; }
</style>
</head>
<body>
<header>
    <h1>Football Management System</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.html">Login</a>
    </nav>
</header>

<main>
    <div class="form-container">
        <h2>Register</h2>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="player">Player</option>
                <option value="team">Team</option>
                <option value="admin">Admin</option>
            </select>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.html">Login here</a></p>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System</p>
</footer>
</body>
</html>

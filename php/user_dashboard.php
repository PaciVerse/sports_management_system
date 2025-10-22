<?php
session_start();

// Check if user is logged in and role is 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: ../../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Football Management System</h1>
        <nav>
            <a href="../../index.html">Home</a>
            <a href="../../php/logout.php">Logout</a>
        </nav>
    </div>
</header>

<main>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <div class="dashboard-grid">
            <a class="card" href="#">Upcoming Fixtures</a>
            <a class="card" href="#">Buy Tickets</a>
            <a class="card" href="#">View Teams</a>
            <a class="card" href="#">Profile</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System</p>
</footer>
</body>
</html>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Football Management System</title>
<link rel="stylesheet" href="css/style.css">
<style>
/* Navbar for roles */
.navbar-links a {
    margin-left: 20px;
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.navbar-links a:hover {
    color: #FFB400; /* accent yellow */
}
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Football Management System</h1>
        <nav class="navbar-links">
            <?php if(isset($_SESSION['username'])): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/users_manage.php">Users</a>
                    <a href="admin/teams_manage.php">Teams</a>
                    <a href="admin/fixtures_manage.php">Fixtures</a>
                    <a href="admin/news_manage.php">News</a>
                    <a href="admin/stadiums_manage.php">Stadiums</a>
                <?php elseif($_SESSION['role'] === 'team'): ?>
                    <a href="team/fixtures_manage.php">Fixtures</a>
                    <a href="team/news.php">News</a>
                <?php elseif($_SESSION['role'] === 'player'): ?>
                    <a href="player/player_dashboard.php">Dashboard</a>
                <?php else: // regular user ?>
                    <a href="user/user_dashboard.php">Dashboard</a>
                    <a href="fixtures.php">Fixtures</a>
                    <a href="news.php">News</a>
                <?php endif; ?>
                <a href="php/logout.php">Logout</a>

            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Welcome to the Football Management System</h2>
        <p>Manage teams, players, fixtures, and tickets easily.</p>
        <div class="hero-buttons">
            <?php if(!isset($_SESSION['username'])): ?>
                <a href="login.html" class="btn">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

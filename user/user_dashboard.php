<?php
session_start();
include('../php/db_connect.php');

// Only regular users
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'user'){
    header("Location: ../login.html");
    exit;
}

// Fetch news
$news_result = $conn->query("SELECT * FROM news ORDER BY id DESC LIMIT 3");

// Fetch upcoming fixtures
$fixtures_result = $conn->query("SELECT * FROM fixtures WHERE match_date >= CURDATE() ORDER BY match_date ASC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - Football Management System</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
.container { width: 95%; max-width: 1200px; margin: 20px auto; }

/* Navbar */
.navbar-links { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
.navbar-links a, .navbar-links span { color: #fff; text-decoration: none; font-weight: bold; }
.navbar-links a:hover { color: #FFB400; }
header { background: #1B1464; padding: 15px 0; }

/* Dashboard grid */
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }

/* Cards */
.card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px; }
.card h3 { margin-top: 0; color: #1B1464; }

/* News & Fixtures list */
.list-item { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
.list-item:last-child { border-bottom: none; }

/* Welcome Banner */
.welcome-banner { background: #37003c; color: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; text-align: center; }
.welcome-banner h2 { margin: 0; }

/* Quick Links Styling */
.quick-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 10px;
}
.quick-link {
    display: block;
    background-color: #1B1464;
    color: #fff;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}
.quick-link:hover {
    background-color: #37003c;
    transform: translateY(-2px);
}

/* Footer */
footer { text-align: center; margin-top: 50px; padding: 20px 0; background: #1B1464; color: #fff; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Football Management System</h1>
        <nav class="navbar-links">
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="../index.php">Home</a>
            <a href="user_dashboard.php">Dashboard</a>
            <a href="../php/logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="welcome-banner">
        <h2>Hello <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <p>Here’s what’s happening in the football world today.</p>
    </div>

    <div class="dashboard-grid">
        <!-- News Card -->
        <div class="card">
            <h3>📰 Latest News</h3>
            <?php if($news_result->num_rows > 0): ?>
                <?php while($news = $news_result->fetch_assoc()): ?>
                    <div class="list-item">
                        <strong><?= htmlspecialchars($news['title']) ?></strong>
                        <p><?= htmlspecialchars($news['content']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No news available.</p>
            <?php endif; ?>
        </div>

        <!-- Upcoming Fixtures -->
        <div class="card">
            <h3>⚽ Upcoming Fixtures</h3>
            <?php if($fixtures_result->num_rows > 0): ?>
                <?php while($fixture = $fixtures_result->fetch_assoc()): ?>
                    <div class="list-item">
                        <strong><?= htmlspecialchars($fixture['home_team']) ?> vs <?= htmlspecialchars($fixture['away_team']) ?></strong>
                        <p>Date: <?= htmlspecialchars($fixture['match_date']) ?> | Stadium: <?= htmlspecialchars($fixture['venue']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No upcoming fixtures.</p>
            <?php endif; ?>
        </div>

        <!-- Quick Links Card -->
        <div class="card">
            <h3>🔗 Quick Links</h3>
            <div class="quick-links">
                <a href="#" class="quick-link">📰 View All News</a>
                <a href="#" class="quick-link">⚽ View All Fixtures</a>
                <a href="#" class="quick-link">🚪 Logout</a>
            </div>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

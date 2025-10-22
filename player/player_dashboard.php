<?php
session_start();
include('../php/db_connect.php');

// Only player can access
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'player'){
    header("Location: ../login.html");
    exit;
}

// Fetch all upcoming fixtures
$fixtures_result = $conn->query("SELECT * FROM fixtures ORDER BY match_date ASC");

// Fetch news
$news_result = $conn->query("SELECT * FROM news ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Player Dashboard</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    margin: 0;
    padding: 0;
}
.container { width: 90%; max-width: 1200px; margin: 50px auto; }

header {
    background: #1B1464;
    color: #fff;
    padding: 20px 0;
    margin-bottom: 40px;
}
header nav a {
    margin-left: 20px;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}
header nav a:hover { color: #FFB400; }
header nav span { margin-right: 20px; }

h2 { color: #1B1464; margin-bottom: 20px; }

/* Card Layout */
.cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 50px; }
.card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.card:hover { transform: translateY(-5px); }
.card h3 { margin-top: 0; color: #1B1464; }
.card p { margin: 5px 0; }

/* Footer */
footer { text-align: center; margin-top: 50px; padding: 20px 0; background: #1B1464; color: #fff; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Player Dashboard</h1>
        <nav>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Player)</span>
            <a href="../index.php">Home</a>
            <a href="player_dashboard.php">Dashboard</a>
            <a href="../php/logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="container">
    <h2>Upcoming Fixtures</h2>
    <div class="cards">
        <?php while($fixture = $fixtures_result->fetch_assoc()): ?>
        <div class="card">
            <h3><?= htmlspecialchars($fixture['home_team']) ?> vs <?= htmlspecialchars($fixture['away_team']) ?></h3>
            <p><strong>Date:</strong> <?= htmlspecialchars($fixture['match_date']) ?></p>
            <p><strong>Stadium:</strong> <?= htmlspecialchars($fixture['venue']) ?></p>
        </div>
        <?php endwhile; ?>
    </div>

    <h2>Latest News</h2>
    <div class="cards">
        <?php while($news = $news_result->fetch_assoc()): ?>
        <div class="card">
            <h3><?= htmlspecialchars($news['title']) ?></h3>
            <p><?= htmlspecialchars($news['content']) ?></p>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

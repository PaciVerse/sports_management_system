<?php
session_start();
include('../php/db_connect.php');

// Redirect non-team users
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'team'){
    header("Location: ../index.php");
    exit;
}

// Fetch teams
$teams_result = $conn->query("SELECT id, team_name FROM teams ORDER BY team_name ASC");

// Fetch stadiums
$stadiums_result = $conn->query("SELECT id, stadium_name FROM stadiums ORDER BY stadium_name ASC");

// Handle Add Fixture form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fixture'])) {
    $team1 = $_POST['team1'] ?? '';
    $team2 = $_POST['team2'] ?? '';
    $match_date = $_POST['match_date'] ?? '';
    $stadium = $_POST['stadium'] ?? '';

    if($team1 && $team2 && $match_date && $stadium){
        $stmt = $conn->prepare("INSERT INTO fixtures (home_team, away_team, match_date, venue) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $team1, $team2, $match_date, $stadium);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Delete Fixture
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM fixtures WHERE id = $delete_id");
}

// Fetch fixtures
$fixtures_result = $conn->query("SELECT * FROM fixtures ORDER BY match_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Fixtures</title>
<link rel="stylesheet" href="../css/style.css">
<style>
/* Navbar */
header {
    background-color: #1B1464;
    color: #fff;
    padding: 20px 0;
}
header .container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header h1 { font-size: 24px; font-weight: bold; }
header nav a { color: #fff; margin-left: 20px; text-decoration: none; font-weight: bold; }
header nav a:hover { color: #FFB400; }

/* Fixtures Page Styling */
.fixtures-section { text-align: center; padding: 50px 20px; }
.fixtures-section h2 { color: #37003c; margin-bottom: 40px; }

.fixtures-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto; }

.fixture-card { background: #f8f8f8; border-radius: 12px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s ease; position: relative; }
.fixture-card:hover { transform: translateY(-5px); }
.fixture-card h3 { color: #37003c; margin-bottom: 10px; }
.fixture-card p { color: #333; margin: 5px 0; }

.fixture-card .delete-btn { position: absolute; top: 10px; right: 10px; background: #d0008f; color: #fff; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 12px; transition: background 0.3s; }
.fixture-card .delete-btn:hover { background: #37003c; }

.form-container { max-width: 500px; margin: 30px auto; padding: 20px; background: #f2f2f2; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.form-container h3 { margin-bottom: 15px; color: #37003c; }
.form-container label { display: block; text-align: left; margin-bottom: 5px; font-weight: bold; }
.form-container select, .form-container input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
.form-container button { padding: 12px; background-color: #37003c; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
.form-container button:hover { background-color: #d0008f; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Football Management System</h1>
        <nav>
            <a href="../index.php">Home</a>
            <a href="fixtures_manage.php">Manage Fixtures</a>
            <a href="../php/logout.php">Logout</a>
        </nav>
    </div>
</header>

<div class="fixtures-section">
    <h2>Manage Fixtures</h2>

    <!-- Add Fixture Form -->
    <div class="form-container">
        <h3>Add New Fixture</h3>
        <form method="POST">
            <input type="hidden" name="add_fixture" value="1">

            <label for="team1">Team 1</label>
            <select id="team1" name="team1" required>
                <option value="">Select Team 1</option>
                <?php while($team = $teams_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($team['team_name']) ?>"><?= htmlspecialchars($team['team_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="team2">Team 2</label>
            <select id="team2" name="team2" required>
                <option value="">Select Team 2</option>
                <?php $teams_result->data_seek(0); ?>
                <?php while($team = $teams_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($team['team_name']) ?>"><?= htmlspecialchars($team['team_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="match_date">Date</label>
            <input type="date" id="match_date" name="match_date" required>

            <label for="stadium">Stadium</label>
            <select id="stadium" name="stadium" required>
                <option value="">Select Stadium</option>
                <?php while($stadium = $stadiums_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($stadium['stadium_name']) ?>"><?= htmlspecialchars($stadium['stadium_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Add Fixture</button>
        </form>
    </div>

    <!-- Existing Fixtures -->
    <div class="fixtures-container">
        <?php while($fixture = $fixtures_result->fetch_assoc()): ?>
            <div class="fixture-card">
                <h3><?= htmlspecialchars($fixture['home_team']) ?> vs <?= htmlspecialchars($fixture['away_team']) ?></h3>
                <p>Date: <?= htmlspecialchars($fixture['match_date']) ?></p>
                <p>Stadium: <?= htmlspecialchars($fixture['venue']) ?></p>
                <a class="delete-btn" href="?delete_id=<?= $fixture['id'] ?>" onclick="return confirm('Are you sure you want to delete this fixture?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>

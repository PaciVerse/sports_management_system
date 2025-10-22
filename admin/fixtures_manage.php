<?php
session_start();
include('../php/db_connect.php');

// Only admin can access
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.html");
    exit;
}

// Handle addition of a new fixture
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $team1 = $_POST['team1'] ?? '';
    $team2 = $_POST['team2'] ?? '';
    $match_date = $_POST['match_date'] ?? '';
    $stadium = $_POST['stadium'] ?? '';

    if($team1 && $team2 && $match_date && $stadium){
        $stmt = $conn->prepare("INSERT INTO fixtures (home_team, away_team, match_date, venue) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $team1, $team2, $match_date, $stadium);
        $stmt->execute();
        $stmt->close();
        header("Location: fixtures_manage.php");
        exit;
    }
}

// Handle deletion
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM fixtures WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: fixtures_manage.php");
    exit;
}

// Fetch teams for dropdown
$teams_result = $conn->query("SELECT id, team_name FROM teams ORDER BY team_name ASC");

// Fetch stadiums for dropdown
$stadiums_result = $conn->query("SELECT id, stadium_name FROM stadiums ORDER BY stadium_name ASC");

// Fetch all fixtures
$fixtures_result = $conn->query("SELECT * FROM fixtures ORDER BY match_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Fixtures</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.container { width: 90%; max-width: 1200px; margin: 50px auto; }
h2 { color: #1B1464; text-align: center; margin-bottom: 30px; }

/* Table */
table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; }
th { background-color: #1B1464; color: #fff; }
tr:nth-child(even) { background-color: #f2f2f2; }
a.delete-btn { background-color: #d0008f; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; transition: 0.2s; }
a.delete-btn:hover { background-color: #37003c; }

/* Navbar */
header nav a { margin-left: 20px; color: #fff; text-decoration: none; font-weight: bold; }
header nav a:hover { color: #FFB400; }

/* Footer */
footer { text-align: center; margin-top: 50px; padding: 20px 0; background: #1B1464; color: #fff; }

/* Centered Add button */
.add-btn-container { text-align: center; margin-bottom: 20px; }
button.add-btn { background-color: #1B1464; color: #fff; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
button.add-btn:hover { background-color: #37003c; }

/* Modal Styles */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
.modal-content { background-color: #f2f2f2; margin: 10% auto; padding: 30px 25px; border-radius: 15px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
.close { color: #aaa; position: absolute; right: 20px; top: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover { color: #000; }

/* Form inside modal */
.modal form label { display: block; margin-bottom: 8px; font-weight: bold; color: #1B1464; }
.modal form input, .modal form select { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; }
.modal form button { width: 100%; padding: 12px; background-color: #37003c; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
.modal form button:hover { background-color: #d0008f; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Admin - Manage Fixtures</h1>
        <nav>
            <a href="../index.php">Home</a>
            <a href="users_manage.php">Users</a>
            <a href="teams_manage.php">Teams</a>
            <a href="fixtures_manage.php">Fixtures</a>
            <a href="news_manage.php">News</a>
            <a href="stadiums_manage.php">Stadiums</a>
            <a href="../php/logout.php">Logout</a>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <div class="add-btn-container">
            <button class="add-btn" id="openModal">Add New Fixture</button>
        </div>

        <!-- Fixtures Table -->
        <h2>Existing Fixtures</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Home Team</th>
                <th>Away Team</th>
                <th>Date</th>
                <th>Stadium</th>
                <th>Action</th>
            </tr>
            <?php while($fixture = $fixtures_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($fixture['id']) ?></td>
                <td><?= htmlspecialchars($fixture['home_team']) ?></td>
                <td><?= htmlspecialchars($fixture['away_team']) ?></td>
                <td><?= htmlspecialchars($fixture['match_date']) ?></td>
                <td><?= htmlspecialchars($fixture['venue']) ?></td>
                <td>
                    <a href="fixtures_manage.php?delete_id=<?= $fixture['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this fixture?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<!-- Add Fixture Modal -->
<div id="fixtureModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h3>Add Fixture</h3>
        <form method="POST">
            <label for="team1">Home Team</label>
            <select id="team1" name="team1" required>
                <option value="">Select Home Team</option>
                <?php $teams_result->data_seek(0); while($team = $teams_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($team['team_name']) ?>"><?= htmlspecialchars($team['team_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="team2">Away Team</label>
            <select id="team2" name="team2" required>
                <option value="">Select Away Team</option>
                <?php $teams_result->data_seek(0); while($team = $teams_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($team['team_name']) ?>"><?= htmlspecialchars($team['team_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="match_date">Date</label>
            <input type="date" id="match_date" name="match_date" required>

            <label for="stadium">Stadium</label>
            <select id="stadium" name="stadium" required>
                <option value="">Select Stadium</option>
                <?php $stadiums_result->data_seek(0); while($stadium = $stadiums_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($stadium['stadium_name']) ?>"><?= htmlspecialchars($stadium['stadium_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Add Fixture</button>
        </form>
    </div>
</div>

<script>
// Modal logic
var modal = document.getElementById("fixtureModal");
var openBtn = document.getElementById("openModal");
var closeBtn = document.getElementById("closeModal");

openBtn.onclick = function(){ modal.style.display = "block"; }
closeBtn.onclick = function(){ modal.style.display = "none"; }
window.onclick = function(event){ if(event.target == modal) modal.style.display = "none"; }
</script>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

<?php
session_start();
include('../php/db_connect.php');

// Only admin can access
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.html");
    exit;
}

// Handle addition of a new team
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['team_name'])){
    $team_name = trim($_POST['team_name']);
    if($team_name != ''){
        $stmt = $conn->prepare("INSERT INTO teams (team_name) VALUES (?)");
        $stmt->bind_param("s", $team_name);
        $stmt->execute();
        $stmt->close();
        header("Location: teams_manage.php");
        exit;
    }
}

// Handle deletion
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: teams_manage.php");
    exit;
}

// Fetch all teams
$teams_result = $conn->query("SELECT id, team_name FROM teams ORDER BY team_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Teams</title>
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

/* Form */
.form-container { max-width: 400px; margin: 30px auto; padding: 20px; background: #f2f2f2; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.form-container label { display: block; text-align: left; margin-bottom: 5px; font-weight: bold; }
.form-container input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
.form-container button { padding: 12px; background-color: #37003c; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
.form-container button:hover { background-color: #d0008f; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Admin - Manage Teams</h1>
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
        <h2>Add New Team</h2>
        <div class="form-container">
            <form method="POST">
                <label for="team_name">Team Name</label>
                <input type="text" id="team_name" name="team_name" placeholder="Enter team name" required>
                <button type="submit">Add Team</button>
            </form>
        </div>

        <h2>Teams List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Team Name</th>
                <th>Action</th>
            </tr>
            <?php while($team = $teams_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($team['id']) ?></td>
                <td><?= htmlspecialchars($team['team_name']) ?></td>
                <td>
                    <a href="teams_manage.php?delete_id=<?= $team['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this team?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

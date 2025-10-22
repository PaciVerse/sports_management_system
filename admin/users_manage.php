<?php
session_start();
include('../php/db_connect.php');

// Only admin can access
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.html");
    exit;
}

// Handle deletion
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users_manage.php");
    exit;
}

// Handle password reset
if(isset($_POST['reset_id']) && isset($_POST['new_password'])){
    $reset_id = $_POST['reset_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $reset_id);
    $stmt->execute();
    $stmt->close();

    header("Location: users_manage.php");
    exit;
}

// Fetch all users
$users_result = $conn->query("SELECT id, username, role FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.container { width: 90%; max-width: 1200px; margin: 50px auto; }
h2 { color: #1B1464; text-align: center; margin-bottom: 30px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; }
th { background-color: #1B1464; color: #fff; }
tr:nth-child(even) { background-color: #f2f2f2; }
a.delete-btn, button.reset-btn { background-color: #d0008f; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
a.delete-btn:hover, button.reset-btn:hover { background-color: #37003c; }
.reset-form { display: none; margin-top: 5px; }
.reset-form input { padding: 5px; margin-right: 5px; border-radius: 5px; border: 1px solid #ccc; }
</style>
<script>
function toggleResetForm(id) {
    var form = document.getElementById('reset-form-' + id);
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>
</head>
<body>

<header>
    <div class="container">
        <h1>Admin - Manage Users</h1>
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
        <h2>Users List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while($user = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <a href="users_manage.php?delete_id=<?= $user['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <button type="button" class="reset-btn" onclick="toggleResetForm(<?= $user['id'] ?>)">Reset Password</button>
                    <form method="POST" class="reset-form" id="reset-form-<?= $user['id'] ?>">
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <input type="hidden" name="reset_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="reset-btn">Update</button>
                    </form>
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

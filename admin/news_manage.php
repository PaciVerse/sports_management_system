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
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: news_manage.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = $_POST['news_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if($title && $content){
        if($news_id){ // Edit
            $stmt = $conn->prepare("UPDATE news SET title=?, content=? WHERE id=?");
            $stmt->bind_param("ssi", $title, $content, $news_id);
            $stmt->execute();
            $stmt->close();
        } else { // Add
            $stmt = $conn->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $content);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: news_manage.php");
        exit;
    }
}

// Fetch all news
$news_result = $conn->query("SELECT * FROM news ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage News</title>
<link rel="stylesheet" href="../css/style.css">
<style>
/* Container & Table */
.container { width: 90%; max-width: 1200px; margin: 50px auto; }
h2 { color: #1B1464; text-align: center; margin-bottom: 30px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; }
th { background-color: #1B1464; color: #fff; }
tr:nth-child(even) { background-color: #f2f2f2; }

/* Buttons */
a.delete-btn, button.edit-btn { color: #fff; padding: 5px 10px; border-radius: 5px; border: none; cursor: pointer; transition: 0.2s; }
a.delete-btn { background-color: #d0008f; text-decoration: none; }
a.delete-btn:hover { background-color: #37003c; }
button.edit-btn { background-color: #37003c; margin-right:5px; }
button.edit-btn:hover { background-color: #d0008f; }

/* Center Add button */
.add-btn-container { text-align: center; margin-bottom: 20px; }
button.add-btn { background-color: #1B1464; color: #fff; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
button.add-btn:hover { background-color: #37003c; }

/* Modal overlay & content */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
.modal-content { background-color: #f2f2f2; margin: 10% auto; padding: 30px 25px; border-radius: 15px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
.close { color: #aaa; position: absolute; right: 20px; top: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover { color: #000; }

/* Form inside modal */
.modal form label { display: block; margin-bottom: 8px; font-weight: bold; color: #1B1464; }
.modal form input, .modal form textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; }
.modal form textarea { resize: vertical; height: 100px; }
.modal form button { width: 100%; padding: 12px; background-color: #37003c; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
.modal form button:hover { background-color: #d0008f; }
</style>
</head>
<body>

<header>
    <div class="container">
        <h1>Admin - Manage News</h1>
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
        <h2>News List</h2>

        <div class="add-btn-container">
            <button class="add-btn" id="openAddModal">Add New News</button>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Action</th>
            </tr>
            <?php while($news = $news_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($news['id']) ?></td>
                <td><?= htmlspecialchars($news['title']) ?></td>
                <td><?= htmlspecialchars($news['content']) ?></td>
                <td>
                    <button class="edit-btn" onclick="openEditModal(<?= $news['id'] ?>, '<?= htmlspecialchars($news['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($news['content'], ENT_QUOTES) ?>')">Edit</button>
                    <a href="news_manage.php?delete_id=<?= $news['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this news?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <h3>Add News</h3>
        <form method="POST">
            <input type="hidden" name="news_id" value="">
            <label for="title_add">Title</label>
            <input type="text" id="title_add" name="title" required>
            <label for="content_add">Content</label>
            <textarea id="content_add" name="content" required></textarea>
            <button type="submit">Add News</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h3>Edit News</h3>
        <form method="POST">
            <input type="hidden" id="edit_news_id" name="news_id">
            <label for="title_edit">Title</label>
            <input type="text" id="title_edit" name="title" required>
            <label for="content_edit">Content</label>
            <textarea id="content_edit" name="content" required></textarea>
            <button type="submit">Update News</button>
        </form>
    </div>
</div>

<script>
// Add Modal
var addModal = document.getElementById("addModal");
var openAddBtn = document.getElementById("openAddModal");
var closeAdd = document.getElementById("closeAddModal");
openAddBtn.onclick = function(){ addModal.style.display = "flex"; }
closeAdd.onclick = function(){ addModal.style.display = "none"; }

// Edit Modal
var editModal = document.getElementById("editModal");
var closeEdit = document.getElementById("closeEditModal");
function openEditModal(id, title, content){
    document.getElementById("edit_news_id").value = id;
    document.getElementById("title_edit").value = title;
    document.getElementById("content_edit").value = content;
    editModal.style.display = "flex";
}
closeEdit.onclick = function(){ editModal.style.display = "none"; }

// Close modal on outside click
window.onclick = function(event){
    if(event.target == addModal) addModal.style.display = "none";
    if(event.target == editModal) editModal.style.display = "none";
}
</script>

<footer>
    <p>&copy; 2025 Football Management System. All Rights Reserved.</p>
</footer>

</body>
</html>

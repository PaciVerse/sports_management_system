<?php
include 'db_connect.php'; // We'll make this next

$query = "SELECT * FROM fixtures ORDER BY match_date ASC";
$result = $conn->query($query);

$fixtures = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fixtures[] = $row;
    }
}

echo json_encode($fixtures);
$conn->close();
?>

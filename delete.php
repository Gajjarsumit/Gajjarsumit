<?php
include 'db.php';
$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM watches WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$conn->query("CREATE TEMPORARY TABLE temp_watches AS SELECT ROW_NUMBER() OVER (ORDER BY id) AS new_id, name, make, price, image, created_at FROM watches");
$conn->query("DELETE FROM watches");
$conn->query("INSERT INTO watches (id, name, make, price, image, created_at) SELECT new_id, name, make, price, image, created_at FROM temp_watches");
$conn->query("DROP TEMPORARY TABLE temp_watches");

$result = $conn->query("SELECT MAX(id) + 1 AS next_id FROM watches");
$row = $result->fetch_assoc();
$next_id = $row['next_id'] ?? 1;
$conn->query("ALTER TABLE watches AUTO_INCREMENT = $next_id");

header("Location: index.php");
$conn->close();
?>
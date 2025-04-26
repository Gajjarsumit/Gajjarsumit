<?php
include 'db.php';

$name = $_POST['name'];
$make = $_POST['make'];
$price = $_POST['price'];
$imageName = '';

if ($_FILES['image']['name']) {
    $imageName = basename($_FILES['image']['name']);
    $targetDir = "uploads/";
    $targetFile = $targetDir . $imageName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($targetFile);
        if (!in_array($fileType, $allowedTypes)) {
            unlink($targetFile);
            $imageName = '';
            echo "Error: Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $imageName = '';
        echo "Error: Failed to upload image.";
    }
}

$stmt = $conn->prepare("INSERT INTO watches (name, make, price, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $name, $make, $price, $imageName);

if ($stmt->execute()) {
    $conn->query("CREATE TEMPORARY TABLE temp_watches AS SELECT ROW_NUMBER() OVER (ORDER BY id) AS new_id, name, make, price, image, created_at FROM watches");
    $conn->query("DELETE FROM watches");
    $conn->query("INSERT INTO watches (id, name, make, price, image, created_at) SELECT new_id, name, make, price, image, created_at FROM temp_watches");
    $conn->query("DROP TEMPORARY TABLE temp_watches");

    $result = $conn->query("SELECT MAX(id) + 1 AS next_id FROM watches");
    $row = $result->fetch_assoc();
    $next_id = $row['next_id'];
    $conn->query("ALTER TABLE watches AUTO_INCREMENT = $next_id");

    header("Location: index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
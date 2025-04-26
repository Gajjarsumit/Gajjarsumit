<?php
include 'db.php';
$id = $_POST['id'];
$name = $_POST['name'];
$make = $_POST['make'];
$price = $_POST['price'];
$newImageName = $_POST['current_image'] ?? ''; // Default to current image if no new upload

// Handle new image upload if provided
if ($_FILES['image']['name']) {
    $newImageName = basename($_FILES['image']['name']);
    $targetDir = "uploads/";
    $targetFile = $targetDir . $newImageName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($targetFile);
        if (!in_array($fileType, $allowedTypes)) {
            unlink($targetFile);
            $newImageName = '';
            echo "Error: Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $newImageName = '';
        echo "Error: Failed to upload image.";
    }

    // Delete the old image if a new one is uploaded successfully
    $stmt = $conn->prepare("SELECT image FROM watches WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldRow = $result->fetch_assoc();
    $stmt->close();

    if ($oldRow['image'] && file_exists("uploads/{$oldRow['image']}") && $newImageName) {
        unlink("uploads/{$oldRow['image']}");
    }
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("UPDATE watches SET name = ?, make = ?, price = ?, image = ? WHERE id = ?");
$stmt->bind_param("ssdsi", $name, $make, $price, $newImageName, $id);

if ($stmt->execute()) {
    header("Location: index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
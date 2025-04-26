<?php
include 'db.php';
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM watches WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Watch</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h3>Edit Watch</h3>
    <form action="update_save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required><br>
        </div>
        <div>
            <label>Make:</label>
            <input type="text" name="make" value="<?php echo htmlspecialchars($row['make']); ?>" required><br>
        </div>
        <div>
            <label>Price:</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required><br>
        </div>
        <div>
            <label>Current Image:</label>
            <?php if ($row['image'] && file_exists("uploads/{$row['image']}")): ?>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Current Watch Image" style="max-width: 100px; height: auto; margin-top: 5px;"><br>
            <?php else: ?>
                <p>No image available</p><br>
            <?php endif; ?>
            <label>New Image:</label>
            <input type="file" name="image" accept="image/*"><br>
        </div>
        <input type="submit" value="Update">
    </form>
</body>
</html>
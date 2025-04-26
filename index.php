<?php 
session_start(); 
if (!isset($_SESSION['loggedin'])) { 
    header('Location: login.php'); 
    exit(); 
} 
include 'db.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Watch Shop</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the CSS file -->
</head>
<body>
<h2>Online Watch Shop - Product List</h2><p><a href='logout.php'>Logout</a></p>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Make</th>
        <th>Price</th>
        <th>Image</th>
        <th>Added On</th>
        <th>Actions</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM watches");
    while ($row = $result->fetch_assoc()) {
        $img = $row['image'] ? "<img src='uploads/{$row['image']}' alt='Watch' style='max-width: 100px; height: auto;'>" : "No Image";
        if ($row['image'] && !file_exists("uploads/{$row['image']}")) {
            $img = "Image not found";
        }
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['make']}</td>
                <td>{$row['price']}</td>
                <td>$img</td>
                <td>{$row['created_at']}</td>
                <td>
                    <a href='update.php?id={$row['id']}'>Edit</a> |
                    <a href='delete.php?id={$row['id']}'>Delete</a>
                </td>
              </tr>";
    }
    ?>
</table>

<h3>Add New Watch</h3>
<form action="insert.php" method="post" enctype="multipart/form-data">
    Name: <input type="text" name="name" required>
    Make: <input type="text" name="make" required>
    Price: <input type="number" name="price" required>
    Image: <input type="file" name="image" accept="image/*">
    <input type="submit" value="Add">
</form>
</body>
</html>
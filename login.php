<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $id;
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required><br>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required><br>
        </div>
        <input type="submit" value="Login">
    </form>
    <p><a href="register.php">Register</a></p>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
<?php
session_start();
require 'db.php';

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $address = trim($_POST['address']);

    // Basic validation
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    // Check if username already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username is already taken.";
    }
    $stmt->close();

    // If no errors, create user
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password_hash, $address);
        $stmt->execute();
        $stmt->close();

        $_SESSION['username'] = $username;

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <body>

        <h1>Signup</h1>
        <a href="index.php">⬅ Back</a>

        <?php
        if (!empty($errors)) {
            echo "<div style='background:#f8d7da; padding:10px; color:#721c24; border:1px solid #f5c6cb; margin-bottom:10px;'>";
            foreach ($errors as $e) {
                echo "<p>" . htmlspecialchars($e) . "</p>";
            }
            echo "</div>";
        }
        ?>

        <form method="post">
            <label>Username:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <label>Home Address:</label><br>
            <input type="text" name="address" required><br><br>

            <button type="submit">Create Account</button>
        </form>

    </body>
</html>

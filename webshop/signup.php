<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $address = trim($_POST['address']);

    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter (A–Z).";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter (a–z).";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one digit (0–9).";
    }

    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must contain at least one special character (!@#$%^&* etc).";
    }

    // Check password blacklist
    $stmt = $mysqli->prepare("SELECT id FROM password_blacklist WHERE password = ?");
    $stmt->bind_param("s", $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "This password is too common and cannot be used. Choose a stronger password.";
    }

    $stmt->close();


    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username is already taken.";
    }
    $stmt->close();

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
        <p><strong>Password requirements:</strong></p>
        <ul>
            <li>At least 8 characters</li>
            <li>At least one uppercase letter (A–Z)</li>
            <li>At least one lowercase letter (a–z)</li>
            <li>At least one number (0–9)</li>
            <li>At least one special character (!@#$%^&* etc.)</li>
        </ul>


    </body>
</html>

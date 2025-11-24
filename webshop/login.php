<?php
session_start();
require 'db.php';

$errors = [];

// If user is already logged in, go to index
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $mysqli->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $password_hash);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $password_hash)) {
            // Login successful
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;

            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Incorrect password.";
        }
    } else {
        $errors[] = "User not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
    <body>

        <h1>Login</h1>
        <a href="index.php">⬅ Back</a>

        <?php
        if (!empty($errors)) {
            echo "<div style='background:#f8d7da; padding:10px; color:#721c24; 
                        border:1px solid #f5c6cb; margin-bottom:10px;'>";
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

            <button type="submit">Login</button>
        </form>

    </body>
</html>

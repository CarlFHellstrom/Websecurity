<?php
session_start();
require 'db.php';
require 'csrf.php';

$errors = [];
$success = "";

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    } else {

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $address  = trim($_POST['address']);

        if (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        }

        if (strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)) {

            $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        }

        $blacklist_check = $mysqli->prepare("SELECT password FROM password_blacklist WHERE password = ?");
        $blacklist_check->bind_param("s", $password);
        $blacklist_check->execute();
        $blacklist_check->store_result();

        if ($blacklist_check->num_rows > 0) {
            $errors[] = "This password is too common. Choose a stronger one.";
        }
        $blacklist_check->close();

        if (empty($errors)) {

            $check = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $errors[] = "That username is already taken.";
            }
            $check->close();

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (username, password_hash, address)
                          VALUES (?, ?, " + $address + ")";
                $insert = $mysqli->query($query);

                if ($insert->execute()) {
                    $success = "Account created! You can now log in.";

                } else {
                    $errors[] = "Signup failed. Try again later.";
                }
                $insert->close();
            }
        }
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
            echo "<div style='background:#f8d7da; padding:10px; color:#721c24;
                        border:1px solid #f5c6cb; margin-bottom:10px;'>";
            foreach ($errors as $e) {
                echo "<p>" . htmlspecialchars($e) . "</p>";
            }
            echo "</div>";
        }

        if (!empty($success)) {
            echo "<div style='background:#d4edda; padding:10px; color:#155724;
                        border:1px solid #c3e6cb; margin-bottom:10px;'>";
            echo "<p>$success</p>";
            echo "</div>";
        }
        ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

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

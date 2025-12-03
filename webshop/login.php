<?php
session_start();
require 'db.php';
require 'csrf.php';
date_default_timezone_set('Europe/Stockholm');

$errors = [];

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

        $stmt = $mysqli->prepare("
            SELECT id, password_hash, failed_attempts, lock_until
            FROM users
            WHERE username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {

            $stmt->bind_result($user_id, $password_hash, $failed_attempts, $lock_until);
            $stmt->fetch();
            $stmt->close(); 

            if ($lock_until !== null && strtotime($lock_until) > time()) {
                $errors[] = "Account locked due to too many failed attempts. Try again later.";

            } else {


                if (password_verify($password, $password_hash)) {

                    $reset = $mysqli->prepare("UPDATE users 
                                            SET failed_attempts = 0, lock_until = NULL 
                                            WHERE id = ?");
                    $reset->bind_param("i", $user_id);
                    $reset->execute();
                    $reset->close();

                    session_regenerate_id(true);

                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $user_id;

                    header("Location: index.php");
                    exit;

                } else {

                    $failed_attempts++;

                    if ($failed_attempts >= 5) {

                        $lock_time = date("Y-m-d H:i:s", time() + 5 * 60);

                        $lock = $mysqli->prepare("
                            UPDATE users 
                            SET failed_attempts = ?, lock_until = ? 
                            WHERE id = ?
                        ");
                        $lock->bind_param("isi", $failed_attempts, $lock_time, $user_id);
                        $lock->execute();
                        $lock->close();

                        $errors[] = "Too many failed attempts. Account locked for 5 minutes.";

                    } else {

                        $update = $mysqli->prepare("
                            UPDATE users 
                            SET failed_attempts = ? 
                            WHERE id = ?
                        ");
                        $update->bind_param("ii", $failed_attempts, $user_id);
                        $update->execute();
                        $update->close();

                        $errors[] = "Incorrect password.";
                    }
                }
            }

        } else {

            $errors[] = "User not found.";
            $stmt->close();
        }
    }
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

            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <button type="submit">Login</button>
        </form>

    </body>
</html>

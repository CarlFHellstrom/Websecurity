<?php
session_start();
require 'db.php';
require 'csrf.php';

$errors = [];
$success = "";
$username_value = "";
$address_value = "";

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $address  = trim($_POST['address'] ?? '');

        $username_value = $username;
        $address_value = $address;

        if (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        }

        if (
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)
        ) {
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
                          VALUES ('$username', '$hash', '$address')";
                if ($mysqli->multi_query($query)) {
                    // Consume all result sets
                    do {
                        if ($result = $mysqli->store_result()) {
                            $result->free();
                        }
                    } while ($mysqli->next_result());
                    
                    $success = "Account created! You can now log in.";
                    $username_value = "";
                    $address_value = "";
                } else {
                    $errors[] = "Signup failed. Try again later.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-title">Sign up</div>
        <div class="nav">
            <a href="index.php">Back to shop</a>
            <a href="login.php">Login</a>
        </div>
    </header>

    <div class="card">
        <h2>Create an account</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="signup.php">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <label for="username">Username</label>
            <input type="text"
                   id="username"
                   name="username"
                   required
                   value="<?php echo htmlspecialchars($username_value); ?>">

            <label for="password">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   required>

            <label for="address">Home address</label>
            <input type="text"
                   id="address"
                   name="address"
                   required
                   value="<?php echo htmlspecialchars($address_value); ?>">

            <button type="submit">Create account</button>
        </form>
    </div>

</div>
</body>
</html>

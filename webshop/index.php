<?php
session_start();
require 'db.php';
require 'csrf.php';

$result = $mysqli->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Heart Attack Land</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-title">Heart Attack Land</div>
        <div class="nav">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="header-user">
                    Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                </span>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="card">
        <h1>Welcome!</h1>
        <p>Buy your favourite energy drinks securely using blockchain payments.</p>
    </div>

    <h2>Products</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card product">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p class="product-price"><?php echo $row['price']; ?> kr</p>
                </div>
                <div>
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit">Add to cart</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card">
            <p>No products available.</p>
        </div>
    <?php endif; ?>

    <div class="page-footer-links">
        <a href="cart.php" class="button-link">Go to cart</a>
    </div>

</div>
</body>
</html>

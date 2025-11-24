<?php
session_start();
require 'db.php';
?>

<!DOCTYPE html>
<html>
    <body>
        <h1>Webshop</h1>

        <p>PHP is working and sessions are enabled.</p>

        <?php if (isset($_SESSION['username'])): ?>
            <p>You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']);?></strong></p>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> | <a href="signup.php">Signup</a>
        <?php endif; ?>

        <h2>Products</h2>
        <?php
        $result = $mysqli->query("SELECT * FROM products");

        while ($row = $result->fetch_assoc()):
        ?>

            <div style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc;">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong><?php echo $row['price']; ?> kr</strong></p>

                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Add to cart</button>
                </form>
            </div>
        <?php endwhile; ?>

        <a href="cart.php">Go to cart</a>
    </body>
</html>
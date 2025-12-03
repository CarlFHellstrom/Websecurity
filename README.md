# Websecurity

To start PHP: 
    php -S localhost:8000

To access MySQL:
    mysql -u root

To create database in SQL: 
    CREATE DATABASE webshop;
    USE webshop;

    CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL
    );

    INSERT INTO products (name, description, price) VALUES
    ('Red Bull 250ml', 'Classic Red Bull Energy Drink 250ml', 19.90),
    ('Red Bull Sugarfree 250ml', 'Sugarfree variant of Red Bull', 18.90),
    ('Red Bull Summer Edition 250ml', 'Seasonal limited Red Bull flavor', 20.90),

    ('Monster Energy 500ml', 'Monster Original 500ml', 22.90),
    ('Monster Ultra White 500ml', 'Zero sugar, citrus flavor', 21.90),
    ('Monster Mango Loco 500ml', 'Juiced Monster Mango Loco', 22.90),
    ('Monster Pipeline Punch 500ml', 'Fruit punch tropical Monster', 22.90),

    ('Nocco Caribbean 330ml', 'No sugar, pineapple flavor', 24.90),
    ('Nocco Miami 330ml', 'No sugar, strong fruity flavor', 24.90),
    ('Nocco Blood Orange 330ml', 'No sugar, citrus and orange flavor', 24.90),

    ('Celsius Sparkling Wild Berry 355ml', 'Zero sugar sparkling berry drink', 20.90),
    ('Celsius Cola 355ml', 'Cola flavored energy drink', 20.90),
    ('Celsius Peach Vibe 355ml', 'Popular peach flavor', 20.90),

    ('Battery Energy Drink 330ml', 'Classic Finnish energy drink', 15.90),
    ('Battery Juiced Strawberry 330ml', 'Battery with strawberry flavor', 16.90),

    ('Prime Energy Blue Raspberry 330ml', 'Blue raspberry flavor, US import', 34.90),
    ('Prime Energy Ice Pop 330ml', 'Ice pop flavor, US import', 34.90);
 
    CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL
    );

    CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        tx_id VARCHAR(255) DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    CREATE TABLE order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    );

    CREATE TABLE password_blacklist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        password VARCHAR(255) NOT NULL
    );

    INSERT INTO password_blacklist (password) VALUES
    ('password'),
    ('Password'),
    ('123456'),
    ('12345678'),
    ('iloveyou'),
    ('qwerty'),
    ('admin'),
    ('welcome'),
    ('abc123'),
    ('letmein'),
    ('monkey'),
    ('dragon'),
    ('football'),
    ('princess'),
    ('123123');
    ('Password1!'),
    ('Welcome1!'),
    ('Admin123!'),
    ('Qwerty12!'),
    ('Abc12345!'),
    ('Summer23!'),
    ('Winter21!'),
    ('Spring2023!'),
    ('Hello123!'),
    ('Test1234!'),
    ('Letmein1!'),
    ('Welcome@123'),
    ('Admin@2024'),
    ('Password@123'),
    ('October2024!');

    ALTER TABLE users
    ADD failed_attempts INT DEFAULT 0,
    ADD lock_until DATETIME DEFAULT NULL;

# Attacks
## SQL injections
### Description
Changing the SQL from parameter-based and prepared queries to raw queries creates a vulnerability for SQL-injection in the signup page.

We can write as an address:
```sql
Lund'); SET FOREIGN_KEY_CHECKS=0; DROP TABLE users; SET FOREIGN_KEY_CHECKS=1; --
``` 

### Code changes
```php
// In signup.php line 60
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
} else {
    $errors[] = "Signup failed. Try again later.";
}
```
## Cross-site scripting
### Description
Via the sql injection it's possible to add html with javascript that execute on other users browsers. This is possible if the website does not check html special chars.
### Code changes
```php
// In cart.php line 34
<strong><?php echo $row['name']; ?></strong>
```
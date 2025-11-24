# Websecurity

To access MySQL:
    mysql -u root

To create database: 
    CREATE DATABASE webshop;
    USE webshop;

To create table: 
    CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL
);

To insert energy drinks: 
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


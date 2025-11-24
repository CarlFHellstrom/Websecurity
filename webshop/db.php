<?php

$DB_HOST = "localhost";
$DB_USER = "root";   
$DB_PASS = "";        
$DB_NAME = "webshop";

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

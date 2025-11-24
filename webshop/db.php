<?php

$DB_HOST = "localhost";
$DB_USER = "root";    // default homebrew MySQL user
$DB_PASS = "";        // press enter if your root has no password
$DB_NAME = "webshop";

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

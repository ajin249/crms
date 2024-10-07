<?php
// db_connection.php

$servername = "localhost";  // Replace with your MySQL server hostname or IP
$username = "root";         // Replace with your MySQL username
$password = "";             // Replace with your MySQL password
$dbname = "review_management";  // Replace with the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
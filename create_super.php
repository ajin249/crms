<?php
include('db_connection.php');

// Superadmin credentials
$username = 'superadmin';
$password = 'password123';

// Hash the password before inserting it into the database
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert the user with hashed password
$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', 'superadmin')";

if ($conn->query($sql) === TRUE) {
    echo "Superadmin created successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
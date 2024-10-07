<?php
// create_useradmin.php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createUser'])) {

    if ($_SESSION['role'] != 'superadmin') {
        header('Location: index.php');
        exit();
    }

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Password Hashing
    $hotel_restaurant_id = $_POST['hotel_restaurant_id'];

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists
        header('Location: create_account.php?error=' . urlencode('Username already exists.'));
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, hotel_restaurant_id) VALUES (?, ?, 'useradmin', ?)");
    $stmt->bind_param('ssi', $username, $password, $hotel_restaurant_id);

    if ($stmt->execute()) {
        // Success redirect
        header('Location: create_account.php?success=1');
    } else {
        // Error in query execution
        header('Location: create_account.php?error=' . urlencode('Failed to create user admin.'));
    }

    $stmt->close();
} else {
    header('Location: create_account.php?error=' . urlencode('Invalid request.'));
}

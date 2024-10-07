<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createHotel'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $status = 1;

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO hotels_restaurants (name, address, created_by, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $name, $address, $_SESSION['user_id'], $status);

    if ($stmt->execute()) {
        // Redirect to create_account.php after successful creation
        header('Location: create_account.php?success=1');
        exit();
    } else {
        // Error in query execution
        header('Location: create_account.php?error=' . urlencode('Failed to create Hotel/Restaurant.'));
    }

    $stmt->close();
} else {
    header('Location: create_account.php?error=' . urlencode('Invalid request.'));
}
?>
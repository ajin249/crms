<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the user exists in the database
    $stmt = $conn->prepare("SELECT id, username, password, role, hotel_restaurant_id FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify password using password_verify
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['hotel_restaurant_id'] = $user['hotel_restaurant_id'];

            // Redirect based on the user role
            if ($user['role'] == 'superadmin') {
                header('Location: dashboard.php');
            } elseif ($user['role'] == 'useradmin') {
                header('Location: useradmin.php');
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid username!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Add gradient background */
            background: linear-gradient(to right, #6a11cb, #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 400px;
            /* Limit the width of the container */
            margin: auto;
        }

        .card {
            border-radius: 15px;
            /* Round the card edges */
        }

        .card-header {
            text-align: center;
            /* Center the title */
        }

        h2 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-white text-center mb-4">Review Management System</h2> <!-- Title at the top -->
        <div class="card">
            <div class="card-header">
                <h3 class="">Login</h3>
            </div>
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php } ?>
            <div class="card-body">
                <form action="index.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button> <!-- Full width button -->
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<?php
// create_account.php
session_start();
include('db_connection.php'); // Database connection file
if ($_SESSION['role'] != 'superadmin') {
    header('Location: index.php');
    exit();
}

// Fetch hotels/restaurants
$result = $conn->query("SELECT * FROM hotels_restaurants");
include('header.php');
?>

<!-- Display Success or Error Message -->
<div class="container mt-4">
    <?php if (isset($_GET['success'])) { ?>
        <div class="alert alert-success" role="alert">
            Entry added successfully!
        </div>
    <?php } elseif (isset($_GET['error'])) { ?>
        <div class="alert alert-danger" role="alert">
            Error: <?= htmlspecialchars($_GET['error']); ?>
        </div>
    <?php } ?>
</div>

<div class="card mt-4">
    <div class="card-header">
        <b>Create Hotel/Restaurant</b>
    </div>
    <div class="card-body">
        <form action="create_hotel.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="createHotel">Create</button>
        </form>
    </div>
</div>
<hr />
<div class="card">
    <div class="card-header">
        <b>Create User Account</b>
    </div>
    <div class="card-body">
        <form action="create_useradmin.php" method="post">
            <div class="mb-3">
                <label for="hotel" class="form-label">Assign Hotel/Restaurant</label>
                <select class="form-select" id="hotel" name="hotel_restaurant_id" required>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" name="createUser">Create User Admin</button>
        </form>
    </div>
</div>
<?php include('footer.php'); ?>
<?php
session_start();
include('db_connection.php');

// Ensure that only the superadmin can access this page
if ($_SESSION['role'] !== 'superadmin') {
    header('Location: index.php');
    exit();
}

// Handle password reset request
if (isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password']; // Get the new password from the form
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param('si', $hashed_password, $user_id);

    if ($stmt->execute()) {
        $success_message = "Password reset successfully for user";
    } else {
        $error_message = "Failed to reset password.";
    }
}

// Handle suspend/unsuspend/terminate actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $hotel_id = $_GET['id'];
    if ($_GET['action'] == 'suspend') {
        $stmt = $conn->prepare("UPDATE hotels_restaurants SET status = 2 WHERE id = ?");
        $stmt->bind_param('i', $hotel_id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'unsuspend') {
        $stmt = $conn->prepare("UPDATE hotels_restaurants SET status = 1 WHERE id = ?");
        $stmt->bind_param('i', $hotel_id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'terminate') {
        $stmt = $conn->prepare("UPDATE hotels_restaurants SET status = 0 WHERE id = ?");
        $stmt->bind_param('i', $hotel_id);
        $stmt->execute();
    }
}

// Fetch all hotels/restaurants with their admin details
$sql = "SELECT h.id, h.name, h.address, h.status, h.phone, h.email, h.logo, h.cover_image, h.website_link, h.created_by, h.created_at, h.updated_at, u.username, u.id AS user_id 
        FROM hotels_restaurants h 
        JOIN users u ON h.id = u.hotel_restaurant_id";
$result = $conn->query($sql);

include('header.php');
?>

<h2>Hotels and Restaurants</h2>

<?php if (isset($success_message)) { ?>
    <div class="alert alert-success">
        <?php echo $success_message; ?>
    </div>
<?php } ?>

<?php if (isset($error_message)) { ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
<?php } ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Hotel/Restaurant Name</th>
            <th>Address</th>
            <th>Admin Username</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0) { ?>
            <?php $i = 1;
            while ($row = $result->fetch_assoc()) { ?>
                <tr class="<?php echo $row['status'] == 0 ? 'table-secondary' : ''; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['status'] == 1 ? 'Active' : ($row['status'] == 2 ? 'Suspended' : 'Terminated'); ?></td>
                    <td>
                        <!-- Info Modal Trigger -->
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#infoModal<?php echo $row['id']; ?>">Info</button>

                        <?php if ($row['status'] != 0) { ?>

                            <!-- Edit Button -->
                            <!-- <a href="create_update_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a> -->

                            <!-- Reset Password Modal Trigger -->
                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#resetPasswordModal<?php echo $row['user_id']; ?>">Reset Password</button>

                            <!-- Suspend/Unsuspend Button with Confirmation -->
                            <?php if ($row['status'] == 1) { ?>
                                <a href="?action=suspend&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"
                                    onclick="return confirm('Are you sure you want to suspend this hotel/restaurant?');">Suspend</a>
                            <?php } elseif ($row['status'] == 2) { ?>
                                <a href="?action=unsuspend&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm"
                                    onclick="return confirm('Are you sure you want to unsuspend this hotel/restaurant?');">Unsuspend</a>
                            <?php } ?>

                            <!-- Terminate Button with Confirmation -->
                            <a href="?action=terminate&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to terminate this hotel/restaurant?');">Terminate</a>
                        <?php } ?>
                    </td>
                </tr>

                <!-- Info Modal -->
                <div class="modal fade" id="infoModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="infoModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="infoModalLabel">Hotel/Restaurant Info</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                                <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                                <p><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
                                <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                                <p><strong>Logo:</strong> <img src="<?php echo $row['logo']; ?>" alt="Logo"
                                        style="width: 100px;"></p>
                                <p><strong>Cover Image:</strong> <img src="<?php echo $row['cover_image']; ?>" alt="Cover"
                                        style="width: 100px;"></p>
                                <p><strong>Website Link:</strong> <a href="<?php echo $row['website_link']; ?>"
                                        target="_blank"><?php echo $row['website_link']; ?></a></p>
                                <p><strong>Status:</strong>
                                    <?php echo $row['status'] == 1 ? 'Active' : ($row['status'] == 2 ? 'Suspended' : 'Terminated'); ?>
                                </p>
                                <p><strong>Account Created:</strong> <?php echo Date('d-m-Y', strtotime($row['created_at'])); ?></p>
                                <p><strong>Account Updated:</strong> <?php echo Date('d-m-Y', strtotime($row['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Password Modal -->
                <div class="modal fade" id="resetPasswordModal<?php echo $row['user_id']; ?>" tabindex="-1"
                    aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password for
                                    <?php echo $row['username']; ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST"
                                    onsubmit="return confirm('Are you sure you want to reset the password for this user?');">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password"
                                            required>
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $i++;
            } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6">No hotels or restaurants found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php include('footer.php'); ?>
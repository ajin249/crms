<?php
// useradmin.php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'useradmin') {
    header('Location: index.php');
    exit();
}

$hotel_restaurant_id = $_SESSION['hotel_restaurant_id'];

// Fetch hotel/restaurant details to check status and other details
$hotel_query = $conn->prepare("SELECT status, name, address, phone, email, logo, cover_image, website_link FROM hotels_restaurants WHERE id = ?");
$hotel_query->bind_param('i', $hotel_restaurant_id);
$hotel_query->execute();
$hotel_result = $hotel_query->get_result();
$hotel = $hotel_result->fetch_assoc();

$hotel_status = $hotel['status']; // 1 = Active, 2 = Suspended, 0 = Terminated

// Handle updating hotel/restaurant information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hotel'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website_link = $_POST['website_link'];

    // Handle logo upload
    if ($_FILES['logo']['name']) {
        $logo = 'uploads/' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
    } else {
        $logo = $hotel['logo'];
    }

    // Handle cover image upload
    if ($_FILES['cover_image']['name']) {
        $cover_image = 'uploads/' . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
    } else {
        $cover_image = $hotel['cover_image'];
    }

    // Update the hotel/restaurant information
    $update_stmt = $conn->prepare("UPDATE hotels_restaurants SET name = ?, address = ?, phone = ?, email = ?, logo = ?, cover_image = ?, website_link = ? WHERE id = ?");
    $update_stmt->bind_param('sssssssi', $name, $address, $phone, $email, $logo, $cover_image, $website_link, $hotel_restaurant_id);

    if ($update_stmt->execute()) {
        $success_message = "Hotel/Restaurant information updated successfully.";
    } else {
        $error_message = "Failed to update information.";
    }
}


include('header.php');
?>

<!-- Hotel/Restaurant Information Modification Form -->
<div class="card mt-5">
    <div class="card-header">
        Modify Profile Information
    </div>
    <div class="card-body">
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php } ?>

        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php } ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="<?= htmlspecialchars($hotel['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address"
                    value="<?= htmlspecialchars($hotel['address']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone"
                    value="<?= htmlspecialchars($hotel['phone']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?= htmlspecialchars($hotel['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                <?php if ($hotel['logo']) { ?>
                    <img src="<?= htmlspecialchars($hotel['logo']) ?>" alt="Logo" class="img-thumbnail mt-2" width="100">
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image</label>
                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                <?php if ($hotel['cover_image']) { ?>
                    <img src="<?= htmlspecialchars($hotel['cover_image']) ?>" alt="Cover Image" class="img-thumbnail mt-2"
                        width="200">
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="website_link" class="form-label">Website Link</label>
                <input type="url" class="form-control" id="website_link" name="website_link"
                    value="<?= htmlspecialchars($hotel['website_link']) ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="update_hotel">Update Information</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>
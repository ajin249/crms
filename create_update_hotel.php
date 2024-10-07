<?php
session_start();
include('db_connection.php');

// Ensure user is logged in and is a useradmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'useradmin') {
    header('Location: index.php');
    exit();
}

// Handle form submission for creating/updating hotel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['updateHotel']) {
    // Collect form data
    $hotel_id = $_POST['hotel_id']; // If updating, hotel ID will be passed
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website_link = $_POST['website_link'];

    // Handle file uploads
    $logo = $_FILES['logo']['name'] ? 'uploads/logos/' . basename($_FILES['logo']['name']) : null;
    $cover_image = $_FILES['cover_image']['name'] ? 'uploads/cover_images/' . basename($_FILES['cover_image']['name']) : null;

    // Handle file upload
    if ($logo) {
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
    }
    if ($cover_image) {
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
    }

    // If hotel ID is set, we are updating
    if ($hotel_id) {
        $sql = "UPDATE hotels_restaurants SET name=?, address=?, phone=?, email=?, logo=?, cover_image=?, website_link=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssi', $name, $address, $phone, $email, $logo, $cover_image, $website_link, $hotel_id);
    } else {
        // Otherwise, we are creating a new hotel
        $sql = "INSERT INTO hotels_restaurants (name, address, phone, email, logo, cover_image, website_link) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssss', $name, $address, $phone, $email, $logo, $cover_image, $website_link);
    }

    // Execute the query
    if ($stmt->execute()) {
        $success_message = $hotel_id ? "Hotel updated successfully!" : "Hotel created successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

// Fetch hotel data for updating
$hotel_data = null;
if (isset($_GET['id'])) {
    $hotel_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM hotels_restaurants WHERE id = ?");
    $stmt->bind_param('i', $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hotel_data = $result->fetch_assoc();
}

include('header.php');
?>

<h2><?php echo $hotel_data ? 'Update Hotel' : 'Create Hotel'; ?></h2>

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

<form action="create_update_hotel.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="hotel_id" value="<?php echo $hotel_data ? $hotel_data['id'] : ''; ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Hotel/Restaurant Name</label>
        <input type="text" class="form-control" id="name" name="name" required
            value="<?php echo $hotel_data ? $hotel_data['name'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" name="address" required
            value="<?php echo $hotel_data ? $hotel_data['address'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone"
            value="<?php echo $hotel_data ? $hotel_data['phone'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email"
            value="<?php echo $hotel_data ? $hotel_data['email'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="website_link" class="form-label">Website Link</label>
        <input type="url" class="form-control" id="website_link" name="website_link"
            value="<?php echo $hotel_data ? $hotel_data['website_link'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="logo" class="form-label">Logo</label>
        <input type="file" class="form-control" id="logo" name="logo">
    </div>
    <div class="mb-3">
        <label for="cover_image" class="form-label">Cover Image</label>
        <input type="file" class="form-control" id="cover_image" name="cover_image">
    </div>
    <button type="submit" class="btn btn-primary"
        name="updateHotel"><?php echo $hotel_data ? 'Update Hotel' : 'Create Hotel'; ?></button>
</form>
<?php include('footer.php'); ?>
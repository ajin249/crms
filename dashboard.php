<?php
session_start();
include('db_connection.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch hotels from the database
$sql = "SELECT * FROM hotels_restaurants where status=1";
$result = $conn->query($sql);

include('header.php');
?>

<!-- Main Content -->
<h2>List of Hotels</h2>
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
            <!-- <th>Actions</th> -->
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0) { ?>

            <?php $i = 1;
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['phone'] ? $row['phone'] : ''; ?></td>
                    <td><?php echo $row['email'] ? $row['email'] : ''; ?></td>
                    <!-- <td>
                        <a href="create_update_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="suspend_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Suspend</a>
                        <a href="delete_hotel.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Terminate</a>
                    </td> -->
                </tr>
                <?php $i++;
            } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7">No hotels found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>


<?php include('footer.php'); ?>
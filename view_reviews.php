<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'useradmin') {
    header('Location: index.php');
    exit();
}

$hotel_restaurant_id = $_SESSION['hotel_restaurant_id'];

// Fetch all customer details for the hotel/restaurant
$sql = "SELECT c.id AS customer_id, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone 
        FROM customers c
        WHERE c.hotel_restaurant_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $hotel_restaurant_id);
$stmt->execute();
$customers = $stmt->get_result();

include('header.php');
?>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<h4>Customer Reviews</h4>

<?php if (isset($success_message)) { ?>
    <div class="alert alert-success"><?= $success_message ?></div>
<?php } ?>

<?php if (isset($error_message)) { ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
<?php } ?>

<!-- List of customer details -->
<div class="card mt-5">
    <div class="card-header bg-secondary">
        <div class="card-title text-white">
            Customer Details
        </div>
    </div>
</div>
<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers->num_rows > 0) { ?>
                <?php $i = 1;
                while ($customer = $customers->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                        <td><?= htmlspecialchars($customer['customer_email']) ?></td>
                        <td><?= htmlspecialchars($customer['customer_phone']) ?></td>
                        <td>
                            <!-- View Reviews Button to open modal -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#reviewModal<?= $customer['customer_id'] ?>">
                                View Reviews
                            </button>
                        </td>
                    </tr>

                    <!-- Modal for showing reviews -->
                    <div class="modal fade" id="reviewModal<?= $customer['customer_id'] ?>" tabindex="-1"
                        aria-labelledby="reviewModalLabel<?= $customer['customer_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reviewModalLabel<?= $customer['customer_id'] ?>">Customer
                                        Reviews for <?= htmlspecialchars($customer['customer_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Fetch and display reviews for this customer -->
                                    <?php
                                    // SQL to fetch reviews for this customer
                                    $review_sql = "SELECT rq.question, rq.type, cr.review, cr.rating 
                                                   FROM customer_reviews cr
                                                   JOIN review_questions rq ON cr.review_question_id = rq.id
                                                   WHERE cr.customer_id = ?";
                                    $review_stmt = $conn->prepare($review_sql);
                                    $review_stmt->bind_param('i', $customer['customer_id']);
                                    $review_stmt->execute();
                                    $customer_reviews = $review_stmt->get_result();
                                    ?>

                                    <?php if ($customer_reviews->num_rows > 0) { ?>
                                        <!-- Display reviews without table -->
                                        <div class="list-group">
                                            <?php
                                            while ($review = $customer_reviews->fetch_assoc()) { ?>
                                                <div class="list-group-item">
                                                    <h6 class="mb-1 text-secondary"><?= htmlspecialchars($review['question']) ?></h6>
                                                    <p class="mb-1"><?= htmlspecialchars($review['review']) ?></p>
                                                    <?php if ($review['type'] == 'rating') { ?>
                                                        <div>
                                                            <?php
                                                            $rating = $review['rating'];
                                                            // Display stars based on rating
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                if ($i <= $rating) {
                                                                    echo '<i class="fas fa-star text-warning"></i>'; // Filled star
                                                                } else {
                                                                    echo '<i class="far fa-star text-secondary"></i>'; // Empty star
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <p>No reviews found for this customer.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5">No customers found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
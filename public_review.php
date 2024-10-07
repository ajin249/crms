<?php
session_start();
include('db_connection.php');

if (isset($_GET['hotel_id'])) {
    $hotel_id = $_GET['hotel_id'];

    // Fetch hotel information
    $hotel_query = $conn->prepare("SELECT * FROM hotels_restaurants WHERE id = ? AND status = 1");
    $hotel_query->bind_param("i", $hotel_id);
    $hotel_query->execute();
    $hotel_result = $hotel_query->get_result();
    $hotel = $hotel_result->fetch_assoc(); // Fetch hotel information

    // Fetch review questions for the specific hotel
    $questions_query = $conn->prepare("SELECT * FROM review_questions WHERE hotel_restaurant_id = ? AND status = 1");
    $questions_query->bind_param("i", $hotel_id);
    $questions_query->execute();
    $questions_result = $questions_query->get_result(); // Fetch result set
} else {
    echo "Hotel ID is required.";
    exit();
}
$error = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];

    // Insert customer information
    $insert_customer = $conn->prepare("INSERT INTO customers (hotel_restaurant_id, name, email, phone) VALUES (?, ?, ?, ?)");
    $insert_customer->bind_param("isss", $hotel_id, $customer_name, $customer_email, $customer_phone);
    if ($insert_customer->execute()) {
        $customer_id = $insert_customer->insert_id;
    } else {
        // echo "Error inserting customer: " . $insert_customer->error;
        $error = 1;
    }

    // Insert reviews
    while ($question = $questions_result->fetch_assoc()) {
        $review_answer = isset($_POST['question_' . $question['id']]) ? $_POST['question_' . $question['id']] : null;
        $rating = isset($_POST['rating_' . $question['id']]) ? $_POST['rating_' . $question['id']] : null;

        // echo "Customer ID: " . $customer_id . "<br>";
        // echo "Hotel ID: " . $hotel_id . "<br>";
        // echo "Question ID: " . $question['id'] . "<br>";
        // echo "Review Answer: " . $review_answer . "<br>";
        // echo "Rating: " . $rating . "<br>";

        $insert_review = $conn->prepare("INSERT INTO customer_reviews (customer_id, hotel_restaurant_id, review_question_id, review, rating) VALUES (?, ?, ?, ?, ?)");
        $insert_review->bind_param("iiisi", $customer_id, $hotel_id, $question['id'], $review_answer, $rating);
        if ($insert_review->execute()) {
            $error = 2;
            // echo "Review inserted successfully.<br>";
        } else {
            $error = 1;
            // echo "Error inserting review: " . $insert_review->error . "<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for star icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            <?php if (!empty($hotel['cover_image'])): ?>
                background-image: url('<?= htmlspecialchars($hotel['cover_image']) ?>');
            <?php else: ?>
                background: linear-gradient(to right, #6a11cb, #2575fc);
            <?php endif; ?>
            background-size: cover;
            background-position: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            margin-bottom: 0px;
        }

        .hotel-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-align: left;
        }

        .hotel-logo {
            max-width: 150px;
            margin-right: 20px;
        }

        .hotel-details {
            flex-grow: 1;
        }

        .centered-info {
            text-align: center;
        }

        .rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            display: inline-block;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 1rem;
            color: #aaa;
            cursor: pointer;
        }

        .rating label:hover,
        .rating label:hover~label,
        .rating input:checked~label {
            color: #ffc107;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="hotel-info">
            <?php if ($hotel && !empty($hotel['logo'])) { ?>
                <img src="<?= htmlspecialchars($hotel['logo']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>"
                    class="hotel-logo">
                <div class="hotel-details">
                    <h5><?= htmlspecialchars(strtoupper($hotel['name'])) ?></h5>
                    <p><strong>Address:</strong> <?= htmlspecialchars($hotel['address']) ?> <br />
                        <strong>Phone:</strong> <?= htmlspecialchars($hotel['phone']) ?><br />
                        <strong>Email:</strong> <?= htmlspecialchars($hotel['email']) ?><br />
                        <strong>Website:</strong> <a href="<?= htmlspecialchars($hotel['website_link']) ?>" target="_blank">
                            <?= htmlspecialchars(preg_replace('#^https?://#', '', $hotel['website_link'])) ?></a>
                    </p>
                </div>
            <?php } elseif ($hotel) { ?>
                <div class="centered-info">
                    <h5><?= htmlspecialchars(strtoupper($hotel['name'])) ?></h5>
                    <p><strong>Address:</strong> <?= htmlspecialchars($hotel['address']) ?> <br />
                        <strong>Phone:</strong> <?= htmlspecialchars($hotel['phone']) ?><br />
                        <strong>Email:</strong> <?= htmlspecialchars($hotel['email']) ?><br />
                        <strong>Website:</strong> <a href="<?= htmlspecialchars($hotel['website_link']) ?>" target="_blank">
                            <?= htmlspecialchars(preg_replace('#^https?://#', '', $hotel['website_link'])) ?></a>
                    </p>
                </div>
            <?php } else { ?>
                <p>Hotel information not available.</p>
            <?php } ?>
        </div>
        <hr />
        <?php if ($error == 0) { ?>
            <h2>Leave Your Review</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="customer_name">Your Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label for="customer_email">Your Phone</label>
                    <input type="phone" class="form-control" id="customer_phone" name="customer_phone">
                </div>
                <div class="form-group">
                    <label for="customer_email">Your Email</label>
                    <input type="email" class="form-control" id="customer_email" name="customer_email">
                </div>
                <hr />
                <?php while ($question = $questions_result->fetch_assoc()) { ?>
                    <div class="form-group">
                        <label><?= htmlspecialchars($question['question']) ?></label>

                        <?php if ($question['type'] === 'text') { ?>
                            <textarea class="form-control" name="question_<?= $question['id'] ?>" rows="3" required></textarea>
                        <?php } elseif ($question['type'] === 'rating') { ?>
                            <div class="rating">
                                <input type="radio" id="star5_<?= $question['id'] ?>" name="rating_<?= $question['id'] ?>" value="5"
                                    required>
                                <label for="star5_<?= $question['id'] ?>"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star4_<?= $question['id'] ?>" name="rating_<?= $question['id'] ?>"
                                    value="4">
                                <label for="star4_<?= $question['id'] ?>"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star3_<?= $question['id'] ?>" name="rating_<?= $question['id'] ?>"
                                    value="3">
                                <label for="star3_<?= $question['id'] ?>"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star2_<?= $question['id'] ?>" name="rating_<?= $question['id'] ?>"
                                    value="2">
                                <label for="star2_<?= $question['id'] ?>"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star1_<?= $question['id'] ?>" name="rating_<?= $question['id'] ?>"
                                    value="1">
                                <label for="star1_<?= $question['id'] ?>"><i class="fas fa-star"></i></label>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <button type="submit" class="btn btn-primary btn-block">Submit Review</button>
            </form>
        <?php } else if ($error == 2) {
            echo "<div class='alert alert-success ' role='alert'>Thank you for your review!</div>";
        } else {
            echo "<div class='alert alert-danger ' role='alert'>Error posting review!</div>";
        } ?>
    </div>

    <!-- Bootstrap JS and dependencies (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
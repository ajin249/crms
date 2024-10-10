<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'useradmin') {
    header('Location: index.php');
    exit();
}

$hotel_restaurant_id = $_SESSION['hotel_restaurant_id'];

// Fetch hotel/restaurant details to check status
$hotel_query = $conn->prepare("SELECT status,name FROM hotels_restaurants WHERE id = ?");
$hotel_query->bind_param('i', $hotel_restaurant_id);
$hotel_query->execute();
$hotel_result = $hotel_query->get_result();
$hotel = $hotel_result->fetch_assoc();

$hotel_status = $hotel['status']; // 1 = Active, 2 = Suspended, 0 = Terminated
$hotel_name = $hotel['name'];

// Fetch review questions and reviews if the hotel is not suspended
if ($hotel_status == 1) {
    $questions = $conn->query("SELECT * FROM review_questions WHERE hotel_restaurant_id = '$hotel_restaurant_id'");
    $reviews = $conn->query("SELECT * FROM reviews WHERE hotel_restaurant_id = '$hotel_restaurant_id'");
}

include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_link'])) {
    $customer_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $public_review_link = "http://localhost/crms/public_review.php?hotel_id=" . $hotel_restaurant_id;

    // Send email
    $subject = "Public Review Link";
    $message = "Dear Customer,\n\nPlease find your public review link below:\n" . $public_review_link . "\n\nThank you!";
    $headers = "From: no-reply@yourdomain.com";

    if (mail($customer_email, $subject, $message, $headers)) {
        $success_message = "Link sent successfully to $customer_email!";
    } else {
        $error_message = "Failed to send link. Please try again.";
    }
}

$url_path = $_SERVER['REQUEST_URI'];

// Remove any query parameters if present
$url_path_without_query = strtok($url_path, '?');

// Remove the filename from the end of the URL path
$base_url = preg_replace('/\/[^\/]+\.[^\/]+$/', '', $url_path_without_query);

$publicLink = "https://".$_SERVER['HTTP_HOST'].$base_url."/public_review.php?hotel_id=".$hotel_restaurant_id;
?>



<?php if ($hotel_status == 2) { ?>
    <!-- Account suspended modal -->
    <div class="modal show bg-secondary" id="accountSuspendedModal" tabindex="-1" aria-labelledby="accountSuspendedLabel"
        aria-hidden="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <p class="modal-title text-white" id="accountSuspendedLabel">Account Suspended </p>
                </div>
                <div class="modal-body text-danger">
                    Your account has been suspended. Please contact support for further assistance.
                </div>
                <div class="modal-footer">
                    <form action="logout.php" method="POST">
                        <button type="submit" class="btn btn-primary btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } elseif ($hotel_status == 1) { ?>
    <!-- Display customer reviews -->
    <h3 class="mt-3"><?php echo $hotel_name; ?></h3>

    <!-- Public Review Link with Copy Button -->
    <div class="mb-4">
        <label for="publicReviewLink" class="form-label text-white">Public Review Link</label>
        <div class="input-group">
            <input type="text" id="publicReviewLink" class="form-control"
                value="<?= $publicLink ?>" disabled>
            <button class="btn btn-secondary col-md-2" type="button" id="copyLinkBtn" onclick="copyText()">Copy <i
                    class="fa fa-copy"></i></button>
        </div>
    </div>

    <!-- Email/WhatsApp Link Section -->
    <div class="card bg-secondary">
        <div class="card-header">
            <div class="card-title text-white">Send Public Review Link</div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-white mb-3">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="icon-container p-3 me-3">
                                    <i class="fa fa-envelope-o fa-5x text-primary"></i>
                                </div>
                                <div>
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Customer Email</label>
                                            <input type="email" name="email" id="email" class="form-control" required>
                                        </div>
                                        <button type="submit" name="send_link" class="btn btn-primary">Send Link via
                                            Email</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-white mb-3">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="icon-container p-3 me-3">
                                    <i class="fa fa-whatsapp fa-5x text-success"></i>
                                </div>
                                <div>
                                    <div class="mb-3">
                                        <label for="whatsapp" class="form-label">Customer WhatsApp Number (include country
                                            code)</label>
                                        <input type="tel" name="whatsapp" id="whatsapp" class="form-control"
                                            placeholder="+91xxxxxxxxxx" required>
                                    </div>
                                    <button type="button" class="btn btn-success" id="whatsappBtn" onclick="sendToWhatsapp()">Send Link via
                                        WhatsApp</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Cards for average rating per question -->
    <h4 class="mt-5">Average Rating</h4>
    <div class="row">
        <?php
        // Fetch each question and its average rating
        $question_query = $conn->prepare("SELECT id, question FROM review_questions WHERE hotel_restaurant_id = ? and type = 'rating'");
        $question_query->bind_param('i', $hotel_restaurant_id);
        $question_query->execute();
        $questions_result = $question_query->get_result();

        while ($question = $questions_result->fetch_assoc()) {
            $question_id = $question['id'];
            $question_text = $question['question'];

            // Fetch the average rating for this question
            $avg_query = $conn->prepare("SELECT AVG(rating) as average_rating FROM customer_reviews WHERE review_question_id = ? AND hotel_restaurant_id = ?");
            $avg_query->bind_param('ii', $question_id, $hotel_restaurant_id);
            $avg_query->execute();
            $avg_result = $avg_query->get_result();
            $average_rating = $avg_result->fetch_assoc()['average_rating'];

            // Set default if no ratings are available
            $average_rating = $average_rating ? round($average_rating, 2) : 'No Ratings';
            ?>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="icon-container p-3 me-3 bg-white rounded-circle">
                                <i class="fa fa-star text-info fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="card-title"><?= htmlspecialchars($question_text) ?></h5>
                                <p class="card-text">Average Rating: <?= htmlspecialchars($average_rating) ?>/5</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>


<?php } else { ?>
    <div class="alert alert-danger mt-5" role="alert">
        This account has been terminated.
    </div>
    <form action="logout.php" method="POST">
        <button type="submit" class="btn btn-primary">Logout</button>
    </form>
<?php } ?>

<script>

    // Function to send WhatsApp link
     function sendToWhatsapp() {
        const phoneNumber = document.getElementById('whatsapp').value.trim();

        if (!phoneNumber.match(/^\+\d{10,15}$/)) {
            alert("Please enter a valid WhatsApp number with country code (e.g., +919876543210).");
            return;
        }

        const formattedPhoneNumber = phoneNumber.replace(/\D/g, ''); // Remove non-numeric characters
        const hotelId = <?= $hotel_restaurant_id ?>;
        const hotelName = "<?= strtoupper($hotel_name) ?>";  // Properly formatted
        // const publicReviewLink = "http://localhost/crms/public_review.php?hotel_id=" + hotelId;
        const whatsappMessage = encodeURIComponent("Hi this is from "+ hotelName +". Please leave us a review: <?= $publicLink ?>" );
        const whatsappUrl = "https://wa.me/" + formattedPhoneNumber + "?text=" + whatsappMessage;

        // Open WhatsApp link in a new tab
        window.open(whatsappUrl, '_blank');
    };

    // Function to copy public review link
    function copyText() {
        const publicReviewLinkInput = document.getElementById('publicReviewLink');

        // Ensure the text is selected for copying
        publicReviewLinkInput.select();
        publicReviewLinkInput.setSelectionRange(0, 99999);  // For mobile devices

        // Try to copy the link using the Clipboard API
        navigator.clipboard.writeText(publicReviewLinkInput.value).then(function () {
            alert('Public review link copied to clipboard!');
        }).catch(function (err) {
            alert('Failed to copy the link. Please try again.');
            console.error('Error copying to clipboard: ', err);
        });
    };

</script>

<?php include('footer.php'); ?>

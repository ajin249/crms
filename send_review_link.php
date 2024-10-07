<?php
// send_review_link.php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['customer_email'];
    $whatsapp = $_POST['customer_whatsapp'];

    // Example review link (you should replace with your actual link)
    $review_link = "http://localhost/crms/public_review.php?hotel_id=" . $_SESSION['hotel_restaurant_id'];

    // Send email
    if (!empty($email)) {
        // Using mail() function (make sure your server is configured to send emails)
        $subject = "Your Review Link";
        $message = "Dear Customer,\n\nWe value your feedback! Please take a moment to review us at the following link:\n$review_link\n\nThank you!";
        mail($email, $subject, $message);
    }

    // Send WhatsApp message (you may need a service or API to send messages)
    if (!empty($whatsapp)) {
        $whatsapp_message = "Dear Customer, we value your feedback! Please review us at: $review_link";
        // Placeholder: Use an API like Twilio or WhatsApp Business API to send this message.
        // Example: sendWhatsAppMessage($whatsapp, $whatsapp_message);
    }

    // Redirect back to useradmin.php
    header('Location: useradmin.php?message=Review link sent successfully.');
    exit();
}
?>
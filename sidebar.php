<?php
// User role
$user_role = $_SESSION['role'];
?>

<!-- Sidebar Toggle Button for mobile -->
<button class="btn btn-dark d-md-none sidebar-toggle" id="sidebarToggle">
    ☰ Menu
</button>

<div class="sidebar" id="sidebar">
    <h4 class="text-white text-center">Admin Dashboard</h4>
    <hr class="bg-white">
    <?php if ($user_role === 'superadmin') { ?>
        <a href="dashboard.php" class="nav-link">Home</a>
        <a href="list_hotels.php" class="nav-link">List Hotels</a>
        <a href="create_account.php" class="nav-link">Create Account</a>
    <?php } elseif ($user_role === 'useradmin') { ?>
        <a href="useradmin.php" class="nav-link">Home</a>
        <a href="review_questions.php" class="nav-link">Review Questions</a>
        <a href="view_reviews.php" class="nav-link">View Reviews</a>
        <a href="update_profile.php" class="nav-link">Profile</a>
    <?php } ?>
    <a href="logout.php" class="nav-link">Logout</a>
</div>

<div class="main-content" id="main-content">
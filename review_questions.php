<?php
// review_questions.php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'useradmin') {
    header('Location: index.php');
    exit();
}

$hotel_restaurant_id = $_SESSION['hotel_restaurant_id'];

// Handle creating a new review question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_question'])) {
    $question = $_POST['question'];
    $type = $_POST['type'];
    $created_by = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Insert new question into the database with status 1 (Active)
    $stmt = $conn->prepare("INSERT INTO review_questions (hotel_restaurant_id, question, type, status, created_by) VALUES (?, ?, ?, 1, ?)");
    $stmt->bind_param('issi', $hotel_restaurant_id, $question, $type, $created_by);
    if ($stmt->execute()) {
        $success_message = "Review question created successfully.";
    } else {
        $error_message = "Failed to create review question.";
    }
}

// Handle modifying an existing review question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify_question'])) {
    $question_id = $_POST['question_id'];
    $new_question = $_POST['new_question'];
    $new_type = $_POST['new_type'];

    // Update the existing question
    $stmt = $conn->prepare("UPDATE review_questions SET question = ?, type = ? WHERE id = ?");
    $stmt->bind_param('ssi', $new_question, $new_type, $question_id);
    if ($stmt->execute()) {
        $success_message = "Review question modified successfully.";
    } else {
        $error_message = "Failed to modify review question.";
    }
}

// Handle delete, disable, or enable actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $question_id = $_GET['id'];
    if ($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("UPDATE review_questions SET status = 0 WHERE id = ?");
        $stmt->bind_param('i', $question_id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'disable') {
        $stmt = $conn->prepare("UPDATE review_questions SET status = 2 WHERE id = ?");
        $stmt->bind_param('i', $question_id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'enable') {
        $stmt = $conn->prepare("UPDATE review_questions SET status = 1 WHERE id = ?");
        $stmt->bind_param('i', $question_id);
        $stmt->execute();
    }
}

// Fetch all review questions for the hotel/restaurant
$sql = "SELECT * FROM review_questions WHERE hotel_restaurant_id = '$hotel_restaurant_id' and status != 0";
$questions = $conn->query($sql);

include('header.php');
?>

<h3>Manage Review Questions</h3>

<?php if (isset($success_message)) { ?>
    <div class="alert alert-success"><?= $success_message ?></div>
<?php } ?>

<?php if (isset($error_message)) { ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
<?php } ?>

<!-- Form to create a new review question -->
<div class="card mt-4">
    <div class="card-header">
        <div class="card-title">Create New Review Question</div>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" class="form-control" id="question" name="question" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Question Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="rating">5-Star Rating</option>
                    <option value="text">Text Message</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="create_question">Create Question</button>
        </form>
    </div>
</div>
<hr />


<!-- List of existing review questions -->
<div class="card mt-5">
    <div class="card-header bg-secondary">
        <div class="card-title text-white">
            Review Questions
        </div>
    </div>
</div>
<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($questions->num_rows > 0) { ?>
                <?php $i = 1;
                while ($row = $questions->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['question']) ?></td>
                        <td><?= htmlspecialchars($row['type'] == 'rating' ? '5-Star Rating' : 'Text Message') ?></td>
                        <td><?= $row['status'] == 1 ? 'Active' : ($row['status'] == 2 ? 'Disabled' : 'Deleted') ?></td>
                        <td>
                            <!-- Modify Question Modal Trigger -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modifyModal<?= $row['id'] ?>">Modify</button>

                            <!-- Disable/Enable Button -->
                            <?php if ($row['status'] == 1) { ?>
                                <a href="?action=disable&id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Disable</a>
                            <?php } elseif ($row['status'] == 2) { ?>
                                <a href="?action=enable&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Enable</a>
                            <?php } ?>

                            <!-- Delete Button -->
                            <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
                        </td>
                    </tr>

                    <!-- Modify Question Modal -->
                    <div class="modal fade" id="modifyModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modifyModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modifyModalLabel">Modify Review Question</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="new_question" class="form-label">Question</label>
                                            <input type="text" class="form-control" id="new_question" name="new_question"
                                                value="<?= htmlspecialchars($row['question']) ?>" required>
                                            <input type="hidden" name="question_id" value="<?= $row['id'] ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_type" class="form-label">Question Type</label>
                                            <select class="form-select" id="new_type" name="new_type" required>
                                                <option value="rating" <?= $row['type'] == 'rating' ? 'selected' : '' ?>>5-Star
                                                    Rating</option>
                                                <option value="text" <?= $row['type'] == 'text' ? 'selected' : '' ?>>Text
                                                    Message
                                                </option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="modify_question">Modify
                                            Question</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5">No review questions found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
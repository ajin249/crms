<?php
session_start();
include('db_connection.php');

// Ensure user is logged in and is a useradmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'useradmin') {
    header('Location: index.php');
    exit();
}

// Fetch all questions from the database
$sql = "SELECT * FROM questions"; // Assuming you have a questions table
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>List of Questions</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['question']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2">No questions found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php $conn->close(); ?>
<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO category_list (category_name, description) VALUES (:category_name, :description)");
    $stmt->bindParam(':category_name', $category_name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    $success_message = "Category added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../../views/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Add Category</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit">Add Category</button>
        </form>
    </div>
</body>
</html>
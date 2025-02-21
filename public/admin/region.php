<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $region_name = $_POST['region_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO region_list (region_name, description) VALUES (:region_name, :description)");
    $stmt->bindParam(':region_name', $region_name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    $success_message = "Region added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Regions</title>
    <link rel="stylesheet" href="../../views/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Add Regions</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="region_name">Region Name:</label>
                <input type="text" id="region_name" name="region_name" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit">Add Region</button>
        </form>
    </div>
</body>
</html>
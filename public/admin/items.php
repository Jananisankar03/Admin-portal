<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];
    $category = $_POST['category'];
    $item_name = $_POST['item_name'];
    $points = $_POST['points'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO item_list (code, category, name, points, description) VALUES (:code, :category, :item_name, :points, :description)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':points', $points);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    $success_message = "Item added successfully.";
}

$categoriesStmt = $conn->prepare("SELECT category_name FROM category_list");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="../../views/styles.css">
</head>
<body>
    <div class="form-container-1">
        <h2>Add Item</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <div class="form-scrollable">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="code">Code:</label>
                    <input type="text" id="code" name="code" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['category_name']); ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="item_name">Item Name:</label>
                    <input type="text" id="item_name" name="item_name" required>
                </div>
                <div class="form-group">
                    <label for="points">Points:</label>
                    <input type="number" id="points" name="points" value="0" required>
                </div>
                <div class="form-group">
                    <label for="description">Comments:</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                <button type="submit">Add Item</button>
            </form>
        </div>
    </div>
</body>
</html>
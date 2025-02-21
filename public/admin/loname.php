<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $region_name = $_POST['region_name'];
    $loname = $_POST['loname'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO lo_list (region_name, loname, description) VALUES (:region_name, :loname, :description)");
    $stmt->bindParam(':region_name', $region_name);
    $stmt->bindParam(':loname', $loname);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    $success_message = "LO added successfully.";
}

$stmt = $conn->prepare("SELECT region_name FROM region_list");
$stmt->execute();
$region_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add LO</title>
    <link rel="stylesheet" href="../../views/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Add LO</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <div class="form-scrollable">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="region_name">Region Name:</label>
                    <select id="region_name" name="region_name" required>
                        <?php foreach ($region_list as $region): ?>
                            <option value="<?php echo htmlspecialchars($region['region_name']); ?>">
                                <?php echo htmlspecialchars($region['region_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="loname">LO Name:</label>
                    <input type="text" id="loname" name="loname" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                <button type="submit">Add LO</button>
            </form>
        </div>
    </div>
</body>
</html>

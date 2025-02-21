<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['table']) || empty($_GET['table'])) {
    die("Invalid Request");
}

$id = $_GET['id'];
$table = $_GET['table'];
$columnsStmt = $conn->prepare("DESCRIBE $table");
$columnsStmt->execute();
$columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);

$regions = [];
$lonames = [];
$categories = [];
$itemsData = [];

$regionsStmt = $conn->prepare("SELECT region_name FROM region_list");
$regionsStmt->execute();
$regions = $regionsStmt->fetchAll(PDO::FETCH_ASSOC);

$lonameStmt = $conn->prepare("SELECT loname FROM lo_list");
$lonameStmt->execute();
$lonames = $lonameStmt->fetchAll(PDO::FETCH_ASSOC);

$categoriesStmt = $conn->prepare("SELECT category_name FROM category_list");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$codesStmt = $conn->prepare("SELECT code, name, points FROM item_list");
$codesStmt->execute();
$itemsData = $codesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updateFields = [];
    $updateValues = ['id' => $id];

    foreach ($columns as $column) {
        if ($column != 'id' && isset($_POST[$column])) {
            $updateFields[] = "$column = :$column";
            $updateValues[$column] = $_POST[$column];
        }
    }

    $updateStmt = $conn->prepare("UPDATE $table SET " . implode(', ', $updateFields) . " WHERE id = :id");
    $updateStmt->execute($updateValues);

    header("Location: view.php?table=$table");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM $table WHERE id = :id");
$stmt->execute(['id' => $id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Record Not Found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link rel="stylesheet" href="../../views/styles.css">
    <style>
        .edit-container {
            width: 60%;
            margin: 20px auto;
            margin-left: 30%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .edit-container h2 {
            margin-bottom: 20px;
            color: #23374D;
        }
        .edit-container label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .edit-container input, .edit-container textarea, .edit-container select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .edit-container button {
            margin-top: 15px;
            padding: 10px 20px;
            background: #23374D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-container button:hover {
            background: #1A2B3D;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="edit-container">
        <h2>Edit Record</h2>
        <form method="POST">
            <?php foreach ($columns as $column): ?>
                <?php if ($column != 'id'): ?>
                    <label><?php echo ucfirst(str_replace('_', ' ', $column)); ?>:</label>
                    <?php if ($column == 'region_name'): ?>
                        <select name="<?php echo $column; ?>" required>
                            <option value="">Select Region</option>
                            <?php foreach ($regions as $region): ?>
                                <option value="<?= htmlspecialchars($region['region_name']); ?>" <?= ($record[$column] == $region['region_name']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($region['region_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($column == 'lo_name'): ?>
                        <select name="<?php echo $column; ?>" required>
                            <option value="">Select LO Name</option>
                            <?php foreach ($lonames as $loName): ?>
                                <option value="<?= htmlspecialchars($loName['loname']); ?>" <?= ($record[$column] == $loName['loname']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($loName['loname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($column == 'category'): ?>
                        <select name="<?php echo $column; ?>" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['category_name']); ?>" <?= ($record[$column] == $category['category_name']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($column == 'code'): ?>
                        <select name="<?php echo $column; ?>" required onchange="updateItemDetails(this)">
                            <option value="">Select Code</option>
                            <?php foreach ($itemsData as $item): ?>
                                <option value="<?= htmlspecialchars($item['code']); ?>" 
                                    data-item="<?= htmlspecialchars($item['name']); ?>" 
                                    data-points="<?= htmlspecialchars($item['points']); ?>" 
                                    <?= ($record[$column] == $item['code']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['code']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($column == 'description'): ?>
                        <textarea name="<?php echo $column; ?>" required><?php echo htmlspecialchars($record[$column]); ?></textarea>
                    <?php else: ?>
                        <input type="text" name="<?php echo $column; ?>" value="<?php echo htmlspecialchars($record[$column]); ?>" required>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit">Update</button>
        </form>
    </div>

    <script>
        function updateItemDetails(select) {
            var selectedOption = select.options[select.selectedIndex];
            document.querySelector('input[name="item"]').value = selectedOption.getAttribute('data-item') || '';
            document.querySelector('input[name="points"]').value = selectedOption.getAttribute('data-points') || '';
        }
    </script>
</body>
</html>

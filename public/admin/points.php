<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

$regions = [];
$lonames = [];
$categories = [];
$codes = [];
$items = [];
$selectedRegion = '';
$selectedLoName = '';
$selectedCategory = '';
$selectedCode = '';
$selectedItem = '';
$selectedPoints = 0;

$regionsStmt = $conn->prepare("SELECT region_name FROM region_list");
$regionsStmt->execute();
$regions = $regionsStmt->fetchAll(PDO::FETCH_ASSOC);

$lonameStmt = $conn->prepare("SELECT region_name, loname FROM lo_list");
$lonameStmt->execute();
$lonames = $lonameStmt->fetchAll(PDO::FETCH_ASSOC);

$categoriesStmt = $conn->prepare("SELECT category_name FROM category_list");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$codesStmt = $conn->prepare("SELECT code, name, points, category FROM item_list");
$codesStmt->execute();
$itemsData = $codesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedRegion = $_POST['region_name'] ?? '';
    $selectedLoName = $_POST['lo_name'] ?? '';
    $selectedCategory = $_POST['category'] ?? '';
    $selectedCode = $_POST['code'] ?? '';
    $date = $_POST['date'] ?? '';

    foreach ($itemsData as $item) {
        if ($item['code'] === $selectedCode) {
            $selectedItem = $item['name'];
            $selectedPoints = $item['points'];
            break;
        }
    }

    if (!empty($selectedRegion) && !empty($selectedLoName) && !empty($selectedCategory) && !empty($selectedCode) && !empty($_POST['description']) && !empty($date)) {
        $description = $_POST['description'];

        $stmt = $conn->prepare("INSERT INTO points_list (region_name, lo_name, category, code, item, description, points, date) 
                                VALUES (:region_name, :lo_name, :category, :code, :item, :description, :points, :date)");
        $stmt->bindParam(':region_name', $selectedRegion);
        $stmt->bindParam(':lo_name', $selectedLoName);
        $stmt->bindParam(':category', $selectedCategory);
        $stmt->bindParam(':code', $selectedCode);
        $stmt->bindParam(':item', $selectedItem);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':points', $selectedPoints);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $success_message = "Points added successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Points</title>
    <link rel="stylesheet" href="../../views/styles.css">
</head>
<body>
    <div class="form-container-2">
        <h2>Add Points</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <div class="form-scrollable">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="region_name">Region Name:</label>
                    <select id="region_name" name="region_name" required onchange="filterLoNames()">
                        <option value="">Select Region</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= htmlspecialchars($region['region_name']); ?>" <?= ($selectedRegion == $region['region_name']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($region['region_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lo_name">LO Name:</label>
                    <select id="lo_name" name="lo_name" required>
                        <option value="">Select LO Name</option>
                        <?php foreach ($lonames as $loName): ?>
                            <option value="<?= htmlspecialchars($loName['loname']); ?>" 
                                    data-region="<?= htmlspecialchars($loName['region_name']); ?>" 
                                    <?= ($selectedLoName == $loName['loname']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($loName['loname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required onchange="filterCodes()">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_name']); ?>" <?= ($selectedCategory == $category['category_name']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="code">Code:</label>
                    <select id="code" name="code" required onchange="updateItemDetails(this)">
                        <option value="">Select Code</option>
                        <?php foreach ($itemsData as $item): ?>
                            <option value="<?= htmlspecialchars($item['code']); ?>" 
                                data-item="<?= htmlspecialchars($item['name']); ?>" 
                                data-points="<?= htmlspecialchars($item['points']); ?>" 
                                data-category="<?= htmlspecialchars($item['category']); ?>"
                                <?= ($selectedCode == $item['code']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($item['code']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="item">Item Name:</label>
                    <input type="text" id="item" name="item" value="<?= htmlspecialchars($selectedItem); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="points">Points:</label>
                    <input type="number" id="points" name="points" value="<?= htmlspecialchars($selectedPoints); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="description">Comments:</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>

                <button type="submit">Add Points</button>
            </form>
        </div>
    </div>

    <script>
        function updateItemDetails(select) {
            var selectedOption = select.options[select.selectedIndex];
            document.getElementById('item').value = selectedOption.getAttribute('data-item') || '';
            document.getElementById('points').value = selectedOption.getAttribute('data-points') || '';
        }

        function filterLoNames() {
            var selectedRegion = document.getElementById('region_name').value;
            var loNameSelect = document.getElementById('lo_name');
            var options = loNameSelect.options;

            for (var i = 0; i < options.length; i++) {
                var option = options[i];

                if (option.getAttribute('data-region') === selectedRegion || option.value === "") {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }

            loNameSelect.selectedIndex = 0;
        }

        function filterCodes() {
            var selectedCategory = document.getElementById('category').value;
            var codeSelect = document.getElementById('code');
            var options = codeSelect.options;

            for (var i = 0; i < options.length; i++) {
                var option = options[i];

                if (option.getAttribute('data-category') === selectedCategory || option.value === "") {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }

            codeSelect.selectedIndex = 0;
        }

        document.addEventListener('DOMContentLoaded', function() {
            filterLoNames();
            filterCodes();
        });
    </script>
</body>
</html>
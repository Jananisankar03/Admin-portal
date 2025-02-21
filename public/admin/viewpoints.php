<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

$pointsList = [];
$regions = [];
$loNames = [];
$columns = ['Region Name', 'LO Name', 'Category', 'Code', 'Item', 'Points', 'Date'];
$title = "Points Report";

if (!$conn) {
    die("Database connection failed!");
}

$regionsStmt = $conn->prepare("SELECT DISTINCT region_name FROM region_list");
$regionsStmt->execute();
$regions = $regionsStmt->fetchAll(PDO::FETCH_ASSOC);

$loNamesStmt = $conn->prepare("SELECT region_name, loname FROM lo_list");
$loNamesStmt->execute();
$loNames = $loNamesStmt->fetchAll(PDO::FETCH_ASSOC);

$filterRegionName = $_POST['filter_region_name'] ?? '';
$filterLoName = $_POST['filter_lo_name'] ?? '';

$query = "SELECT region_name, lo_name, category, code, item, points, date FROM points_list";
$conditions = [];
$params = [];

if (!empty($filterRegionName)) {
    $conditions[] = "region_name = :region_name";
    $params[':region_name'] = $filterRegionName;
}

if (!empty($filterLoName)) {
    $conditions[] = "lo_name = :lo_name";
    $params[':lo_name'] = $filterLoName;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$pointsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPoints = array_sum(array_column($pointsList, 'points'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../../views/styles.css">
    <style>
        .report-container {
            width: 95%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .filters {
            display: flex;
            align-items: center;
            gap: 20px; 
            margin-bottom: 20px;
        }
        .filters select, .filters button {
            padding: 10px;
            font-size: 14px;
            width: 30%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .report-table th {
            background-color: #23374D;
            color: #fff;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f1f1f1;
        }
        .download-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #23374D;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h2><?php echo $title; ?></h2>

        <form method="POST" action="">
            <div class="filters">
                <select id="filter_region_name" name="filter_region_name" onchange="filterLoNames()">
                    <option value="">Select Region Name</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?php echo htmlspecialchars($region['region_name']); ?>" <?php echo ($filterRegionName == $region['region_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($region['region_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="filter_lo_name" name="filter_lo_name">
                    <option value="">Select LO Name</option>
                    <?php foreach ($loNames as $loName): ?>
                        <option value="<?php echo htmlspecialchars($loName['loname']); ?>" data-region="<?php echo htmlspecialchars($loName['region_name']); ?>" <?php echo ($filterLoName == $loName['loname']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($loName['loname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
            </div>
        </form>

        <?php if (!empty($pointsList)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pointsList as $item): ?>
                        <tr>
                            <?php foreach ($columns as $key => $col): ?>
                                <td><?php echo htmlspecialchars($item[array_keys($item)[$key]] ?? ''); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="6">Total Points</td>
                        <td colspan="2"><?php echo number_format($totalPoints); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data found</p>
        <?php endif; ?>

        <a href="download.php?region=<?php echo urlencode($filterRegionName); ?>&lo=<?php echo urlencode($filterLoName); ?>" class="download-btn">Download Report</a>
    </div>

    <script>
        function filterLoNames() {
            var selectedRegion = document.getElementById('filter_region_name').value;
            var loNameSelect = document.getElementById('filter_lo_name');
            var options = loNameSelect.options;

            for (var i = 0; i < options.length; i++) {
                var option = options[i];
                if (option.getAttribute('data-region') === selectedRegion || option.value === "") {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }

            var selectedLoName = loNameSelect.querySelector('option[selected]');
            if (selectedLoName && selectedLoName.getAttribute('data-region') !== selectedRegion) {
                loNameSelect.selectedIndex = 0;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            filterLoNames();
        });
    </script>
</body>
</html>

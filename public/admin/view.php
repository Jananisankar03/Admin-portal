<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

$lo_region_list = [];
$columns = [];
$title = "Report";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['filter_type'])) {
    $filterType = $_POST['filter_type'];

    switch ($filterType) {
        case 'category':
            $query = "SELECT id, category_name, description FROM category_list";
            $columns = ['Category Name', 'Description'];
            $title = "Category Report";
            $table = 'category_list';
            break;
        case 'item':
            $query = "SELECT id, code, category, name, points FROM item_list";
            $columns = ['Code','Category', 'Item Name', 'Points'];
            $title = "Item Report";
            $table = 'item_list';
            break;
        case 'region':
            $query = "SELECT id, region_name, description FROM region_list";
            $columns = ['Region Name', 'Description'];
            $title = "Region Report";
            $table = 'region_list';
            break;
        case 'loname':
            $query = "SELECT id, region_name, loname FROM lo_list";
            $columns = ['Region', 'LO Name'];
            $title = "LO Name Report";
            $table = 'lo_list';
            break;
        default:
            $query = "";
    }

    if ($query) {
        $stmt = $conn->query($query);
        $lo_region_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
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
            margin: 10px auto;
            margin-left: 10px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            overflow-y: auto;
        }
        .filters {
            display: flex;
            align-items: center;
            gap: 20px; 
            margin-bottom: 20px;
        }
        .filters select {
            padding: 10px;
            font-size: 14px;
            width: 70%;
        }
        .filters button {
            padding: 10px;
            font-size: 14px;
            width: 30%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-table tbody tr {
            display: table-row !important;
            height: 50px; 
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            vertical-align: middle;
        }
        .report-table th {
            background-color: #23374D;
            color: #fff;
            font-weight: bold;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-table tr:hover {
            background-color: #f1f1f1;
        }
        .actions {
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: center;
            align-items: center;
            white-space: nowrap; 
        }
        .edit-btn, .delete-btn {
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            display: inline-block;
            width: 50px;
            text-align: center; 
        }
        .edit-btn {
            background-color: #23374D;
            border-radius: 5px;
        }
        .delete-btn {
            background-color: #23374D;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h2><?php echo $title; ?></h2>

        <form method="POST" action="">
            <div class="filters">
                <select name="filter_type">
                    <option value="">Select Filter</option>
                    <option value="category" <?php echo isset($_POST['filter_type']) && $_POST['filter_type'] == 'category' ? 'selected' : ''; ?>>Category</option>
                    <option value="item" <?php echo isset($_POST['filter_type']) && $_POST['filter_type'] == 'item' ? 'selected' : ''; ?>>Item</option>
                    <option value="region" <?php echo isset($_POST['filter_type']) && $_POST['filter_type'] == 'region' ? 'selected' : ''; ?>>Region</option>
                    <option value="loname" <?php echo isset($_POST['filter_type']) && $_POST['filter_type'] == 'loname' ? 'selected' : ''; ?>>LO Name</option>
                </select>
                <button type="submit">Filter</button>
            </div>
        </form>

        <?php if (!empty($lo_region_list)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <?php foreach ($columns as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serialNo = 1; ?>
                    <?php foreach ($lo_region_list as $item): ?>
                        <tr>
                            <td><?php echo $serialNo++; ?></td>
                            <?php foreach ($columns as $key => $col): ?>
                                <td><?php echo htmlspecialchars($item[array_keys($item)[$key + 1]]); ?></td>
                            <?php endforeach; ?>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $item['id']; ?>&table=<?php echo $table; ?>" class="edit-btn">Edit</a>
                                <a href="delete.php?id=<?php echo $item['id']; ?>&table=<?php echo $table; ?>" class="delete-btn" onclick="return confirm('Do you want to delete this record?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data found</p>
        <?php endif; ?>
    </div>
</body>
</html>

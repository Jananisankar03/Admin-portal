<?php
session_start();
require '../../config/db.php';
require 'navbar.php';
include '../../views/header.php';

$stmt = $conn->prepare("SELECT lo_list.loname, region_list.region_name, SUM(points_list.points) AS total_points
                        FROM lo_list 
                        JOIN region_list ON lo_list.region_name = region_list.region_name
                        LEFT JOIN points_list ON lo_list.loname = points_list.lo_name
                        GROUP BY lo_list.loname, region_list.region_name
                        ORDER BY total_points DESC, lo_list.loname ASC");
$stmt->execute();
$lo_region_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filterRegionName = $_GET['region'] ?? '';
$filterLoName = $_GET['lo'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
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
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-table tr:hover {
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
        <h2>Report</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Region Name</th>
                    <th>LO Name</th>
                    <th>Total Points</th>
                </tr>
            </thead>
            <tbody>
                <?php $serialNo = 1; ?>
                <?php foreach ($lo_region_list as $item): ?>
                    <tr>
                        <td><?php echo $serialNo++; ?></td>
                        <td><?php echo htmlspecialchars($item['region_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['loname']); ?></td>
                        <td><?php echo htmlspecialchars($item['total_points'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="downloadreport.php?region=<?php echo urlencode($filterRegionName); ?>&lo=<?php echo urlencode($filterLoName); ?>" class="download-btn">
            Download Report
        </a>
    </div>
</body>
</html>
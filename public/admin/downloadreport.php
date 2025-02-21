<?php
session_start();
require '../../config/db.php';

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$filterRegionName = $_GET['region'] ?? '';
$filterLoName = $_GET['lo'] ?? '';

$query = "SELECT lo_list.loname, region_list.region_name, SUM(points_list.points) AS total_points
          FROM lo_list 
          JOIN region_list ON lo_list.region_name = region_list.region_name
          LEFT JOIN points_list ON lo_list.loname = points_list.lo_name";

$conditions = [];
$params = [];

if (!empty($filterRegionName)) {
    $conditions[] = "region_list.region_name = :region_name";
    $params[':region_name'] = $filterRegionName;
}

if (!empty($filterLoName)) {
    $conditions[] = "lo_list.loname = :lo_name";
    $params[':lo_name'] = $filterLoName;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY lo_list.loname, region_list.region_name
            ORDER BY total_points DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$lo_region_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Region Name');
$sheet->setCellValue('B1', 'LO Name');
$sheet->setCellValue('C1', 'Total Points');

$row = 2;
foreach ($lo_region_list as $item) {
    $sheet->setCellValue("A$row", $item['region_name']);
    $sheet->setCellValue("B$row", $item['loname']);
    $sheet->setCellValue("C$row", $item['total_points'] ?? 0);
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

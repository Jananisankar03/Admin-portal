<?php
require '../../config/db.php';

if (!$conn) {
    die("Database connection failed!");
}

$filterRegionName = $_GET['region'] ?? '';
$filterLoName = $_GET['lo'] ?? '';

$query = "SELECT region_name, lo_name, SUM(points) AS total_points FROM points_list";
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

$query .= " GROUP BY region_name, lo_name";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$pointsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pointsList)) {
    die("No data found for the selected filters.");
}

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Region Name');
$sheet->setCellValue('B1', 'LO Name');
$sheet->setCellValue('C1', 'Total Points');

$row = 2;
foreach ($pointsList as $item) {
    $sheet->setCellValue('A' . $row, $item['region_name']);
    $sheet->setCellValue('B' . $row, $item['lo_name']);
    $sheet->setCellValue('C' . $row, $item['total_points']);
    $row++;
}

$filename = "Points_Report.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

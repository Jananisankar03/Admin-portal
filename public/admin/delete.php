<?php
require '../../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['table']) || empty($_GET['table'])) {
    die("Invalid Request: Missing id or table parameter");
}

$id = $_GET['id'];
$table = $_GET['table'];

echo "ID: $id<br>";
echo "Table: $table<br>";

$validTables = ['category_list', 'item_list', 'region_list', 'lo_list'];

if (!in_array($table, $validTables)) {
    die("Invalid Table: $table is not a valid table");
}

try {
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        header("Location: view.php?table=$table");
        exit();
    } else {
        die("Record Not Found or Could Not Be Deleted");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
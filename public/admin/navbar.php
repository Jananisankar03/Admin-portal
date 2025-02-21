<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../views/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <h2> Admin Panel</h2>
    <ul>
        <li><a href="category.php"><i class="fas fa-folder-plus"></i> Add Category</a></li>
        <li><a href="items.php"><i class="fas fa-box"></i> Add Item</a></li>
        <li><a href="region.php"><i class="fas fa-map-marker-alt"></i> Add Regions</a></li>
        <li><a href="loname.php"><i class="fas fa-user"></i> Add LO Name</a></li>
        <li><a href="points.php"><i class="fa-solid fa-ranking-star"></i> Add Points</a></li>
        <li><a href="view.php"><i class="fa-solid fa-filter"></i> Filter</a></li>
        <li><a href="viewpoints.php"><i class="fas fa-eye"></i> View Points</a></li>
        <li><a href="report.php"><i class="fas fa-file-alt"></i> Report</a></li>
        <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>
</body>
</html>

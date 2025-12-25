<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Supervised Driving Experience Management System">
    <meta name="author" content="Aliyeva Asmar">
    <title><?php echo isset($page_title) ? $page_title : 'Driving Experience'; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>🚗 Driving Experience Tracker</h1>
            <p class="tagline">Track your supervised driving journey</p>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <ul class="nav-menu">
                <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="add_experience.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_experience.php') ? 'class="active"' : ''; ?>>Add Experience</a></li>
                <li><a href="summary.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'summary.php') ? 'class="active"' : ''; ?>>View Summary</a></li>
                <li><a href="statistics.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'statistics.php') ? 'class="active"' : ''; ?>>Statistics</a></li>
            </ul>
        </div>
    </nav>
    
    <main class="container">

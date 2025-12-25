<?php
session_start();
require_once 'config/database.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['errors'] = ["Invalid experience ID."];
    header('Location: summary.php');
    exit();
}

$experience_id = intval($_GET['id']);

try {
    // USING PREPARED STATEMENT (PDO)
    $sql = "DELETE FROM drivingExperience WHERE id_drivingExperience = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$experience_id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Experience deleted successfully!";
    } else {
        $_SESSION['errors'] = ["Experience not found."];
    }
    
} catch (PDOException $e) {
    error_log("Delete Error: " . $e->getMessage());
    $_SESSION['errors'] = ["Error deleting experience. Please try again."];
}

header('Location: summary.php');
exit();
?>

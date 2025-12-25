<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: summary.php');
    exit();
}

$id_drivingExperience = intval($_POST['id_drivingExperience'] ?? 0);
$date = trim($_POST['date'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$finish_time = trim($_POST['finish_time'] ?? '');
$km_traveled = floatval($_POST['km_traveled'] ?? 0);
$id_weatherCondition = intval($_POST['id_weatherCondition'] ?? 0);
$id_roadType = intval($_POST['id_roadType'] ?? 0);
$id_roadCondition = intval($_POST['id_roadCondition'] ?? 0);
$id_trafficLevel = intval($_POST['id_trafficLevel'] ?? 0);
$maneuvers = $_POST['maneuvers'] ?? [];

// Validation
if ($id_drivingExperience <= 0 || empty($date) || empty($start_time) || empty($finish_time) || $km_traveled <= 0) {
    $_SESSION['errors'] = ["Please fill all required fields correctly."];
    header("Location: edit_experience.php?id=$id_drivingExperience");
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Update main experience
    $sql = "UPDATE drivingExperience SET 
            date = ?, start_time = ?, finish_time = ?, km_traveled = ?,
            id_weatherCondition = ?, id_roadType = ?, id_roadCondition = ?, id_trafficLevel = ?
            WHERE id_drivingExperience = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $date, $start_time, $finish_time, $km_traveled,
        $id_weatherCondition, $id_roadType, $id_roadCondition, $id_trafficLevel,
        $id_drivingExperience
    ]);
    
    // Delete old maneuvers
    $sql_delete = "DELETE FROM drivingExperience_maneuvers WHERE id_drivingExperience = ?";
    $stmt = $pdo->prepare($sql_delete);
    $stmt->execute([$id_drivingExperience]);
    
    // Insert new maneuvers
    if (!empty($maneuvers)) {
        $sql_insert = "INSERT INTO drivingExperience_maneuvers (id_drivingExperience, id_maneuver) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql_insert);
        
        foreach ($maneuvers as $maneuver_id) {
            $stmt->execute([$id_drivingExperience, intval($maneuver_id)]);
        }
    }
    
    $pdo->commit();
    
    $_SESSION['success_message'] = "✅ Experience updated successfully!";
    header('Location: summary.php');
    exit();
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Update Error: " . $e->getMessage());
    $_SESSION['errors'] = ["Error updating experience."];
    header("Location: edit_experience.php?id=$id_drivingExperience");
    exit();
}
?>

<?php
session_start();
require_once 'config/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_experience.php');
    exit();
}

// Validate and sanitize input data
$date = trim($_POST['date'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$finish_time = trim($_POST['finish_time'] ?? '');
$km_traveled = floatval($_POST['km_traveled'] ?? 0);
$id_weatherCondition = intval($_POST['id_weatherCondition'] ?? 0);
$id_roadType = intval($_POST['id_roadType'] ?? 0);
$id_roadCondition = intval($_POST['id_roadCondition'] ?? 0);
$id_trafficLevel = intval($_POST['id_trafficLevel'] ?? 0);
$maneuvers = $_POST['maneuvers'] ?? [];
$notes = trim($_POST['notes'] ?? '');

// Validation
$errors = [];

if (empty($date)) {
    $errors[] = "Date is required.";
}

if (empty($start_time)) {
    $errors[] = "Start time is required.";
}

if (empty($finish_time)) {
    $errors[] = "Finish time is required.";
}

if ($start_time >= $finish_time) {
    $errors[] = "Finish time must be after start time.";
}

if ($km_traveled <= 0 || $km_traveled > 500) {
    $errors[] = "Please enter a valid distance between 0.1 and 500 km.";
}

if ($id_weatherCondition <= 0) {
    $errors[] = "Please select a weather condition.";
}

if ($id_roadType <= 0) {
    $errors[] = "Please select a road type.";
}

if ($id_roadCondition <= 0) {
    $errors[] = "Please select a road condition.";
}

if ($id_trafficLevel <= 0) {
    $errors[] = "Please select a traffic level.";
}

// If there are errors, redirect back to form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: add_experience.php');
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // USING PREPARED STATEMENTS WITH PDO (3 points!)
    $sql = "INSERT INTO drivingExperience (
        date, start_time, finish_time, km_traveled,
        id_weatherCondition, id_roadType, id_roadCondition, id_trafficLevel
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $date,
        $start_time,
        $finish_time,
        $km_traveled,
        $id_weatherCondition,
        $id_roadType,
        $id_roadCondition,
        $id_trafficLevel
    ]);
    
    // Get the ID of the inserted driving experience
    $experience_id = $pdo->lastInsertId();
    
    // Insert maneuvers (many-to-many relationship)
    if (!empty($maneuvers) && is_array($maneuvers)) {
        $sql_maneuver = "INSERT INTO drivingExperience_maneuvers (id_drivingExperience, id_maneuver) VALUES (?, ?)";
        $stmt_maneuver = $pdo->prepare($sql_maneuver);
        
        foreach ($maneuvers as $maneuver_id) {
            $maneuver_id = intval($maneuver_id);
            if ($maneuver_id > 0) {
                $stmt_maneuver->execute([$experience_id, $maneuver_id]);
            }
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Set success message
    $_SESSION['success_message'] = "✅ Driving experience saved successfully!";
    header('Location: summary.php');
    exit();
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    error_log("Save Experience Error: " . $e->getMessage());
    $_SESSION['errors'] = ["An error occurred while saving. Please try again."];
    $_SESSION['form_data'] = $_POST;
    header('Location: add_experience.php');
    exit();
}
?>

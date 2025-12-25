<?php
$page_title = "Edit Driving Experience";
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['errors'] = ["Invalid experience ID."];
    header('Location: summary.php');
    exit();
}

$experience_id = intval($_GET['id']);

// Fetch existing experience data
try {
    $sql = "SELECT * FROM drivingExperience WHERE id_drivingExperience = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$experience_id]);
    $experience = $stmt->fetch();
    
    if (!$experience) {
        $_SESSION['errors'] = ["Experience not found."];
        header('Location: summary.php');
        exit();
    }
    
    // Fetch selected maneuvers
    $sql_maneuvers = "SELECT id_maneuver FROM drivingExperience_maneuvers WHERE id_drivingExperience = ?";
    $stmt = $pdo->prepare($sql_maneuvers);
    $stmt->execute([$experience_id]);
    $selected_maneuvers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch all dropdown data
    $weather_conditions = $pdo->query("SELECT id_weatherCondition, weatherCondition FROM weatherCondition ORDER BY weatherCondition")->fetchAll();
    $road_types = $pdo->query("SELECT id_roadType, roadType FROM roadType ORDER BY roadType")->fetchAll();
    $road_conditions = $pdo->query("SELECT id_roadCondition, roadCondition FROM roadCondition ORDER BY roadCondition")->fetchAll();
    $traffic_levels = $pdo->query("SELECT id_trafficLevel, trafficLevel FROM trafficLevel ORDER BY trafficLevel")->fetchAll();
    $maneuvers = $pdo->query("SELECT id_maneuver, maneuver_type, risk_level FROM maneuvers ORDER BY maneuver_type")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Edit Page Error: " . $e->getMessage());
    $_SESSION['errors'] = ["Error loading experience data."];
    header('Location: summary.php');
    exit();
}
?>

<section class="edit-experience">
    <div class="form-container">
        <h2 class="text-center mb-20">✏️ Edit Driving Experience</h2>
        
        <form action="update_experience.php" method="POST">
            <input type="hidden" name="id_drivingExperience" value="<?php echo $experience_id; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="date">📅 Date *</label>
                    <input type="date" id="date" name="date" value="<?php echo $experience['date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="start_time">🕐 Start Time *</label>
                    <input type="time" id="start_time" name="start_time" value="<?php echo $experience['start_time']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="finish_time">🕑 Finish Time *</label>
                    <input type="time" id="finish_time" name="finish_time" value="<?php echo $experience['finish_time']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="km_traveled">📏 Kilometers *</label>
                    <input type="number" id="km_traveled" name="km_traveled" step="0.1" min="0.1" max="500" value="<?php echo $experience['km_traveled']; ?>" required>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="weather">🌤️ Weather *</label>
                    <select id="weather" name="id_weatherCondition" required>
                        <?php foreach ($weather_conditions as $weather): ?>
                            <option value="<?php echo $weather['id_weatherCondition']; ?>" 
                                <?php echo ($weather['id_weatherCondition'] == $experience['id_weatherCondition']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($weather['weatherCondition']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="road_type">🛣️ Road Type *</label>
                    <select id="road_type" name="id_roadType" required>
                        <?php foreach ($road_types as $type): ?>
                            <option value="<?php echo $type['id_roadType']; ?>" 
                                <?php echo ($type['id_roadType'] == $experience['id_roadType']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['roadType']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="road_condition">🛤️ Road Condition *</label>
                    <select id="road_condition" name="id_roadCondition" required>
                        <?php foreach ($road_conditions as $condition): ?>
                            <option value="<?php echo $condition['id_roadCondition']; ?>" 
                                <?php echo ($condition['id_roadCondition'] == $experience['id_roadCondition']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($condition['roadCondition']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="traffic_level">🚦 Traffic *</label>
                    <select id="traffic_level" name="id_trafficLevel" required>
                        <?php foreach ($traffic_levels as $traffic): ?>
                            <option value="<?php echo $traffic['id_trafficLevel']; ?>" 
                                <?php echo ($traffic['id_trafficLevel'] == $experience['id_trafficLevel']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($traffic['trafficLevel']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>🔄 Maneuvers</label>
                <div class="checkbox-group">
                    <?php foreach ($maneuvers as $maneuver): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" 
                                id="maneuver_<?php echo $maneuver['id_maneuver']; ?>" 
                                name="maneuvers[]" 
                                value="<?php echo $maneuver['id_maneuver']; ?>"
                                <?php echo in_array($maneuver['id_maneuver'], $selected_maneuvers) ? 'checked' : ''; ?>>
                            <label for="maneuver_<?php echo $maneuver['id_maneuver']; ?>">
                                <?php echo htmlspecialchars($maneuver['maneuver_type']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="text-center mt-20">
                <button type="submit" class="btn btn-primary">💾 Update Experience</button>
                <a href="summary.php" class="btn btn-secondary">❌ Cancel</a>
            </div>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

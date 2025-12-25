<?php
$page_title = "Add Driving Experience";
require_once 'config/database.php';
require_once 'includes/header.php';

// Fetch all dropdown data from database 
try {
    // Weather conditions
    $stmt = $pdo->query("SELECT id_weatherCondition, weatherCondition FROM weatherCondition ORDER BY weatherCondition");
    $weather_conditions = $stmt->fetchAll();
    
    // Road types
    $stmt = $pdo->query("SELECT id_roadType, roadType FROM roadType ORDER BY roadType");
    $road_types = $stmt->fetchAll();
    
    // Road conditions
    $stmt = $pdo->query("SELECT id_roadCondition, roadCondition FROM roadCondition ORDER BY roadCondition");
    $road_conditions = $stmt->fetchAll();
    
    // Traffic levels
    $stmt = $pdo->query("SELECT id_trafficLevel, trafficLevel FROM trafficLevel ORDER BY trafficLevel");
    $traffic_levels = $stmt->fetchAll();
    
    // Maneuvers (for many-to-many checkboxes)
    $stmt = $pdo->query("SELECT id_maneuver, maneuver_type, risk_level FROM maneuvers ORDER BY maneuver_type");
    $maneuvers = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Form Data Error: " . $e->getMessage());
    die("Error loading form data. Please try again later.");
}

// Get current date and time for defaults
$current_date = date('Y-m-d');
$current_time = date('H:i');
?>

<section class="add-experience">
    <div class="form-container">
        <h2 class="text-center mb-20">+ Add New Driving Experience</h2>
        
        <form action="save_experience.php" method="POST" id="experienceForm">
            
            <!-- Date and Time Section -->
            <div class="form-grid">
                <div class="form-group">
                    <label for="date">Date <span class="required">*</span></label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        value="<?php echo $current_date; ?>" 
                        max="<?php echo $current_date; ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="start_time">Start Time <span class="required">*</span></label>
                    <input 
                        type="time" 
                        id="start_time" 
                        name="start_time" 
                        value="<?php echo $current_time; ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="finish_time">Finish Time <span class="required">*</span></label>
                    <input 
                        type="time" 
                        id="finish_time" 
                        name="finish_time" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="km_traveled">Kilometers Traveled <span class="required">*</span></label>
                    <input 
                        type="number" 
                        id="km_traveled" 
                        name="km_traveled" 
                        step="0.1" 
                        min="0.1" 
                        max="2000"
                        placeholder="Enter distance in km"
                        required
                    >
                </div>
            </div>
            
            <!-- Conditions Section -->
            <div class="form-grid">
                <div class="form-group">
                    <label for="weather">Weather Condition <span class="required">*</span></label>
                    <select id="weather" name="id_weatherCondition" required>
                        <option value="">-- Select Weather --</option>
                        <?php foreach ($weather_conditions as $weather): ?>
                            <option value="<?php echo $weather['id_weatherCondition']; ?>">
                                <?php echo htmlspecialchars($weather['weatherCondition']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="road_type">Road Type <span class="required">*</span></label>
                    <select id="road_type" name="id_roadType" required>
                        <option value="">-- Select Road Type --</option>
                        <?php foreach ($road_types as $type): ?>
                            <option value="<?php echo $type['id_roadType']; ?>">
                                <?php echo htmlspecialchars($type['roadType']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="road_condition">Road Condition <span class="required">*</span></label>
                    <select id="road_condition" name="id_roadCondition" required>
                        <option value="">-- Select Road Condition --</option>
                        <?php foreach ($road_conditions as $condition): ?>
                            <option value="<?php echo $condition['id_roadCondition']; ?>">
                                <?php echo htmlspecialchars($condition['roadCondition']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="traffic_level">Traffic Level <span class="required">*</span></label>
                    <select id="traffic_level" name="id_trafficLevel" required>
                        <option value="">-- Select Traffic Level --</option>
                        <?php foreach ($traffic_levels as $traffic): ?>
                            <option value="<?php echo $traffic['id_trafficLevel']; ?>">
                                <?php echo htmlspecialchars($traffic['trafficLevel']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Maneuvers Section (Many-to-Many - Checkboxes) -->
            <div class="form-group">
                <label>🔄 Maneuvers Performed</label>
                <p style="font-size: 0.9rem; color: #6b7280; margin-bottom: 10px;">
                    Select all maneuvers you performed during this drive:
                </p>
                <div class="checkbox-group">
                    <?php foreach ($maneuvers as $maneuver): ?>
                        <div class="checkbox-item">
                            <input 
                                type="checkbox" 
                                id="maneuver_<?php echo $maneuver['id_maneuver']; ?>" 
                                name="maneuvers[]" 
                                value="<?php echo $maneuver['id_maneuver']; ?>"
                            >
                            <label for="maneuver_<?php echo $maneuver['id_maneuver']; ?>">
                                <?php echo htmlspecialchars($maneuver['maneuver_type']); ?>
                                <small style="color: <?php 
                                    echo $maneuver['risk_level'] == 'High' ? '#ef4444' : 
                                        ($maneuver['risk_level'] == 'Medium' ? '#f59e0b' : '#10b981'); 
                                ?>;">
                                    (<?php echo $maneuver['risk_level']; ?>)
                                </small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Optional Notes -->
            <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="4" 
                    placeholder="Add any additional notes about your driving experience..."
                ></textarea>
            </div>
            
            <!-- Submit Button -->
            <div class="text-center mt-20" style="display: flex; gap: 12px; justify-content: center; align-items: center;">
                <button type="submit" class="btn btn-primary">Save Experience</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>

<!-- Form Validation JavaScript -->
<script>
document.getElementById('experienceForm').addEventListener('submit', function(e) {
    const startTime = document.getElementById('start_time').value;
    const finishTime = document.getElementById('finish_time').value;
    
    if (startTime && finishTime) {
        const start = new Date('1970-01-01T' + startTime);
        const finish = new Date('1970-01-01T' + finishTime);
        
        if (finish <= start) {
            e.preventDefault();
            alert('⚠️ Finish time must be after start time!');
            return false;
        }
    }
    
    const km = parseFloat(document.getElementById('km_traveled').value);
    if (km <= 0 || km > 2000) {
        e.preventDefault();
        alert('⚠️ Please enter a valid distance between 0.1 and 2000 km!');
        return false;
    }
});

// Auto-calculate duration 
document.getElementById('finish_time').addEventListener('change', function() {
    const startTime = document.getElementById('start_time').value;
    const finishTime = this.value;
    
    if (startTime && finishTime) {
        const start = new Date('1970-01-01T' + startTime);
        const finish = new Date('1970-01-01T' + finishTime);
        const diff = (finish - start) / 1000 / 60; // minutes
        
        if (diff > 0) {
            const hours = Math.floor(diff / 60);
            const minutes = Math.floor(diff % 60);
            console.log(`Duration: ${hours}h ${minutes}m`);
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
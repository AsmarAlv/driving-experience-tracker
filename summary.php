<?php
$page_title = "Summary - All Driving Experiences";
require_once 'config/database.php';
require_once 'includes/header.php';

// Display success message if exists
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

// Display error messages if exists
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-error">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}

try {
    // USING JOIN QUERIES (2 points!)
    $sql = "
        SELECT 
            de.id_drivingExperience,
            de.date,
            de.start_time,
            de.finish_time,
            de.km_traveled,
            wc.weatherCondition,
            rt.roadType,
            rc.roadCondition,
            tl.trafficLevel,
            GROUP_CONCAT(m.maneuver_type SEPARATOR ', ') as maneuvers
        FROM drivingExperience de
        INNER JOIN weatherCondition wc ON de.id_weatherCondition = wc.id_weatherCondition
        INNER JOIN roadType rt ON de.id_roadType = rt.id_roadType
        INNER JOIN roadCondition rc ON de.id_roadCondition = rc.id_roadCondition
        INNER JOIN trafficLevel tl ON de.id_trafficLevel = tl.id_trafficLevel
        LEFT JOIN drivingExperience_maneuvers dem ON de.id_drivingExperience = dem.id_drivingExperience
        LEFT JOIN maneuvers m ON dem.id_maneuver = m.id_maneuver
        GROUP BY de.id_drivingExperience
        ORDER BY de.date DESC, de.start_time DESC
    ";
    
    $stmt = $pdo->query($sql);
    $experiences = $stmt->fetchAll();
    
    // Calculate total kilometers
    $stmt = $pdo->query("SELECT SUM(km_traveled) as total FROM drivingExperience");
    $total_km = $stmt->fetch()['total'] ?? 0;
    
} catch (PDOException $e) {
    error_log("Summary Page Error: " . $e->getMessage());
    echo '<div class="alert alert-error">Error loading driving experiences.</div>';
    $experiences = [];
    $total_km = 0;
}
?>

<section class="summary">
    <h2 class="text-center mb-20">📋 All Driving Experiences</h2>
    
    <!-- Total Kilometers Display -->
    <div class="card text-center mb-20" style="max-width: 400px; margin: 0 auto 30px;">
        <h3>🎯 Total Distance Traveled</h3>
        <div class="card-stat" style="color: #8b5cf6;"><?php echo number_format($total_km, 1); ?> km</div>
    </div>
    
    <?php if (empty($experiences)): ?>
        <div class="alert alert-info">
            <p>📝 No driving experiences recorded yet.</p>
            <a href="add_experience.php" class="btn btn-primary mt-20">Add Your First Experience</a>
        </div>
    <?php else: ?>
        
        <!-- Desktop Table View -->
        <div class="table-container desktop-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3>All Experiences (<?php echo count($experiences); ?>)</h3>
                <a href="add_experience.php" class="btn btn-primary">➕ Add New</a>
            </div>
            
            <table id="experiencesTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>Finish Time</th>
                        <th>Duration</th>
                        <th>Distance (km)</th>
                        <th>Weather</th>
                        <th>Road Type</th>
                        <th>Road Condition</th>
                        <th>Traffic</th>
                        <th>Maneuvers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($experiences as $exp): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($exp['date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($exp['start_time'])); ?></td>
                            <td><?php echo date('H:i', strtotime($exp['finish_time'])); ?></td>
                            <td>
                                <?php 
                                $start = new DateTime($exp['start_time']);
                                $finish = new DateTime($exp['finish_time']);
                                $duration = $start->diff($finish);
                                echo $duration->format('%h:%I');
                                ?>
                            </td>
                            <td><strong><?php echo number_format($exp['km_traveled'], 1); ?></strong></td>
                            <td><?php echo htmlspecialchars($exp['weatherCondition']); ?></td>
                            <td><?php echo htmlspecialchars($exp['roadType']); ?></td>
                            <td><?php echo htmlspecialchars($exp['roadCondition']); ?></td>
                            <td><?php echo htmlspecialchars($exp['trafficLevel']); ?></td>
                            <td style="font-size: 0.85rem;">
                                <?php echo $exp['maneuvers'] ? htmlspecialchars($exp['maneuvers']) : '—'; ?>
                            </td>
                            <td>
                                <a href="edit_experience.php?id=<?php echo $exp['id_drivingExperience']; ?>" 
                                   class="btn btn-secondary" 
                                   style="padding: 5px 10px; font-size: 0.85rem;">
                                    ✏️ Edit
                                </a>
                                <a href="delete_experience.php?id=<?php echo $exp['id_drivingExperience']; ?>" 
                                   class="btn btn-danger" 
                                   style="padding: 5px 10px; font-size: 0.85rem;"
                                   onclick="return confirm('Are you sure you want to delete this experience?')">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards View -->
        <div class="mobile-cards" style="display: none;">
            <div style="margin-bottom: 15px;">
                <a href="add_experience.php" class="btn btn-primary" style="width: 100%;">➕ Add New Experience</a>
            </div>
            
            <?php foreach ($experiences as $exp): ?>
                <div class="card mb-20">
                    <h3 style="color: #8b5cf6; margin-bottom: 15px;">
                        📅 <?php echo date('M d, Y', strtotime($exp['date'])); ?>
                    </h3>
                    
                    <p><strong>⏰ Time:</strong> <?php echo date('H:i', strtotime($exp['start_time'])); ?> - <?php echo date('H:i', strtotime($exp['finish_time'])); ?></p>
                    <p><strong>📏 Distance:</strong> <?php echo number_format($exp['km_traveled'], 1); ?> km</p>
                    <p><strong>🌤️ Weather:</strong> <?php echo htmlspecialchars($exp['weatherCondition']); ?></p>
                    <p><strong>🛣️ Road:</strong> <?php echo htmlspecialchars($exp['roadType']); ?> (<?php echo htmlspecialchars($exp['roadCondition']); ?>)</p>
                    <p><strong>🚦 Traffic:</strong> <?php echo htmlspecialchars($exp['trafficLevel']); ?></p>
                    
                    <?php if ($exp['maneuvers']): ?>
                        <p><strong>🔄 Maneuvers:</strong> <?php echo htmlspecialchars($exp['maneuvers']); ?></p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <a href="edit_experience.php?id=<?php echo $exp['id_drivingExperience']; ?>" 
                           class="btn btn-secondary" style="flex: 1;">✏️ Edit</a>
                        <a href="delete_experience.php?id=<?php echo $exp['id_drivingExperience']; ?>" 
                           class="btn btn-danger" style="flex: 1;"
                           onclick="return confirm('Delete this experience?')">🗑️ Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    <?php endif; ?>
</section>

<!-- Responsive JavaScript -->
<script>
function checkScreenSize() {
    const desktopTable = document.querySelector('.desktop-table');
    const mobileCards = document.querySelector('.mobile-cards');
    
    if (window.innerWidth <= 768) {
        if (desktopTable) desktopTable.style.display = 'none';
        if (mobileCards) mobileCards.style.display = 'block';
    } else {
        if (desktopTable) desktopTable.style.display = 'block';
        if (mobileCards) mobileCards.style.display = 'none';
    }
}

// Check on load and resize
window.addEventListener('load', checkScreenSize);
window.addEventListener('resize', checkScreenSize);
</script>

<?php require_once 'includes/footer.php'; ?>

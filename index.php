<?php
$page_title = "Dashboard - Driving Experience";
require_once 'config/database.php';
require_once 'includes/header.php';

// Get statistics
try {
    // Total kilometers
    $stmt = $pdo->query("SELECT SUM(km_traveled) as total_km FROM drivingExperience");
    $total_km = $stmt->fetch()['total_km'] ?? 0;
    
    // Total experiences
    $stmt = $pdo->query("SELECT COUNT(*) as total_exp FROM drivingExperience");
    $total_experiences = $stmt->fetch()['total_exp'];
    
    // Recent experiences (last 5)
    $stmt = $pdo->query("
        SELECT 
            de.id_drivingExperience,
            de.date,
            de.start_time,
            de.finish_time,
            de.km_traveled,
            wc.weatherCondition,
            rt.roadType
        FROM drivingExperience de
        JOIN weatherCondition wc ON de.id_weatherCondition = wc.id_weatherCondition
        JOIN roadType rt ON de.id_roadType = rt.id_roadType
        ORDER BY de.date DESC, de.start_time DESC
        LIMIT 5
    ");
    $recent_experiences = $stmt->fetchAll();
    
    // Average km per trip
    $avg_km = $total_experiences > 0 ? round($total_km / $total_experiences, 2) : 0;
    
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error_message = "Error loading dashboard data.";
}
?>

<section class="dashboard">
    <h2 class="text-center mb-20">📊 Your Driving Statistics</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="cards-grid">
        <div class="card">
            <h3>🚗 Total Experiences</h3>
            <div class="card-stat"><?php echo $total_experiences; ?></div>
            <p>Driving sessions recorded</p>
        </div>
        
        <div class="card">
            <h3>📏 Total Distance</h3>
            <div class="card-stat"><?php echo number_format($total_km, 1); ?> km</div>
            <p>Total kilometers driven</p>
        </div>
        
        <div class="card">
            <h3>📊 Average Trip</h3>
            <div class="card-stat"><?php echo number_format($avg_km, 1); ?> km</div>
            <p>Average distance per trip</p>
        </div>
        
        <div class="card">
            <h3>⏱️ Total Time</h3>
            <div class="card-stat">
                <?php 
                // Calculate total hours (rough estimate based on avg speed ~40km/h)
                $total_hours = round($total_km / 40, 1);
                echo $total_hours . ' hrs';
                ?>
            </div>
            <p>Estimated driving time</p>
        </div>
    </div>
    
    <!-- Recent Experiences -->
    <div class="table-container mt-20">
        <h3>🕒 Recent Driving Experiences</h3>
        
        <?php if (empty($recent_experiences)): ?>
            <div class="alert alert-info mt-20">
                <p>No driving experiences recorded yet. <a href="add_experience.php">Add your first experience!</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Distance (km)</th>
                        <th>Weather</th>
                        <th>Road Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_experiences as $exp): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($exp['date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($exp['start_time'])); ?></td>
                            <td>
                                <?php 
                                $start = new DateTime($exp['start_time']);
                                $finish = new DateTime($exp['finish_time']);
                                $duration = $start->diff($finish);
                                echo $duration->format('%h:%I');
                                ?>
                            </td>
                            <td><?php echo number_format($exp['km_traveled'], 1); ?></td>
                            <td><?php echo htmlspecialchars($exp['weatherCondition']); ?></td>
                            <td><?php echo htmlspecialchars($exp['roadType']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-20 text-center">
        <a href="add_experience.php" class="btn btn-primary">➕ Add New Experience</a>
        <a href="summary.php" class="btn btn-secondary">📋 View All Experiences</a>
        <a href="statistics.php" class="btn btn-success">📈 View Statistics</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

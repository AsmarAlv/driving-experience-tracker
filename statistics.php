<?php
$page_title = "Statistics & Analytics";
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    // Total kilometers by weather
    $stmt = $pdo->query("
        SELECT wc.weatherCondition, SUM(de.km_traveled) as total_km, COUNT(*) as count
        FROM drivingExperience de
        JOIN weatherCondition wc ON de.id_weatherCondition = wc.id_weatherCondition
        GROUP BY wc.weatherCondition
        ORDER BY total_km DESC
    ");
    $weather_stats = $stmt->fetchAll();
    
    // Total kilometers by road type
    $stmt = $pdo->query("
        SELECT rt.roadType, SUM(de.km_traveled) as total_km, COUNT(*) as count
        FROM drivingExperience de
        JOIN roadType rt ON de.id_roadType = rt.id_roadType
        GROUP BY rt.roadType
        ORDER BY total_km DESC
    ");
    $road_type_stats = $stmt->fetchAll();
    
    // Traffic level distribution
    $stmt = $pdo->query("
        SELECT tl.trafficLevel, COUNT(*) as count
        FROM drivingExperience de
        JOIN trafficLevel tl ON de.id_trafficLevel = tl.id_trafficLevel
        GROUP BY tl.trafficLevel
        ORDER BY FIELD(tl.trafficLevel, 'Low', 'Medium', 'High')
    ");
    $traffic_stats = $stmt->fetchAll();
    
    // Daily kilometers (last 30 days)
    $stmt = $pdo->query("
        SELECT date, SUM(km_traveled) as total_km
        FROM drivingExperience
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY date
        ORDER BY date ASC
    ");
    $daily_km = $stmt->fetchAll();
    
    // Most used maneuvers
    $stmt = $pdo->query("
        SELECT m.maneuver_type, COUNT(*) as count
        FROM drivingExperience_maneuvers dem
        JOIN maneuvers m ON dem.id_maneuver = m.id_maneuver
        GROUP BY m.maneuver_type
        ORDER BY count DESC
        LIMIT 10
    ");
    $maneuver_stats = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Statistics Error: " . $e->getMessage());
    echo '<div class="alert alert-error">Error loading statistics.</div>';
}
?>

<section class="statistics">
    <h2 class="text-center mb-20">📈 Driving Statistics & Analytics</h2>
    
    <!-- Weather Condition Statistics -->
    <div class="chart-container">
        <h3>🌤️ Kilometers by Weather Condition</h3>
        <canvas id="weatherChart"></canvas>
    </div>
    
    <!-- Road Type Statistics -->
    <div class="chart-container">
        <h3>🛣️ Kilometers by Road Type</h3>
        <canvas id="roadTypeChart"></canvas>
    </div>
    
    <!-- Traffic Level Distribution -->
    <div class="chart-container">
        <h3>🚦 Trips by Traffic Level</h3>
        <canvas id="trafficChart"></canvas>
    </div>
    
    <!-- Daily Kilometers Chart -->
    <div class="chart-container">
        <h3>📅 Daily Kilometers (Last 30 Days)</h3>
        <canvas id="dailyKmChart"></canvas>
    </div>
    
    <!-- Maneuvers Statistics -->
    <div class="chart-container">
        <h3>🔄 Most Performed Maneuvers</h3>
        <canvas id="maneuversChart"></canvas>
    </div>
    
    <!-- Summary Tables -->
    <div class="cards-grid mt-20">
        <div class="card">
            <h3>Weather Summary</h3>
            <table style="width: 100%; font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th>Weather</th>
                        <th>Trips</th>
                        <th>Total KM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weather_stats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['weatherCondition']); ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo number_format($stat['total_km'], 1); ?> km</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h3>Road Type Summary</h3>
            <table style="width: 100%; font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th>Road Type</th>
                        <th>Trips</th>
                        <th>Total KM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($road_type_stats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['roadType']); ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo number_format($stat['total_km'], 1); ?> km</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Chart.js Scripts -->
<script>
// Weather Chart
const weatherCtx = document.getElementById('weatherChart').getContext('2d');
new Chart(weatherCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($weather_stats, 'weatherCondition')); ?>,
        datasets: [{
            label: 'Kilometers',
            data: <?php echo json_encode(array_column($weather_stats, 'total_km')); ?>,
            backgroundColor: 'rgba(139, 92, 246, 0.6)',
            borderColor: 'rgba(139, 92, 246, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Kilometers'
                }
            }
        }
    }
});

// Road Type Chart
const roadTypeCtx = document.getElementById('roadTypeChart').getContext('2d');
new Chart(roadTypeCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($road_type_stats, 'roadType')); ?>,
        datasets: [{
            label: 'Kilometers',
            data: <?php echo json_encode(array_column($road_type_stats, 'total_km')); ?>,
            backgroundColor: [
                'rgba(139, 92, 246, 0.8)',
                'rgba(99, 102, 241, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Traffic Chart
const trafficCtx = document.getElementById('trafficChart').getContext('2d');
new Chart(trafficCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($traffic_stats, 'trafficLevel')); ?>,
        datasets: [{
            label: 'Number of Trips',
            data: <?php echo json_encode(array_column($traffic_stats, 'count')); ?>,
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Daily Kilometers Chart
const dailyKmCtx = document.getElementById('dailyKmChart').getContext('2d');
new Chart(dailyKmCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_map(function($d) { 
            return date('M d', strtotime($d['date'])); 
        }, $daily_km)); ?>,
        datasets: [{
            label: 'Kilometers',
            data: <?php echo json_encode(array_column($daily_km, 'total_km')); ?>,
            backgroundColor: 'rgba(99, 102, 241, 0.2)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Kilometers'
                }
            }
        }
    }
});

// Maneuvers Chart
const maneuversCtx = document.getElementById('maneuversChart').getContext('2d');
new Chart(maneuversCtx, {
    type: 'horizontalBar',
    data: {
        labels: <?php echo json_encode(array_column($maneuver_stats, 'maneuver_type')); ?>,
        datasets: [{
            label: 'Times Performed',
            data: <?php echo json_encode(array_column($maneuver_stats, 'count')); ?>,
            backgroundColor: 'rgba(236, 72, 153, 0.6)',
            borderColor: 'rgba(236, 72, 153, 1)',
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            x: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Times'
                }
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

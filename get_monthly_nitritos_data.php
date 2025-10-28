<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get the tagid filter parameter (optional)
    $tagid_filter = $_GET['tagid'] ?? null;
    
    // Build the SQL query to get exactly one year back from now for all pond_ids
    $sql = "SELECT 
                pond_id,
                YEAR(timestamp) as year,
                MONTH(timestamp) as month,
                AVG(nitrite_mg_l) as avg_nitrite,
                COUNT(*) as measurement_count,
                DATE_FORMAT(timestamp, '%Y-%m') as month_label
            FROM water_nitrite_log
            WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              AND timestamp <= CURDATE()";
    
    $params = [];
    
    // Add tagid filter if provided
    if ($tagid_filter && $tagid_filter !== 'all') {
        $sql .= " AND pond_id = ?";
        $params[] = $tagid_filter;
    }
    
    $sql .= " GROUP BY pond_id, YEAR(timestamp), MONTH(timestamp)
              ORDER BY YEAR(timestamp), MONTH(timestamp), pond_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Also get all available pond IDs for the dropdown (from the same time period)
    $pond_sql = "SELECT DISTINCT pond_id 
                 FROM water_nitrite_log 
                 WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                   AND timestamp <= CURDATE()
                 ORDER BY pond_id";
    $pond_stmt = $conn->prepare($pond_sql);
    $pond_stmt->execute();
    $ponds = $pond_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Organize data for Chart.js
    $chartData = [];
    $labels = [];
    $datasets = [];
    
    // Generate complete 12-month range (even if no data for some months)
    $months = [];
    $labels = [];
    $currentDate = new DateTime();
    
    // Go back 11 months to start from 12 months ago
    $startDate = clone $currentDate;
    $startDate->modify('-11 months');
    $startDate->modify('first day of this month');
    
    // Generate 12 months of labels
    for ($i = 0; $i < 12; $i++) {
        $monthKey = $startDate->format('Y-m');
        $months[] = $monthKey;
        $labels[] = $startDate->format('M Y'); // e.g., "Jan 2025"
        $startDate->modify('+1 month');
    }
    
    // Also include any months from actual data that might be outside this range
    foreach ($results as $row) {
        $monthKey = $row['month_label'];
        if (!in_array($monthKey, $months)) {
            $months[] = $monthKey;
            $date = DateTime::createFromFormat('Y-m', $monthKey);
            $labels[] = $date->format('M Y');
        }
    }
    
    // Sort everything chronologically
    array_multisort($months, $labels);
    
    // Organize data by pond
    $pondData = [];
    foreach ($results as $row) {
        $pondId = $row['pond_id'];
        $monthKey = $row['month_label'];
        
        if (!isset($pondData[$pondId])) {
            $pondData[$pondId] = [];
        }
        
        $pondData[$pondId][$monthKey] = round(floatval($row['avg_nitrite']), 2);
    }
    
    // Create datasets for each pond
    $colors = [
        'rgba(255, 99, 132, 1)',   // Red
        'rgba(54, 162, 235, 1)',   // Blue
        'rgba(255, 205, 86, 1)',   // Yellow
        'rgba(75, 192, 192, 1)',   // Green
        'rgba(153, 102, 255, 1)',  // Purple
        'rgba(255, 159, 64, 1)',   // Orange
        'rgba(199, 199, 199, 1)',  // Grey
        'rgba(83, 102, 255, 1)',   // Indigo
        'rgba(255, 99, 255, 1)',   // Pink
        'rgba(99, 255, 132, 1)'    // Light Green
    ];
    
    $colorIndex = 0;
    foreach ($pondData as $pondId => $monthlyData) {
        $data = [];
        
        // Fill data for each month, use null if no data
        foreach ($months as $month) {
            $data[] = isset($monthlyData[$month]) ? $monthlyData[$month] : null;
        }
        
        $color = $colors[$colorIndex % count($colors)];
        $backgroundColor = str_replace('1)', '0.2)', $color); // Make background transparent
        
        $datasets[] = [
            'label' => "Estanque " . $pondId,
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => $backgroundColor,
            'borderWidth' => 2,
            'fill' => false,
            'tension' => 0.4, // Smooth lines
            'pointRadius' => 4,
            'pointHoverRadius' => 6
        ];
        
        $colorIndex++;
    }
    
    // Response data
    $response = [
        'success' => true,
        'labels' => $labels,
        'datasets' => $datasets,
        'ponds' => $ponds,
        'total_measurements' => count($results),
        'months_covered' => count($months)
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos de nitritos: ' . $e->getMessage()
    ]);
}

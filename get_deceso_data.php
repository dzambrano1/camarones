<?php
// Include database connection
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Verify connection is PDO
    if (!($conn instanceof PDO)) {
        throw new Exception("Error: La conexión no es una instancia de PDO");
    }
    
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get monthly average monto and loss data from decesos_camarones_estanques table
    $query = "SELECT 
                DATE_FORMAT(cah_decesos_fecha, '%Y-%m') AS month,
                AVG(cah_decesos_precio * cah_decesos_cantidad) AS avg_monthly_monto,
                SUM(cah_decesos_precio * cah_decesos_cantidad) AS total_monthly_loss,
                COUNT(*) AS total_deaths,
                AVG(cah_decesos_peso) AS avg_weight,
                AVG(cah_decesos_precio) AS avg_price,
                SUM(cah_decesos_cantidad) AS total_quantity_kg,
                AVG(cah_decesos_cantidad / cah_decesos_peso * 100) AS avg_loss_percentage
              FROM cah_decesos
              WHERE 
                cah_decesos_fecha IS NOT NULL AND 
                cah_decesos_fecha != '0000-00-00' AND
                cah_decesos_precio IS NOT NULL AND
                cah_decesos_cantidad IS NOT NULL AND
                cah_decesos_precio IS NOT NULL AND
                cah_decesos_peso IS NOT NULL AND
                cah_decesos_precio * cah_decesos_cantidad > 0
              GROUP BY DATE_FORMAT(cah_decesos_fecha, '%Y-%m')
              ORDER BY month ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize data structures
    $months_data = [];
    $all_months = [];
    $cumulative_loss = 0;
    
    foreach ($monthlyData as $row) {
        $monthYear = $row['month'];
        
        // Format month for display
        $date = DateTime::createFromFormat('Y-m', $monthYear);
        $monthName = '';
        
        if ($date) {
            $months = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            $monthNum = $date->format('m');
            $year = $date->format('Y');
            $monthName = $months[$monthNum] . ' ' . $year;
        } else {
            $monthName = $monthYear;
        }
        
        $avg_monthly_monto = round((float)$row['avg_monthly_monto'], 2);
        $total_monthly_loss = round((float)$row['total_monthly_loss'], 2);
        $cumulative_loss += $total_monthly_loss;
        
        $months_data[$monthName] = [
            'avg_monthly_monto' => $avg_monthly_monto,
            'total_monthly_loss' => $total_monthly_loss,
            'cumulative_loss' => round($cumulative_loss, 2),
            'total_deaths' => (int)$row['total_deaths'],
            'avg_weight' => round((float)$row['avg_weight'], 2),
            'avg_price' => round((float)$row['avg_price'], 2),
            'total_quantity_kg' => round((float)$row['total_quantity_kg'], 2),
            'avg_loss_percentage' => round((float)$row['avg_loss_percentage'], 2)
        ];
        
        $all_months[] = $monthName;
    }
    
    // Prepare datasets for Chart.js
    $labels = $all_months;
    $datasets = [];
    
    // Colors for different series
    $colors = [
        'monthly' => ['rgba(108, 117, 125, 0.8)', '#6c757d'],    // Gray for monthly avg bars
        'cumulative' => ['rgba(220, 53, 69, 0.8)', '#dc3545']   // Red for cumulative loss line
    ];
    
    // Create dataset for monthly average monto (bar chart on primary y-axis)
    $monthly_avg_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_monthly_monto'] : 0;
        $monthly_avg_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Promedio Mensual Monto ($)',
        'data' => $monthly_avg_data,
        'type' => 'bar',
        'backgroundColor' => $colors['monthly'][0],
        'borderColor' => $colors['monthly'][1],
        'borderWidth' => 1,
        'yAxisID' => 'y'  // Primary Y-axis for monthly averages
    ];
    
    // Create dataset for cumulative loss (line chart on secondary y-axis)
    $cumulative_loss_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['cumulative_loss'] : null;
        $cumulative_loss_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Pérdidas Acumuladas ($)',
        'data' => $cumulative_loss_data,
        'type' => 'line',
        'backgroundColor' => $colors['cumulative'][0],
        'borderColor' => $colors['cumulative'][1],
        'borderWidth' => 3,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['cumulative'][1],
        'pointBorderColor' => $colors['cumulative'][1],
        'pointRadius' => 5,
        'yAxisID' => 'y1'  // Secondary Y-axis for cumulative losses
    ];
    
    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'deaths_data' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['deaths_data'] = [
                'avg_monthly_monto' => '$' . number_format($data['avg_monthly_monto'], 2),
                'total_monthly_loss' => '$' . number_format($data['total_monthly_loss'], 2),
                'cumulative_loss' => '$' . number_format($data['cumulative_loss'], 2),
                'total_deaths' => $data['total_deaths'],
                'avg_weight' => number_format($data['avg_weight'], 2) . ' kg',
                'avg_price' => '$' . number_format($data['avg_price'], 2) . '/kg',
                'total_quantity_kg' => number_format($data['total_quantity_kg'], 2) . ' kg',
                'avg_loss_percentage' => number_format($data['avg_loss_percentage'], 2) . '%'
            ];
        } else {
            $month_tooltip['deaths_data'] = [
                'avg_monthly_monto' => '$0.00',
                'total_monthly_loss' => '$0.00',
                'cumulative_loss' => '$0.00',
                'total_deaths' => 0,
                'avg_weight' => '0.00 kg',
                'avg_price' => '$0.00/kg',
                'total_quantity_kg' => '0.00 kg',
                'avg_loss_percentage' => '0.00%'
            ];
        }
        
        $tooltipData[] = $month_tooltip;
    }
    
    // If no data found, provide empty arrays
    if (empty($labels)) {
        $labels = ['Sin Datos'];
        $datasets = [
            [
                'label' => 'Promedio Mensual Monto ($)',
                'data' => [0],
                'type' => 'bar',
                'backgroundColor' => 'rgba(210, 180, 140, 0.8)',
                'borderColor' => '#d2b48c',
                'borderWidth' => 1,
                'yAxisID' => 'y'
            ],
            [
                'label' => 'Pérdidas Acumuladas ($)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(169, 169, 169, 0.8)',
                'borderColor' => '#a9a9a9',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
                'yAxisID' => 'y1'
            ]
        ];
        $tooltipData = [[
            'month' => 'Sin Datos',
            'deaths_data' => [
                'avg_monthly_monto' => '$0.00',
                'total_monthly_loss' => '$0.00',
                'cumulative_loss' => '$0.00',
                'total_deaths' => 0,
                'avg_weight' => '0.00 kg',
                'avg_price' => '$0.00/kg',
                'total_quantity_kg' => '0.00 kg',
                'avg_loss_percentage' => '0.00%'
            ]
        ]];
    }
    
    // Return the data in the format expected by Chart.js
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'datasets' => $datasets,
        'tooltipData' => $tooltipData,
        'summary' => [
            'series_count' => 2,
            'months_count' => count($labels),
            'data_type' => 'deaths_monthly_avg_and_cumulative',
            'has_dual_axis' => true
        ]
    ]);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}

<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable error reporting in PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get monthly purchases data from cah_compras table
      $query = "SELECT 
                  DATE_FORMAT(cah_compras_fecha, '%Y-%m') AS month,
                SUM(CASE WHEN cah_compras_precio IS NOT NULL THEN cah_compras_precio * cah_compras_peso ELSE 0 END) AS monthly_amount,
                  COUNT(*) AS total_purchases
                FROM cah_compras
                WHERE 
                  cah_compras_fecha IS NOT NULL AND 
                  cah_compras_fecha != '0000-00-00' AND
                  cah_compras_precio IS NOT NULL AND
                  cah_compras_peso IS NOT NULL
                GROUP BY DATE_FORMAT(cah_compras_fecha, '%Y-%m')
                ORDER BY month ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize data structures
    $months_data = [];
    $all_months = [];
    $cumulative_amount = 0;
    
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
        
        $monthly_amount = round((float)$row['monthly_amount'], 2);
        $cumulative_amount += $monthly_amount;
        
        $months_data[$monthName] = [
            'monthly_amount' => $monthly_amount,
            'cumulative_amount' => round($cumulative_amount, 2),
            'total_purchases' => (int)$row['total_purchases']
        ];
        
        $all_months[] = $monthName;
    }
    
    // Prepare datasets for Chart.js
    $labels = $all_months;
    $datasets = [];
    
    // Colors for different series
    $colors = [
        'monthly' => ['rgba(102, 16, 242, 0.8)', '#6610f2'],    // Purple for monthly bars
        'cumulative' => ['rgba(255, 193, 7, 0.8)', '#ffc107']  // Yellow for cumulative line
    ];
    
    // Create dataset for monthly purchases (bar chart on primary y-axis)
    $monthly_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['monthly_amount'] : 0;
        $monthly_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Compras Mensuales ($)',
        'data' => $monthly_data,
        'type' => 'bar',
        'backgroundColor' => $colors['monthly'][0],
        'borderColor' => $colors['monthly'][1],
        'borderWidth' => 1,
        'yAxisID' => 'y'  // Primary Y-axis for monthly amounts
    ];
    
    // Create dataset for cumulative purchases (line chart on secondary y-axis)
    $cumulative_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['cumulative_amount'] : null;
        $cumulative_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Compras Acumuladas ($)',
        'data' => $cumulative_data,
        'type' => 'line',
        'backgroundColor' => $colors['cumulative'][0],
        'borderColor' => $colors['cumulative'][1],
        'borderWidth' => 3,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['cumulative'][1],
        'pointBorderColor' => $colors['cumulative'][1],
        'pointRadius' => 5,
        'yAxisID' => 'y1'  // Secondary Y-axis for cumulative amounts
    ];
    
    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'purchases_data' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['purchases_data'] = [
                'monthly_amount' => '$' . number_format($data['monthly_amount'], 2),
                'cumulative_amount' => '$' . number_format($data['cumulative_amount'], 2),
                'total_purchases' => $data['total_purchases']
            ];
        } else {
            $month_tooltip['purchases_data'] = [
                'monthly_amount' => '$0.00',
                'cumulative_amount' => '$0.00',
                'total_purchases' => 0
            ];
        }
        
        $tooltipData[] = $month_tooltip;
    }
    
    // If no data found, provide empty arrays
    if (empty($labels)) {
        $labels = ['Sin Datos'];
        $datasets = [
            [
                'label' => 'Compras Mensuales ($)',
                'data' => [0],
                'type' => 'bar',
                'backgroundColor' => 'rgba(210, 180, 140, 0.8)',
                'borderColor' => '#d2b48c',
                'borderWidth' => 1,
                'yAxisID' => 'y'
            ],
            [
                'label' => 'Compras Acumuladas ($)',
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
            'purchases_data' => [
                'monthly_amount' => '$0.00',
                'cumulative_amount' => '$0.00',
                'total_purchases' => 0
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
            'data_type' => 'purchases_monthly_and_cumulative',
            'has_dual_axis' => true
        ]
    ]);
    
} catch (PDOException $e) {
    // Return error message
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    // Log the error
    error_log('Error in get_purchases_data.php: ' . $e->getMessage());
}
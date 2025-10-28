<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    // Database connection is already established in pdo_conexion.php as $conn (PDO)
    // No need to create a new connection

    // Get monthly salinidad expenses and min/max salinidad levels
    $sql = "SELECT
                DATE_FORMAT(timestamp, '%Y-%m') as month,
                SUM(product_qty * product_price) as total_expenses,
                AVG(product_qty * product_price) as avg_expense_per_treatment,
                COUNT(*) as treatment_count,
                MIN(LEAST(salinity_ppt, salinity_ppt)) as min_salinidad_level,
                MAX(GREATEST(salinity_ppt, salinity_ppt)) as max_salinidad_level,
                AVG(salinity_ppt) as avg_salinidad_antes,
                AVG(salinity_ppt) as avg_salinidad_despues
            FROM water_salinity_log
            WHERE timestamp IS NOT NULL
                AND product_qty IS NOT NULL
                AND product_price IS NOT NULL
                AND salinity_ppt IS NOT NULL
                AND salinity_ppt IS NOT NULL
            GROUP BY DATE_FORMAT(timestamp, '%Y-%m')
            ORDER BY month ASC";

    $result = $conn->prepare($sql);
    $result->execute();

    // Initialize data structures
    $months_data = [];
    $all_months = [];
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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

            // Store expense and salinidad data for this month
            $months_data[$monthName] = [
                'total_expenses' => round((float)$row['total_expenses'], 2),
                'avg_expense_per_treatment' => round((float)$row['avg_expense_per_treatment'], 2),
                'treatment_count' => (int)$row['treatment_count'],
                'min_salinidad_level' => round((float)$row['min_salinidad_level'], 2),
                'max_salinidad_level' => round((float)$row['max_salinidad_level'], 2),
                'avg_salinidad_antes' => round((float)$row['avg_salinidad_antes'], 2),
                'avg_salinidad_despues' => round((float)$row['avg_salinidad_despues'], 2)
            ];
            
            $all_months[] = $monthName;
    }

    // Remove duplicates and sort months
    $all_months = array_unique($all_months);
    
    // Prepare datasets
    $labels = $all_months;
    $datasets = [];
    
    // Colors for different series - distinct and vibrant
    $colors = [
        'expenses' => ['rgba(255, 193, 7, 0.8)', '#FFC107'],      // Amber/Yellow for expenses (bar)
        'min_salinidad' => ['rgba(34, 139, 34, 0.8)', '#228B22'], // Green for min salinidad (line)
        'max_salinidad' => ['rgba(220, 20, 60, 0.8)', '#DC143C']  // Crimson for max salinidad (line)
    ];

    // Create dataset for monthly expenses (bar chart on primary y-axis)
    $expenses_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['total_expenses'] : 0;
        $expenses_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Gastos Mensuales ($)',
        'data' => $expenses_data,
        'type' => 'bar',
        'backgroundColor' => $colors['expenses'][0],
        'borderColor' => $colors['expenses'][1],
        'borderWidth' => 1,
        'yAxisID' => 'y'  // Primary Y-axis for dollar amounts
    ];

    // Create dataset for minimum salinidad levels (line chart on secondary y-axis)
    $min_salinidad_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['min_salinidad_level'] : null;
        $min_salinidad_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Salinidad Mínima (ppt)',
        'data' => $min_salinidad_data,
        'type' => 'line',
        'backgroundColor' => $colors['min_salinidad'][0],
        'borderColor' => $colors['min_salinidad'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['min_salinidad'][1],
        'pointBorderColor' => $colors['min_salinidad'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y1'  // Secondary Y-axis for salinidad levels
    ];

    // Create dataset for maximum salinidad levels (line chart on secondary y-axis)
    $max_salinidad_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['max_salinidad_level'] : null;
        $max_salinidad_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Salinidad Máxima (ppt)',
        'data' => $max_salinidad_data,
        'type' => 'line',
        'backgroundColor' => $colors['max_salinidad'][0],
        'borderColor' => $colors['max_salinidad'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['max_salinidad'][1],
        'pointBorderColor' => $colors['max_salinidad'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y1'  // Secondary Y-axis for salinidad levels
    ];

    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'expenses_and_salinidad' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['expenses_and_salinidad'] = [
                'total_expenses' => '$' . number_format($data['total_expenses'], 2),
                'avg_expense_per_treatment' => '$' . number_format($data['avg_expense_per_treatment'], 2),
                'treatment_count' => $data['treatment_count'],
                'min_salinidad_level' => $data['min_salinidad_level'] . ' ppt',
                'max_salinidad_level' => $data['max_salinidad_level'] . ' ppt',
                'avg_salinidad_antes' => $data['avg_salinidad_antes'] . ' ppt',
                'avg_salinidad_despues' => $data['avg_salinidad_despues'] . ' ppt'
            ];
        } else {
            $month_tooltip['expenses_and_salinidad'] = [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'min_salinidad_level' => 'N/A',
                'max_salinidad_level' => 'N/A',
                'avg_salinidad_antes' => 'N/A',
                'avg_salinidad_despues' => 'N/A'
            ];
        }
        
        $tooltipData[] = $month_tooltip;
    }

    // If no data found, provide empty arrays
    if (empty($labels)) {
        $labels = ['Sin Datos'];
        $datasets = [
            [
                'label' => 'Gastos Mensuales ($)',
                'data' => [0],
                'type' => 'bar',
                'backgroundColor' => 'rgba(210, 180, 140, 0.8)',
                'borderColor' => '#d2b48c',
                'borderWidth' => 1,
                'yAxisID' => 'y'
            ],
            [
                'label' => 'Salinidad Mínima (ppt)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(169, 169, 169, 0.8)',
                'borderColor' => '#a9a9a9',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
                'yAxisID' => 'y1'
            ],
            [
                'label' => 'Salinidad Máxima (ppt)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(128, 128, 128, 0.8)',
                'borderColor' => '#808080',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
                'yAxisID' => 'y1'
            ]
        ];
        $tooltipData = [[
            'month' => 'Sin Datos',
            'expenses_and_salinidad' => [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'min_salinidad_level' => 'N/A',
                'max_salinidad_level' => 'N/A',
                'avg_salinidad_antes' => 'N/A',
                'avg_salinidad_despues' => 'N/A'
            ]
        ]];
    }

    // PDO connection will be closed automatically

    // Return the data in the format expected by Chart.js
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'datasets' => $datasets,
        'tooltipData' => $tooltipData,
        'summary' => [
            'series_count' => 3,
            'months_count' => count($labels),
            'data_type' => 'salinidad_expenses_and_levels',
            'has_dual_axis' => true
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_salinidad_gastos_data.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'labels' => ['Error'],
        'datasets' => [
            [
                'label' => 'Gastos Mensuales ($)',
                'data' => [0],
                'type' => 'bar',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1,
                'yAxisID' => 'y'
            ],
            [
                'label' => 'Salinidad Mínima (ppt)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1,
                'yAxisID' => 'y1'
            ],
            [
                'label' => 'Salinidad Máxima (ppt)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(128, 128, 128, 0.2)',
                'borderColor' => 'rgba(128, 128, 128, 1)',
                'borderWidth' => 1,
                'yAxisID' => 'y1'
            ]
        ],
        'tooltipData' => [[
            'month' => 'Error',
            'expenses_and_salinidad' => [
                'total_expenses' => 'Error',
                'avg_expense_per_treatment' => 'Error',
                'treatment_count' => 0,
                'min_salinidad_level' => 'Error',
                'max_salinidad_level' => 'Error',
                'avg_salinidad_antes' => 'Error',
                'avg_salinidad_despues' => 'Error'
            ]
        ]]
    ]);
}
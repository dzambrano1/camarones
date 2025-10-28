<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    // Database connection is already established in pdo_conexion.php as $conn (PDO)
    // No need to create a new connection

    // Get monthly nitritos expenses and nitrite levels
    $sql = "SELECT 
                DATE_FORMAT(timestamp, '%Y-%m') as month,
                SUM(product_qty * product_price) as total_expenses,
                AVG(product_qty * product_price) as avg_expense_per_treatment,
                COUNT(*) as treatment_count,
                AVG(nitrite_mg_l) as avg_nitrite_mg_l,
                MIN(nitrite_mg_l) as min_nitrite_mg_l,
                MAX(nitrite_mg_l) as max_nitrite_mg_l
            FROM water_nitrite_log
            WHERE timestamp IS NOT NULL 
                AND product_qty IS NOT NULL 
                AND product_price IS NOT NULL
                AND nitrite_mg_l IS NOT NULL
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

            // Store expense and nitritos data for this month
            $months_data[$monthName] = [
                'total_expenses' => round((float)$row['total_expenses'], 2),
                'avg_expense_per_treatment' => round((float)$row['avg_expense_per_treatment'], 2),
                'treatment_count' => (int)$row['treatment_count'],
                'avg_nitrite_mg_l' => round((float)$row['avg_nitrite_mg_l'], 2)
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
        'nitrite_mg_l' => ['rgba(34, 139, 34, 0.8)', '#228B22']  // Crimson for nitritos levels (line)
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

    // Create dataset for nitrite levels (line chart on secondary y-axis)
    $nitrite_mg_l_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_nitrite_mg_l'] : null;
        $nitrite_mg_l_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Niveles de Nitritos (mg/L)',
        'data' => $nitrite_mg_l_data,
        'type' => 'line',
        'backgroundColor' => $colors['nitrite_mg_l'][0],
        'borderColor' => $colors['nitrite_mg_l'][1],
        'borderWidth' => 3,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['nitrite_mg_l'][1],
        'pointBorderColor' => '#ffffff',
        'pointBorderWidth' => 2,
        'pointRadius' => 5,
        'pointHoverRadius' => 7,
        'yAxisID' => 'y1'  // Secondary Y-axis for nitrite levels
    ];

    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'expenses_and_nitritos' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['expenses_and_nitritos'] = [
                'total_expenses' => '$' . number_format($data['total_expenses'], 2),
                'avg_expense_per_treatment' => '$' . number_format($data['avg_expense_per_treatment'], 2),
                'treatment_count' => $data['treatment_count'],
                'avg_nitrite_mg_l' => $data['avg_nitrite_mg_l'] . ' mg/L'
            ];
        } else {
            $month_tooltip['expenses_and_nitritos'] = [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'avg_nitrite_mg_l' => 'N/A'
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
                'label' => 'Niveles de Nitritos (mg/L)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(169, 169, 169, 0.8)',
                'borderColor' => '#a9a9a9',
                'borderWidth' => 3,
                'fill' => false,
                'tension' => 0.4,
                'pointRadius' => 5,
                'yAxisID' => 'y1'
            ]
        ];
        $tooltipData = [[
            'month' => 'Sin Datos',
            'expenses_and_nitritos' => [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'avg_nitrite_mg_l' => 'N/A'
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
            'series_count' => 2,
            'months_count' => count($labels),
            'data_type' => 'nitritos_expenses_and_levels',
            'has_dual_axis' => true
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_nitritos_gastos_data.php: " . $e->getMessage());
    
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
                'label' => 'Niveles de Nitritos (mg/L)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 3,
                'pointRadius' => 5,
                'yAxisID' => 'y1'
            ]
        ],
        'tooltipData' => [[
            'month' => 'Error',
            'expenses_and_nitritos' => [
                'total_expenses' => 'Error',
                'avg_expense_per_treatment' => 'Error',
                'treatment_count' => 0,
                'avg_nitrite_mg_l' => 'Error'
            ]
        ]]
    ]);
}
<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    // Database connection is already established in pdo_conexion.php as $conn (PDO)
    // No need to create a new connection

    // Get monthly amoniaco expenses and min/max amoniaco levels
    $sql = "SELECT 
                DATE_FORMAT(fecha_gasto, '%Y-%m') as month,
                SUM(cantidad_utilizada * costo_unitario) as total_expenses,
                AVG(cantidad_utilizada * costo_unitario) as avg_expense_per_treatment,
                COUNT(*) as treatment_count,
                MIN(LEAST(amoniaco_antes, amoniaco_despues)) as min_amoniaco_level,
                MAX(GREATEST(amoniaco_antes, amoniaco_despues)) as max_amoniaco_level,
                AVG(amoniaco_antes) as avg_amoniaco_antes,
                AVG(amoniaco_despues) as avg_amoniaco_despues
            FROM gastos_amoniaco_estanques
            WHERE fecha_gasto IS NOT NULL 
                AND cantidad_utilizada IS NOT NULL 
                AND costo_unitario IS NOT NULL
                AND amoniaco_antes IS NOT NULL 
                AND amoniaco_despues IS NOT NULL
            GROUP BY DATE_FORMAT(fecha_gasto, '%Y-%m')
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

            // Store expense and amoniaco data for this month
            $months_data[$monthName] = [
                'total_expenses' => round((float)$row['total_expenses'], 2),
                'avg_expense_per_treatment' => round((float)$row['avg_expense_per_treatment'], 2),
                'treatment_count' => (int)$row['treatment_count'],
                'min_amoniaco_level' => round((float)$row['min_amoniaco_level'], 2),
                'max_amoniaco_level' => round((float)$row['max_amoniaco_level'], 2),
                'avg_amoniaco_antes' => round((float)$row['avg_amoniaco_antes'], 2),
                'avg_amoniaco_despues' => round((float)$row['avg_amoniaco_despues'], 2)
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
        'amoniaco_antes' => ['rgba(34, 139, 34, 0.8)', '#228B22'], // Green for amoniaco antes (line)
        'amoniaco_despues' => ['rgba(220, 20, 60, 0.8)', '#DC143C']  // Crimson for amoniaco despues (line)
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

    // Create dataset for amoniaco antes levels (line chart on secondary y-axis)
    $amoniaco_antes_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_amoniaco_antes'] : null;
        $amoniaco_antes_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Amoniaco Antes (mg/L)',
        'data' => $amoniaco_antes_data,
        'type' => 'line',
        'backgroundColor' => $colors['amoniaco_antes'][0],
        'borderColor' => $colors['amoniaco_antes'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['amoniaco_antes'][1],
        'pointBorderColor' => $colors['amoniaco_antes'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y1'  // Secondary Y-axis for amoniaco levels
    ];

    // Create dataset for amoniaco despues levels (line chart on secondary y-axis)
    $amoniaco_despues_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_amoniaco_despues'] : null;
        $amoniaco_despues_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Amoniaco Después (mg/L)',
        'data' => $amoniaco_despues_data,
        'type' => 'line',
        'backgroundColor' => $colors['amoniaco_despues'][0],
        'borderColor' => $colors['amoniaco_despues'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['amoniaco_despues'][1],
        'pointBorderColor' => $colors['amoniaco_despues'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y1'  // Secondary Y-axis for amoniaco levels
    ];

    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'expenses_and_amoniaco' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['expenses_and_amoniaco'] = [
                'total_expenses' => '$' . number_format($data['total_expenses'], 2),
                'avg_expense_per_treatment' => '$' . number_format($data['avg_expense_per_treatment'], 2),
                'treatment_count' => $data['treatment_count'],
                'min_amoniaco_level' => $data['min_amoniaco_level'] . ' mg/L',
                'max_amoniaco_level' => $data['max_amoniaco_level'] . ' mg/L',
                'avg_amoniaco_antes' => $data['avg_amoniaco_antes'] . ' mg/L',
                'avg_amoniaco_despues' => $data['avg_amoniaco_despues'] . ' mg/L'
            ];
        } else {
            $month_tooltip['expenses_and_amoniaco'] = [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'min_amoniaco_level' => 'N/A',
                'max_amoniaco_level' => 'N/A',
                'avg_amoniaco_antes' => 'N/A',
                'avg_amoniaco_despues' => 'N/A'
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
                'label' => 'Amoniaco Antes (mg/L)',
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
                'label' => 'Amoniaco Después (mg/L)',
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
            'expenses_and_amoniaco' => [
                'total_expenses' => '$0.00',
                'avg_expense_per_treatment' => '$0.00',
                'treatment_count' => 0,
                'min_amoniaco_level' => 'N/A',
                'max_amoniaco_level' => 'N/A',
                'avg_amoniaco_antes' => 'N/A',
                'avg_amoniaco_despues' => 'N/A'
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
            'data_type' => 'amoniaco_expenses_and_levels',
            'has_dual_axis' => true
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_amoniaco_gastos_data.php: " . $e->getMessage());
    
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
                'label' => 'Amoniaco Antes (mg/L)',
                'data' => [0],
                'type' => 'line',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1,
                'yAxisID' => 'y1'
            ],
            [
                'label' => 'Amoniaco Después (mg/L)',
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
            'expenses_and_amoniaco' => [
                'total_expenses' => 'Error',
                'avg_expense_per_treatment' => 'Error',
                'treatment_count' => 0,
                'min_amoniaco_level' => 'Error',
                'max_amoniaco_level' => 'Error',
                'avg_amoniaco_antes' => 'Error',
                'avg_amoniaco_despues' => 'Error'
            ]
        ]]
    ]);
}
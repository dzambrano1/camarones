<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    // Database connection is already established in pdo_conexion.php as $conn (PDO)
    // No need to create a new connection

    // Get monthly average temperature data
    $sql = "SELECT 
                DATE_FORMAT(cah_temperatura_fecha_hora, '%Y-%m') as month,
                AVG(cah_temperatura_superficial) as avg_temp_superficial,
                AVG(cah_temperatura_fondo) as avg_temp_fondo,
                COUNT(*) as record_count,
                MIN(cah_temperatura_superficial) as min_temp_superficial,
                MAX(cah_temperatura_superficial) as max_temp_superficial,
                MIN(cah_temperatura_fondo) as min_temp_fondo,
                MAX(cah_temperatura_fondo) as max_temp_fondo
            FROM cah_temperatura
            WHERE cah_temperatura_fecha_hora IS NOT NULL 
                AND cah_temperatura_superficial IS NOT NULL 
                AND cah_temperatura_fondo IS NOT NULL
            GROUP BY DATE_FORMAT(cah_temperatura_fecha_hora, '%Y-%m')
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

            // Store temperature data for this month
            $months_data[$monthName] = [
                'avg_temp_superficial' => round((float)$row['avg_temp_superficial'], 2),
                'avg_temp_fondo' => round((float)$row['avg_temp_fondo'], 2),
                'record_count' => (int)$row['record_count'],
                'min_temp_superficial' => round((float)$row['min_temp_superficial'], 2),
                'max_temp_superficial' => round((float)$row['max_temp_superficial'], 2),
                'min_temp_fondo' => round((float)$row['min_temp_fondo'], 2),
                'max_temp_fondo' => round((float)$row['max_temp_fondo'], 2)
            ];
            
            $all_months[] = $monthName;
    }

    // Remove duplicates and sort months
    $all_months = array_unique($all_months);
    
    // Prepare datasets
    $labels = $all_months;
    $datasets = [];
    
    // Colors for temperature series - distinct and vibrant
    $colors = [
        'superficial' => ['rgba(255, 99, 132, 0.8)', '#FF6384'],   // Pink/Red for surface temperature
        'fondo' => ['rgba(54, 162, 235, 0.8)', '#36A2EB']         // Blue for bottom temperature
    ];

    // Create dataset for superficial temperature
    $temp_superficial_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_temp_superficial'] : null;
        $temp_superficial_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Temperatura Superficial (°C)',
        'data' => $temp_superficial_data,
        'backgroundColor' => $colors['superficial'][0],
        'borderColor' => $colors['superficial'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['superficial'][1],
        'pointBorderColor' => $colors['superficial'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y'  // Primary Y-axis
    ];

    // Create dataset for fondo temperature
    $temp_fondo_data = [];
    foreach ($all_months as $month) {
        $value = isset($months_data[$month]) ? 
                 $months_data[$month]['avg_temp_fondo'] : null;
        $temp_fondo_data[] = $value;
    }
    
    $datasets[] = [
        'label' => 'Temperatura Fondo (°C)',
        'data' => $temp_fondo_data,
        'backgroundColor' => $colors['fondo'][0],
        'borderColor' => $colors['fondo'][1],
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.4,
        'pointBackgroundColor' => $colors['fondo'][1],
        'pointBorderColor' => $colors['fondo'][1],
        'pointRadius' => 4,
        'yAxisID' => 'y'  // Primary Y-axis
    ];

    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'temperatures' => []
        ];
        
        if (isset($months_data[$month])) {
            $data = $months_data[$month];
            $month_tooltip['temperatures'] = [
                'avg_temp_superficial' => $data['avg_temp_superficial'] . '°C',
                'avg_temp_fondo' => $data['avg_temp_fondo'] . '°C',
                'record_count' => $data['record_count'],
                'temp_range_superficial' => $data['min_temp_superficial'] . '°C - ' . $data['max_temp_superficial'] . '°C',
                'temp_range_fondo' => $data['min_temp_fondo'] . '°C - ' . $data['max_temp_fondo'] . '°C'
            ];
        } else {
            $month_tooltip['temperatures'] = [
                'avg_temp_superficial' => 'N/A',
                'avg_temp_fondo' => 'N/A',
                'record_count' => 0,
                'temp_range_superficial' => 'N/A',
                'temp_range_fondo' => 'N/A'
            ];
        }
        
        $tooltipData[] = $month_tooltip;
    }

    // If no data found, provide empty arrays
    if (empty($labels)) {
        $labels = ['Sin Datos'];
        $datasets = [
            [
                'label' => 'Temperatura Superficial (°C)',
                'data' => [0],
                'backgroundColor' => 'rgba(210, 180, 140, 0.8)',
                'borderColor' => '#d2b48c',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4
            ],
            [
                'label' => 'Temperatura Fondo (°C)',
                'data' => [0],
                'backgroundColor' => 'rgba(169, 169, 169, 0.8)',
                'borderColor' => '#a9a9a9',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4
            ]
        ];
        $tooltipData = [[
            'month' => 'Sin Datos',
            'temperatures' => [
                'avg_temp_superficial' => 'N/A',
                'avg_temp_fondo' => 'N/A',
                'record_count' => 0,
                'temp_range_superficial' => 'N/A',
                'temp_range_fondo' => 'N/A'
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
            'temperature_series_count' => 2,
            'months_count' => count($labels),
            'data_type' => 'temperature_records'
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_temperatura_registros_data..php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'labels' => ['Error'],
        'datasets' => [
            [
                'label' => 'Temperatura Superficial (°C)',
                'data' => [0],
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Temperatura Fondo (°C)',
                'data' => [0],
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ]
        ],
        'tooltipData' => [[
            'month' => 'Error',
            'temperatures' => [
                'avg_temp_superficial' => 'Error',
                'avg_temp_fondo' => 'Error',
                'record_count' => 0,
                'temp_range_superficial' => 'Error',
                'temp_range_fondo' => 'Error'
            ]
        ]]
    ]);
}
?>

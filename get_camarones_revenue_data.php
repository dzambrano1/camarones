<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get monthly revenue data from cah_ventas
    $sql = "SELECT 
                DATE_FORMAT(cah_ventas_fecha, '%Y-%m') as month,
                TRIM(cah_ventas_presentacion) as presentation,
                SUM(cah_ventas_precio * cah_ventas_peso) as revenue,
                COUNT(*) as sales_count,
                AVG(cah_ventas_cantidad) as avg_quantity,
                AVG(cah_ventas_precio) as avg_price,
                GROUP_CONCAT(DISTINCT TRIM(cah_ventas_talla) ORDER BY cah_ventas_talla SEPARATOR ', ') as sizes,
                SUM(cah_ventas_cantidad) as total_quantity
            FROM cah_ventas 
            WHERE cah_ventas_fecha IS NOT NULL
            GROUP BY DATE_FORMAT(cah_ventas_fecha, '%Y-%m'), TRIM(cah_ventas_presentacion)
            ORDER BY month ASC, presentation ASC";

    $result = $conn->query($sql);

    
    $exchange_rate = 1;

    // Initialize data structures
    $months_data = [];
    $all_months = [];
    $presentations = ['Entero', 'Sin cabeza', 'Pelado y desvenado'];
    
    // Map database values to clean presentation names
    $presentation_map = [
        'Entero' => 'Entero',
        'Sin cabeza' => 'Sin cabeza', 
        'Pelado y desvenado' => 'Pelado y desvenado'
    ];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $monthYear = $row['month'];
            $presentation = $row['presentation'];
            
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

            // Convert COP to USD
            $revenue_usd = (float)$row['revenue'] / $exchange_rate;
            $avg_price_usd = (float)$row['avg_price'] / $exchange_rate;

            // Map the presentation name to clean version
            $clean_presentation = isset($presentation_map[$presentation]) ? $presentation_map[$presentation] : $presentation;

            // Store data by month and presentation
            if (!isset($months_data[$monthName])) {
                $months_data[$monthName] = [];
                $all_months[] = $monthName;
            }
            
            $months_data[$monthName][$clean_presentation] = [
                'revenue' => $revenue_usd,
                'sales_count' => (int)$row['sales_count'],
                'total_quantity' => (float)$row['total_quantity'],
                'avg_quantity' => (float)$row['avg_quantity'],
                'avg_price' => $avg_price_usd,
                'sizes' => $row['sizes'] ?: 'N/A'
            ];
        }
    }

    // Remove duplicates and sort months
    $all_months = array_unique($all_months);
    
    // Prepare datasets
    $labels = $all_months;
    $datasets = [];
    
    // Colors for each presentation type - distinct and vibrant
    $colors = [
        'Entero' => ['rgba(34, 139, 34, 0.8)', '#228B22'],        // Forest Green
        'Sin cabeza' => ['rgba(255, 140, 0, 0.8)', '#FF8C00'],    // Dark Orange
        'Pelado y desvenado' => ['rgba(220, 20, 60, 0.8)', '#DC143C'], // Crimson Red
        'Total' => ['rgba(72, 61, 139, 0.9)', '#483D8B']          // Dark Slate Blue
    ];

    // Create datasets for each presentation type
    foreach ($presentations as $presentation) {
        $presentation_data = [];
        foreach ($all_months as $month) {
            $value = isset($months_data[$month][$presentation]) ? 
                     round($months_data[$month][$presentation]['revenue'], 2) : 0;
            $presentation_data[] = $value;
        }
        
        $datasets[] = [
            'label' => $presentation . ' (USD)',
            'data' => $presentation_data,
            'backgroundColor' => $colors[$presentation][0],
            'borderColor' => $colors[$presentation][1],
            'borderWidth' => 2,
            'fill' => false,
            'tension' => 0.4,
            'yAxisID' => 'y'  // Primary Y-axis
        ];
    }

    // Create total dataset (sum of all presentations)
    $total_data = [];
    foreach ($all_months as $month) {
        $total = 0;
        foreach ($presentations as $presentation) {
            if (isset($months_data[$month][$presentation])) {
                $total += $months_data[$month][$presentation]['revenue'];
            }
        }
        $total_data[] = round($total, 2);
    }
    
    $datasets[] = [
        'label' => 'Total (USD)',
        'data' => $total_data,
        'backgroundColor' => $colors['Total'][0],
        'borderColor' => $colors['Total'][1],
        'borderWidth' => 3,
        'fill' => false,
        'tension' => 0.4,
        'yAxisID' => 'y1'  // Secondary Y-axis
    ];

    // Prepare tooltip data
    $tooltipData = [];
    foreach ($all_months as $index => $month) {
        $month_tooltip = [
            'month' => $month,
            'presentations' => []
        ];
        
        $total_revenue = 0;
        $total_sales = 0;
        $total_quantity = 0;
        
        foreach ($presentations as $presentation) {
            if (isset($months_data[$month][$presentation])) {
                $data = $months_data[$month][$presentation];
                $month_tooltip['presentations'][$presentation] = [
                    'revenue' => number_format($data['revenue'], 2),
                    'sales_count' => $data['sales_count'],
                    'total_quantity' => number_format($data['total_quantity'], 0),
                    'avg_price' => number_format($data['avg_price'], 2),
                    'sizes' => $data['sizes']
                ];
                
                $total_revenue += $data['revenue'];
                $total_sales += $data['sales_count'];
                $total_quantity += $data['total_quantity'];
            }
        }
        
        $month_tooltip['total'] = [
            'revenue' => number_format($total_revenue, 2),
            'sales_count' => $total_sales,
            'total_quantity' => number_format($total_quantity, 0)
        ];
        
        $tooltipData[] = $month_tooltip;
    }

    // If no data found, provide empty arrays
    if (empty($labels)) {
        $labels = ['Sin Datos'];
        $datasets = [[
            'label' => 'Sin Datos',
            'data' => [0],
            'backgroundColor' => 'rgba(210, 180, 140, 0.8)',
            'borderColor' => '#d2b48c',
            'borderWidth' => 2,
            'fill' => false,
            'tension' => 0.4
        ]];
        $tooltipData = [[
            'month' => 'Sin Datos',
            'presentations' => [],
            'total' => [
                'revenue' => '0.00',
                'sales_count' => 0,
                'total_quantity' => '0'
            ]
        ]];
    }

    $conn->close();

    // Return the data in the format expected by Chart.js
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'datasets' => $datasets,
        'tooltipData' => $tooltipData,
        'summary' => [
            'presentations_count' => count($presentations),
            'months_count' => count($labels)
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_camarones_revenue_data.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'labels' => ['Error'],
        'datasets' => [[
            'label' => 'Error',
            'data' => [0],
            'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
            'borderColor' => 'rgba(255, 99, 132, 1)',
            'borderWidth' => 1
        ]],
        'tooltipData' => [[
            'month' => 'Error',
            'revenue' => '0.00',
            'total_sales' => 0,
            'total_quantity' => '0',
            'avg_quantity' => 0,
            'avg_price' => 0,
            'sizes' => 'Error',
            'presentations' => 'Error'
        ]]
    ]);
}
?>

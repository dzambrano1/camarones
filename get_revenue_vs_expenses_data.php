<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable error reporting in PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get revenue data from ah_peso table - ONLY most recent record per animal (final commercial weight/price)
    $pesoRevenueQuery = "SELECT 
                            DATE_FORMAT(most_recent.ah_peso_fecha, '%Y-%m') AS month,
                            SUM(most_recent.ah_peso_animal * most_recent.ah_peso_precio) AS peso_revenue
                         FROM (
                            SELECT 
                                ah_peso_tagid,
                                ah_peso_fecha,
                                ah_peso_animal,
                                ah_peso_precio,
                                ROW_NUMBER() OVER (PARTITION BY ah_peso_tagid ORDER BY ah_peso_fecha DESC) as rn
                            FROM ah_peso
                            WHERE 
                                ah_peso_fecha IS NOT NULL AND 
                                ah_peso_fecha != '0000-00-00' AND
                                ah_peso_animal IS NOT NULL AND
                                ah_peso_precio IS NOT NULL AND
                                ah_peso_animal > 0 AND
                                ah_peso_precio > 0
                         ) most_recent
                         WHERE most_recent.rn = 1
                         GROUP BY DATE_FORMAT(most_recent.ah_peso_fecha, '%Y-%m')";

    // Get egg revenue data from ah_huevo table (ah_huevo_cantidad * ah_huevo_precio)
    $huevoRevenueQuery = "SELECT 
                             DATE_FORMAT(ah_huevo_fecha, '%Y-%m') AS month,
                             SUM(CASE 
                                 WHEN ah_huevo_cantidad IS NOT NULL AND ah_huevo_precio IS NOT NULL 
                                 THEN ah_huevo_cantidad * ah_huevo_precio
                                 ELSE 0 
                             END) AS huevo_revenue
                          FROM ah_huevo
                          WHERE 
                             ah_huevo_fecha IS NOT NULL AND 
                             ah_huevo_fecha != '0000-00-00' AND
                             ah_huevo_cantidad IS NOT NULL AND
                             ah_huevo_precio IS NOT NULL
                          GROUP BY DATE_FORMAT(ah_huevo_fecha, '%Y-%m')";
    
    // Get feed expenses using simple daily cost calculation (racion * costo for each specific date)
    $concentradoQuery = "SELECT 
                            DATE_FORMAT(ah_concentrado_fecha, '%Y-%m') AS month,
                            SUM(ah_concentrado_racion * ah_concentrado_costo) AS concentrado_expense
                         FROM ah_concentrado
                         WHERE 
                            ah_concentrado_fecha IS NOT NULL AND 
                            ah_concentrado_fecha != '0000-00-00' AND
                            ah_concentrado_racion IS NOT NULL AND
                            ah_concentrado_costo IS NOT NULL AND
                            ah_concentrado_racion > 0 AND
                            ah_concentrado_costo > 0
                         GROUP BY DATE_FORMAT(ah_concentrado_fecha, '%Y-%m')";
    
    $melazaQuery = "SELECT 
                       DATE_FORMAT(ah_melaza_fecha, '%Y-%m') AS month,
                       SUM(ah_melaza_racion * ah_melaza_costo) AS melaza_expense
                    FROM ah_melaza
                    WHERE 
                       ah_melaza_fecha IS NOT NULL AND 
                       ah_melaza_fecha != '0000-00-00' AND
                       ah_melaza_racion IS NOT NULL AND
                       ah_melaza_costo IS NOT NULL AND
                       ah_melaza_racion > 0 AND
                       ah_melaza_costo > 0
                    GROUP BY DATE_FORMAT(ah_melaza_fecha, '%Y-%m')";
    
    $salQuery = "SELECT 
                    DATE_FORMAT(ah_sal_fecha, '%Y-%m') AS month,
                    SUM(ah_sal_racion * ah_sal_costo) AS sal_expense
                 FROM ah_sal
                 WHERE 
                    ah_sal_fecha IS NOT NULL AND 
                    ah_sal_fecha != '0000-00-00' AND
                    ah_sal_racion IS NOT NULL AND
                    ah_sal_costo IS NOT NULL AND
                    ah_sal_racion > 0 AND
                    ah_sal_costo > 0
                 GROUP BY DATE_FORMAT(ah_sal_fecha, '%Y-%m')";
    
    // Get vaccine expenses from all vaccine tables
    $vaccineQueries = [
        "colera" => "SELECT DATE_FORMAT(ah_colera_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_colera_costo, 0)) AS expense FROM ah_colera WHERE ah_colera_fecha IS NOT NULL AND ah_colera_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_colera_fecha, '%Y-%m')",
        "coriza" => "SELECT DATE_FORMAT(ah_coriza_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_coriza_costo, 0)) AS expense FROM ah_coriza WHERE ah_coriza_fecha IS NOT NULL AND ah_coriza_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_coriza_fecha, '%Y-%m')",
        "newcastle" => "SELECT DATE_FORMAT(ah_newcastle_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_newcastle_costo, 0)) AS expense FROM ah_newcastle WHERE ah_newcastle_fecha IS NOT NULL AND ah_newcastle_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_newcastle_fecha, '%Y-%m')",
        "marek" => "SELECT DATE_FORMAT(ah_marek_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_marek_costo, 0)) AS expense FROM ah_marek WHERE ah_marek_fecha IS NOT NULL AND ah_marek_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_marek_fecha, '%Y-%m')",
        "influenza" => "SELECT DATE_FORMAT(ah_influenza_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_influenza_costo, 0)) AS expense FROM ah_influenza WHERE ah_influenza_fecha IS NOT NULL AND ah_influenza_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_influenza_fecha, '%Y-%m')",
        "encefalomielitis" => "SELECT DATE_FORMAT(ah_encefalomielitis_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_encefalomielitis_costo, 0)) AS expense FROM ah_encefalomielitis WHERE ah_encefalomielitis_fecha IS NOT NULL AND ah_encefalomielitis_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_encefalomielitis_fecha, '%Y-%m')",
        "corona_virus" => "SELECT DATE_FORMAT(ah_corona_virus_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_corona_virus_costo, 0)) AS expense FROM ah_corona_virus WHERE ah_corona_virus_fecha IS NOT NULL AND ah_corona_virus_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_corona_virus_fecha, '%Y-%m')",
        "viruela" => "SELECT DATE_FORMAT(ah_viruela_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_viruela_costo, 0)) AS expense FROM ah_viruela WHERE ah_viruela_fecha IS NOT NULL AND ah_viruela_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_viruela_fecha, '%Y-%m')",
        "garrapatas" => "SELECT DATE_FORMAT(ah_garrapatas_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_garrapatas_costo, 0)) AS expense FROM ah_garrapatas WHERE ah_garrapatas_fecha IS NOT NULL AND ah_garrapatas_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_garrapatas_fecha, '%Y-%m')",
        "parasitos" => "SELECT DATE_FORMAT(ah_parasitos_fecha, '%Y-%m') AS month, SUM(COALESCE(ah_parasitos_costo, 0)) AS expense FROM ah_parasitos WHERE ah_parasitos_fecha IS NOT NULL AND ah_parasitos_costo IS NOT NULL GROUP BY DATE_FORMAT(ah_parasitos_fecha, '%Y-%m')"
    ];
    
    // Execute all queries and collect data
    $monthlyData = [];
    
    // Get peso revenue data
    $pesoRevenueStmt = $conn->prepare($pesoRevenueQuery);
    $pesoRevenueStmt->execute();
    $pesoRevenueResults = $pesoRevenueStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($pesoRevenueResults as $row) {
        $month = $row['month'];
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = ['revenue' => 0, 'expenses' => 0, 'peso_revenue' => 0, 'huevo_revenue' => 0];
        }
        $pesoRevenue = (float)$row['peso_revenue'];
        $monthlyData[$month]['revenue'] += $pesoRevenue;
        $monthlyData[$month]['peso_revenue'] += $pesoRevenue;
    }
    
    // Get egg revenue data
    $huevoRevenueStmt = $conn->prepare($huevoRevenueQuery);
    $huevoRevenueStmt->execute();
    $huevoRevenueResults = $huevoRevenueStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($huevoRevenueResults as $row) {
        $month = $row['month'];
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = ['revenue' => 0, 'expenses' => 0, 'peso_revenue' => 0, 'huevo_revenue' => 0];
        }
        $huevoRevenue = (float)$row['huevo_revenue'];
        $monthlyData[$month]['revenue'] += $huevoRevenue;
        $monthlyData[$month]['huevo_revenue'] += $huevoRevenue;
    }
    
    // Process feed expenses using direct SQL queries
    $feedQueries = [
        'concentrado' => $concentradoQuery,
        'melaza' => $melazaQuery,
        'sal' => $salQuery
    ];
    
    foreach ($feedQueries as $type => $query) {
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $month = $row['month'];
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['revenue' => 0, 'expenses' => 0, 'peso_revenue' => 0, 'huevo_revenue' => 0];
            }
            $expenseKey = $type . '_expense';
            $monthlyData[$month]['expenses'] += (float)$row[$expenseKey];
        }
    }
    
    // Get vaccine expenses
    foreach ($vaccineQueries as $type => $query) {
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $month = $row['month'];
                if (!isset($monthlyData[$month])) {
                    $monthlyData[$month] = ['revenue' => 0, 'expenses' => 0, 'peso_revenue' => 0, 'huevo_revenue' => 0];
                }
                $monthlyData[$month]['expenses'] += (float)$row['expense'];
            }
        } catch (PDOException $e) {
            // Log table not found errors but continue processing
            error_log("Table ah_$type might not exist: " . $e->getMessage());
        }
    }
    
    // Format final data
    $finalData = [];
    foreach ($monthlyData as $month => $data) {
        $finalData[] = [
            'month' => $month,
            'total_revenue' => $data['revenue'],
            'huevo_revenue' => $data['huevo_revenue'],
            'peso_revenue' => $data['peso_revenue'],
            'total_expenses' => $data['expenses'],
            'net_profit' => $data['revenue'] - $data['expenses']
        ];
    }
    
         // Sort by month ascending
     usort($finalData, function($a, $b) {
         return strcmp($a['month'], $b['month']);
     });
    
    // Output the data as JSON
    echo json_encode($finalData);
    
} catch (PDOException $e) {
    // Return error message
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    // Log the error
    error_log('Error in get_revenue_vs_expenses_data.php: ' . $e->getMessage());
}
?>
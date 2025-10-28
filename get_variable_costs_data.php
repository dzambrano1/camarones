<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable error reporting in PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get total concentrado expenses using new date columns
    $concentradoQuery = "SELECT SUM(
                           DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) *
                           cah_concentrado_racion *
                           cah_concentrado_costo
                       ) AS total_cost
                       FROM cah_concentrado
                       WHERE
                         cah_concentrado_fecha_inicio IS NOT NULL AND
                         cah_concentrado_fecha_inicio != '0000-00-00' AND
                         cah_concentrado_fecha_fin IS NOT NULL AND
                         cah_concentrado_fecha_fin != '0000-00-00' AND
                         cah_concentrado_racion IS NOT NULL AND
                         cah_concentrado_costo IS NOT NULL AND
                         cah_concentrado_racion > 0 AND
                         cah_concentrado_costo > 0 AND
                         cah_concentrado_fecha_fin >= cah_concentrado_fecha_inicio";

    // Get total harinas expenses using new date columns
    $harinasQuery = "SELECT SUM(
                       DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) *
                       cah_harinas_racion *
                       cah_harinas_costo
                   ) AS total_cost
                   FROM cah_harinas
                   WHERE
                     cah_harinas_fecha_inicio IS NOT NULL AND
                     cah_harinas_fecha_inicio != '0000-00-00' AND
                     cah_harinas_fecha_fin IS NOT NULL AND
                     cah_harinas_fecha_fin != '0000-00-00' AND
                     cah_harinas_racion IS NOT NULL AND
                     cah_harinas_costo IS NOT NULL AND
                     cah_harinas_racion > 0 AND
                     cah_harinas_costo > 0 AND
                     cah_harinas_fecha_fin >= cah_harinas_fecha_inicio";

    // Get total fermentados expenses using new date columns
    $fermentadosQuery = "SELECT SUM(
                   DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) *
                   cah_fermentados_racion *
                   cah_fermentados_costo
               ) AS total_cost
               FROM cah_fermentados
               WHERE
                 cah_fermentados_fecha_inicio IS NOT NULL AND
                 cah_fermentados_fecha_inicio != '0000-00-00' AND
                 cah_fermentados_fecha_fin IS NOT NULL AND
                 cah_fermentados_fecha_fin != '0000-00-00' AND
                 cah_fermentados_racion IS NOT NULL AND
                 cah_fermentados_costo IS NOT NULL AND
                 cah_fermentados_racion > 0 AND
                 cah_fermentados_costo > 0 AND
                 cah_fermentados_fecha_fin >= cah_fermentados_fecha_inicio";

    // WaterTreatment queries - handle tables that might not exist
    $waterTreatmentQueries = [
        "salinidad" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_salinidad_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "ph" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_ph_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "transparencia" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_transparencia_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "redox" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_redox_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "oxigeno" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_oxigeno_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "nitritos" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_nitritos_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "amoniaco" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_amoniaco_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0",
        "alcalinidad" => "SELECT SUM(COALESCE(costo_unitario, 0) * cantidad_utilizada) AS total_cost FROM gastos_alcalinidad_estanques WHERE fecha_gasto IS NOT NULL AND costo_unitario IS NOT NULL AND costo_unitario > 0"
    ];
    
    $costCategories = [];
    $totalVariableCost = 0;

    // Get concentrado costs
    $concentradoStmt = $conn->prepare($concentradoQuery);
    $concentradoStmt->execute();
    $concentradoResult = $concentradoStmt->fetch();
    $concentradoCost = (float)($concentradoResult['total_cost'] ?? 0);
    if ($concentradoCost > 0) {
        $costCategories['concentrado'] = $concentradoCost;
        $totalVariableCost += $concentradoCost;
    }

    // Get harinas costs
    $harinasStmt = $conn->prepare($harinasQuery);
    $harinasStmt->execute();
    $harinasResult = $harinasStmt->fetch();
    $harinasCost = (float)($harinasResult['total_cost'] ?? 0);
    if ($harinasCost > 0) {
        $costCategories['harinas'] = $harinasCost;
        $totalVariableCost += $harinasCost;
    }

    // Get fermentados costs
    $fermentadosStmt = $conn->prepare($fermentadosQuery);
    $fermentadosStmt->execute();
    $fermentadosResult = $fermentadosStmt->fetch();
    $fermentadosCost = (float)($fermentadosResult['total_cost'] ?? 0);
    if ($fermentadosCost > 0) {
        $costCategories['fermentados'] = $fermentadosCost;
        $totalVariableCost += $fermentadosCost;
    }

    // Get waterTreatment costs
    $totalWaterTreatmentCost = 0;
    foreach ($waterTreatmentQueries as $waterTreatmentType => $query) {
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();

            $cost = (float)($result['total_cost'] ?? 0);
            if ($cost > 0) {
                $totalWaterTreatmentCost += $cost;
            }
        } catch (PDOException $e) {
            error_log("Table $waterTreatmentType might not exist: " . $e->getMessage());
        }
    }

    if ($totalWaterTreatmentCost > 0) {
        $costCategories['waterTreatment'] = $totalWaterTreatmentCost;
        $totalVariableCost += $totalWaterTreatmentCost;
    }

    // Create pie chart data
    $pieData = [];
    $categoryNames = [
        'concentrado' => 'Concentrado',
        'harinas' => 'harinas',
        'fermentados' => 'fermentados',
        'waterTreatment' => 'Tratamiento Agua'
    ];

    foreach ($costCategories as $category => $cost) {
        $percentage = $totalVariableCost > 0 ? ($cost / $totalVariableCost) * 100 : 0;
        
        // Use more decimal places for small percentages to avoid showing 0%
        $precisePercentage = $percentage;
        if ($percentage > 0 && $percentage < 0.1) {
            // For very small percentages, show 3 decimal places
            $precisePercentage = round($percentage, 3);
        } elseif ($percentage >= 0.1 && $percentage < 1) {
            // For small percentages, show 2 decimal places
            $precisePercentage = round($percentage, 2);
        } else {
            // For normal percentages, show 1 decimal place
            $precisePercentage = round($percentage, 1);
        }
        
        $pieData[] = [
            'label' => $categoryNames[$category],
            'value' => round($cost, 2),
            'percentage' => $precisePercentage,
            'category_type' => $category
        ];
    }

    // Sort by value descending
    usort($pieData, function($a, $b) {
        return $b['value'] <=> $a['value'];
    });

    $summary = [
        'total_variable_cost' => round($totalVariableCost, 2),
        'category_count' => count($pieData)
    ];

    echo json_encode([
        'data' => $pieData,
        'summary' => $summary
    ]);
    
} catch (PDOException $e) {
    // Return error message
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    // Log the error
    error_log('Error in get_variable_costs_data.php: ' . $e->getMessage());
}
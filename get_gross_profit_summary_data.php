<?php
require_once './pdo_conexion.php';

header('Content-Type: application/json');

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Get total Sales Revenue
    $salesRevenueQuery = "SELECT SUM(cah_ventas_precio * cah_ventas_peso) AS total_sales_revenue
                         FROM cah_ventas
                         WHERE cah_ventas_fecha IS NOT NULL AND cah_ventas_fecha != '0000-00-00' 
                           AND cah_ventas_precio IS NOT NULL AND cah_ventas_peso IS NOT NULL";
    $salesRevenueStmt = $conn->prepare($salesRevenueQuery);
    $salesRevenueStmt->execute();
    $salesRevenueResult = $salesRevenueStmt->fetch();
    $totalSalesRevenue = (float)($salesRevenueResult['total_sales_revenue'] ?? 0);

    // 2. Get total variable costs using same logic as get_variable_costs_data.php
    // Get total concentrado expenses
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
    $concentradoStmt = $conn->prepare($concentradoQuery);
    $concentradoStmt->execute();
    $concentradoResult = $concentradoStmt->fetch();
    $totalConcentradoCost = (float)($concentradoResult['total_cost'] ?? 0);

    // Get total harinas expenses
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
    $harinasStmt = $conn->prepare($harinasQuery);
    $harinasStmt->execute();
    $harinasResult = $harinasStmt->fetch();
    $totalHarinasCost = (float)($harinasResult['total_cost'] ?? 0);

    // Get total fermentados expenses
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
    $fermentadosStmt = $conn->prepare($fermentadosQuery);
    $fermentadosStmt->execute();
    $fermentadosResult = $fermentadosStmt->fetch();
    $totalFermentadosCost = (float)($fermentadosResult['total_cost'] ?? 0);

    // Get total water treatment costs using the gastos tables
    $waterTreatmentQueries = [
        "salinidad" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_salinidad_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "ph" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_ph_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "oxigeno" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_oxigeno_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "nitritos" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_nitritos_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "amoniaco" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_amoniaco_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "alcalinidad" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_alcalinidad_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "transparencia" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_transparencia_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0",
        "redox" => "SELECT SUM(cantidad_utilizada * costo_unitario) AS total_cost FROM gastos_redox_estanques WHERE fecha_gasto IS NOT NULL AND cantidad_utilizada IS NOT NULL AND costo_unitario IS NOT NULL AND cantidad_utilizada > 0 AND costo_unitario > 0"
    ];
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
            error_log("Table gastos_$waterTreatmentType" . "_estanques might not exist: " . $e->getMessage());
        }
    }

    // 3. Calculate total variable costs
    $totalVariableCosts = $totalConcentradoCost + $totalHarinasCost + $totalFermentadosCost + $totalWaterTreatmentCost;

    // 4. Calculate gross profit (using sales revenue as total income)
    $totalIncome = $totalSalesRevenue;
    $grossProfit = $totalIncome - $totalVariableCosts;

    // 5. Calculate percentages for labels
    $expensePercentage = $totalIncome > 0 ? round(($totalVariableCosts / $totalIncome) * 100, 1) : 0;
    $profitPercentage = $totalIncome > 0 ? round(($grossProfit / $totalIncome) * 100, 1) : 0;
    
    // 6. Prepare bar chart data with percentages in labels
    $chartData = [
        [
            'category' => 'Ingresos Totales (100%)',
            'value' => round($totalIncome, 2),
            'type' => 'income',
            'details' => [
                'ventas' => round($totalSalesRevenue, 2)
            ]
        ],
        [
            'category' => "Gastos Variables ({$expensePercentage}%)",
            'value' => round($totalVariableCosts, 2),
            'type' => 'expense',
            'details' => [
                'concentrado' => round($totalConcentradoCost, 2),
                'harinas' => round($totalHarinasCost, 2),
                'fermentados' => round($totalFermentadosCost, 2),
                'waterTreatment' => round($totalWaterTreatmentCost, 2),
                'porcentaje_gastos' => $expensePercentage
            ]
        ],
        [
            'category' => "Ganancia Bruta ({$profitPercentage}%)",
            'value' => round($grossProfit, 2),
            'type' => $grossProfit >= 0 ? 'profit' : 'loss',
            'details' => [
                'margen_porcentaje' => $profitPercentage
            ]
        ]
    ];

    $summary = [
        'total_income' => round($totalIncome, 2),
        'total_expenses' => round($totalVariableCosts, 2),
        'gross_profit' => round($grossProfit, 2),
        'profit_margin_percentage' => $totalIncome > 0 ? round(($grossProfit / $totalIncome) * 100, 1) : 0,
        'expense_ratio' => $totalIncome > 0 ? round(($totalVariableCosts / $totalIncome) * 100, 1) : 0
    ];

    echo json_encode([
        'data' => $chartData,
        'summary' => $summary
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    error_log('Error in get_gross_profit_summary_data.php: ' . $e->getMessage());
}
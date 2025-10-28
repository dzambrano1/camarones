<?php
header('Content-Type: application/json');

// Include database connection
require_once "./pdo_conexion.php";

try {
    // Verify PDO connection
    if (!($conn instanceof PDO)) {
        throw new Exception("Database connection error");
    }
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get tagid filter if provided
    $selected_tagid = isset($_GET['tagid']) ? $_GET['tagid'] : 'all';
    
    // Build WHERE clause for tagid filtering
    $where_clause = '';
    $params = [];
    
    if ($selected_tagid !== 'all') {
        $where_clause = "AND c.cah_concentrado_tagid = :tagid";
        $params[':tagid'] = $selected_tagid;
    }
    
    // Main query to calculate FCR by month
    // FCR = Total Feed Consumed (kg) / Total Biomass Gain (kg)
    $sql = "
        SELECT 
            DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m') AS month,
            YEAR(c.cah_concentrado_fecha_inicio) AS year,
            MONTH(c.cah_concentrado_fecha_inicio) AS month_num,
            
            -- Calculate total feed consumed in the month (kg)
            SUM(
                c.cah_concentrado_racion * 
                DATEDIFF(
                    LEAST(c.cah_concentrado_fecha_fin, LAST_DAY(c.cah_concentrado_fecha_inicio)),
                    GREATEST(c.cah_concentrado_fecha_inicio, DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m-01'))
                ) + 1
            ) AS total_feed_kg,
            
            -- Get biomass data for the same month
            AVG(p.cah_peso_biomasa) AS avg_biomasa_kg,
            SUM(p.cah_peso_biomasa) AS total_biomasa_kg,
            
            -- Calculate FCR metrics
            CASE 
                WHEN SUM(p.cah_peso_biomasa) > 0 
                THEN ROUND(
                    SUM(
                        c.cah_concentrado_racion * 
                        DATEDIFF(
                            LEAST(c.cah_concentrado_fecha_fin, LAST_DAY(c.cah_concentrado_fecha_inicio)),
                            GREATEST(c.cah_concentrado_fecha_inicio, DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m-01'))
                        ) + 1
                    ) / SUM(p.cah_peso_biomasa), 
                    2
                )
                ELSE 0
            END AS overall_fcr,
            
            -- Average FCR per tank
            ROUND(AVG(
                CASE 
                    WHEN p.cah_peso_biomasa > 0 
                    THEN (
                        c.cah_concentrado_racion * 
                        DATEDIFF(
                            LEAST(c.cah_concentrado_fecha_fin, LAST_DAY(c.cah_concentrado_fecha_inicio)),
                            GREATEST(c.cah_concentrado_fecha_inicio, DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m-01'))
                        ) + 1
                    ) / p.cah_peso_biomasa
                    ELSE NULL
                END
            ), 2) AS average_fcr,
            
            -- Additional metrics
            COUNT(DISTINCT c.cah_concentrado_tagid) AS tank_count,
            AVG(p.cah_peso_promedio) AS avg_weight_grams,
            UNIX_TIMESTAMP(MIN(CONCAT(DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m'), '-01'))) AS month_timestamp
            
        FROM cah_concentrado c
        LEFT JOIN cah_peso p ON (
            c.cah_concentrado_tagid = p.cah_peso_tagid 
            AND DATE_FORMAT(c.cah_concentrado_fecha_inicio, '%Y-%m') = DATE_FORMAT(p.cah_peso_fecha, '%Y-%m')
        )
        WHERE c.cah_concentrado_racion > 0 
            AND c.cah_concentrado_tagid IS NOT NULL
            $where_clause
        GROUP BY month, year, month_num
        HAVING total_feed_kg > 0
        ORDER BY year ASC, month_num ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available tagids for dropdown
    $tagid_sql = "
        SELECT DISTINCT c.cah_concentrado_tagid AS tagid, 
               cam.nombre
        FROM cah_concentrado c
        LEFT JOIN camarones cam ON c.cah_concentrado_tagid = cam.tagid
        WHERE c.cah_concentrado_tagid IS NOT NULL 
            AND c.cah_concentrado_racion > 0
        ORDER BY c.cah_concentrado_tagid ASC
    ";
    
    $tagid_stmt = $conn->prepare($tagid_sql);
    $tagid_stmt->execute();
    $tagids = $tagid_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Clean up data - ensure numeric values
    foreach ($monthly_data as &$item) {
        $item['overall_fcr'] = $item['overall_fcr'] ? (string)$item['overall_fcr'] : '0';
        $item['average_fcr'] = $item['average_fcr'] ? (string)$item['average_fcr'] : '0';
        $item['total_feed_kg'] = $item['total_feed_kg'] ? (string)$item['total_feed_kg'] : '0';
        $item['total_biomasa_kg'] = $item['total_biomasa_kg'] ? (string)$item['total_biomasa_kg'] : '0';
        $item['tank_count'] = (string)$item['tank_count'];
        $item['avg_weight_grams'] = $item['avg_weight_grams'] ? number_format($item['avg_weight_grams'], 7) : '0.0000000';
        $item['month_timestamp'] = $item['month_timestamp'] ? number_format($item['month_timestamp'], 6, '.', '') : '0.000000';
    }
    
    // Return response
    echo json_encode([
        'data' => $monthly_data,
        'tagids' => $tagids,
        'selected_tagid' => $selected_tagid
    ]);
    
} catch (Exception $e) {
    error_log("FCR Data Error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Error processing FCR data: ' . $e->getMessage(),
        'data' => [],
        'tagids' => [],
        'selected_tagid' => 'all'
    ]);
}
?>

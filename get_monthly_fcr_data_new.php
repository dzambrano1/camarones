<?php
header('Content-Type: application/json');

// Include database connection details
require_once "./pdo_conexion.php";

$data = [];
$tagids = [];

// Get tagid filter from request (optional)
$selected_tagid = isset($_GET['tagid']) ? $_GET['tagid'] : 'all';

try {
    // First, get all available tagids for the dropdown (from both tables)
    $tagid_sql = "
        SELECT DISTINCT tagid, nombre
        FROM (
            SELECT DISTINCT cah_concentrado_tagid as tagid,
                   (SELECT nombre FROM camarones WHERE tagid = cah_concentrado_tagid LIMIT 1) as nombre
            FROM cah_concentrado 
            WHERE cah_concentrado_tagid IS NOT NULL 
                AND cah_concentrado_racion > 0
            UNION
            SELECT DISTINCT cah_peso_tagid as tagid,
                   (SELECT nombre FROM camarones WHERE tagid = cah_peso_tagid LIMIT 1) as nombre
            FROM cah_peso 
            WHERE cah_peso_tagid IS NOT NULL 
                AND cah_peso_promedio > 0
        ) combined_tagids
        WHERE nombre IS NOT NULL
        ORDER BY tagid ASC
    ";
    
    $tagid_stmt = $conn->prepare($tagid_sql);
    $tagid_stmt->execute();
    $tagid_results = $tagid_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tagid_results as $row) {
        $tagids[] = [
            'tagid' => $row['tagid'],
            'nombre' => $row['nombre'] ?? 'Sin nombre'
        ];
    }

    // Query to calculate monthly FCR values  
    // FCR = Total feed given (kg) / Total shrimp weight gain (kg)
    $where_clause = "";
    $params = [];
    if ($selected_tagid !== 'all') {
        $where_clause = "WHERE fcr_monthly.tagid = :tagid";
        $params[':tagid'] = $selected_tagid;
    }
    
    $sql = "
        SELECT 
            month,
            year,
            month_num,
            total_feed_kg,
            total_biomass_gain_kg,
            initial_biomass_kg,
            final_biomass_kg,
            
            -- FCR calculation: Total feed given / Total biomass gain
            CASE 
                WHEN total_biomass_gain_kg > 0 
                THEN ROUND(total_feed_kg / total_biomass_gain_kg, 3)
                ELSE NULL
            END AS fcr_value,
            
            tank_count,
            concentrado_records,
            peso_records,
            month_timestamp
            
        FROM (
            SELECT 
                fcr_monthly.month,
                fcr_monthly.year,
                fcr_monthly.month_num,
                
                -- Total feed given in the month (kg)
                SUM(fcr_monthly.monthly_feed_kg) AS total_feed_kg,
                
                -- Total biomass at start of month (kg)
                SUM(fcr_monthly.initial_biomass_kg) AS initial_biomass_kg,
                
                -- Total biomass at end of month (kg)
                SUM(fcr_monthly.final_biomass_kg) AS final_biomass_kg,
                
                -- Total biomass gain in the month (kg)
                SUM(fcr_monthly.final_biomass_kg - fcr_monthly.initial_biomass_kg) AS total_biomass_gain_kg,
                
                COUNT(DISTINCT fcr_monthly.tagid) AS tank_count,
                SUM(fcr_monthly.concentrado_records) AS concentrado_records,
                SUM(fcr_monthly.peso_records) AS peso_records,
                MIN(fcr_monthly.month_timestamp) AS month_timestamp
                
            FROM (
                SELECT 
                    tagid,
                    month,
                    year,
                    month_num,
                    
                    -- Monthly feed for this tank (kg) - sum all concentrado racion for the month
                    SUM(monthly_feed) AS monthly_feed_kg,
                    
                    -- Current month biomass (kg)
                    MAX(current_biomass) AS final_biomass_kg,
                    
                    -- Previous month biomass (kg) - get from previous month's peso record
                    COALESCE(
                        (SELECT p_prev.cah_peso_biomasa 
                         FROM cah_peso p_prev 
                         WHERE p_prev.cah_peso_tagid = fcr_base.tagid
                         AND DATE_FORMAT(p_prev.cah_peso_fecha, '%Y-%m') = 
                             DATE_FORMAT(DATE_SUB(STR_TO_DATE(CONCAT(fcr_base.month, '-01'), '%Y-%m-%d'), INTERVAL 1 MONTH), '%Y-%m')
                         ORDER BY p_prev.cah_peso_fecha DESC 
                         LIMIT 1
                        ), 
                        0
                    ) AS initial_biomass_kg,
                    
                    SUM(concentrado_count) AS concentrado_records,
                    SUM(peso_count) AS peso_records,
                    MIN(month_timestamp) AS month_timestamp
                    
                FROM (
                    -- Get concentrado data
                    SELECT 
                        cah_concentrado_tagid AS tagid,
                        DATE_FORMAT(cah_concentrado_fecha_inicio, '%Y-%m') AS month,
                        YEAR(cah_concentrado_fecha_inicio) AS year,
                        MONTH(cah_concentrado_fecha_inicio) AS month_num,
                        cah_concentrado_racion AS monthly_feed,
                        NULL AS current_biomass,
                        1 AS concentrado_count,
                        0 AS peso_count,
                        UNIX_TIMESTAMP(cah_concentrado_fecha_inicio) AS month_timestamp
                    FROM cah_concentrado 
                    WHERE cah_concentrado_racion > 0 
                        AND cah_concentrado_tagid IS NOT NULL
                    
                    UNION ALL
                    
                    -- Get peso data  
                    SELECT 
                        cah_peso_tagid AS tagid,
                        DATE_FORMAT(cah_peso_fecha, '%Y-%m') AS month,
                        YEAR(cah_peso_fecha) AS year,
                        MONTH(cah_peso_fecha) AS month_num,
                        0 AS monthly_feed,
                        cah_peso_biomasa AS current_biomass,
                        0 AS concentrado_count,
                        1 AS peso_count,
                        UNIX_TIMESTAMP(cah_peso_fecha) AS month_timestamp
                    FROM cah_peso 
                    WHERE cah_peso_promedio > 0 
                        AND cah_peso_tagid IS NOT NULL
                        AND cah_peso_biomasa > 0
                ) fcr_base
                GROUP BY fcr_base.tagid, fcr_base.month, fcr_base.year, fcr_base.month_num
                HAVING monthly_feed_kg > 0 AND final_biomass_kg > 0
            ) fcr_monthly
            $where_clause
            GROUP BY fcr_monthly.month, fcr_monthly.year, fcr_monthly.month_num
            HAVING total_biomass_gain_kg > 0 AND total_feed_kg > 0
        ) final_fcr
        ORDER BY year ASC, month_num ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $monthly_data = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[] = [
            'month' => $row['month'],
            'year' => (int)$row['year'],
            'month_num' => (int)$row['month_num'],
            'fcr_value' => $row['fcr_value'] ? (float)$row['fcr_value'] : null,
            'total_feed_kg' => $row['total_feed_kg'] ? (float)$row['total_feed_kg'] : null,
            'total_biomass_gain_kg' => $row['total_biomass_gain_kg'] ? (float)$row['total_biomass_gain_kg'] : null,
            'initial_biomass_kg' => $row['initial_biomass_kg'] ? (float)$row['initial_biomass_kg'] : null,
            'final_biomass_kg' => $row['final_biomass_kg'] ? (float)$row['final_biomass_kg'] : null,
            'tank_count' => (int)$row['tank_count'],
            'concentrado_records' => (int)$row['concentrado_records'],
            'peso_records' => (int)$row['peso_records'],
            'month_timestamp' => (int)$row['month_timestamp']
        ];
    }
    
    $data = $monthly_data;

    // Return both data and available tagids
    echo json_encode([
        'data' => $data,
        'tagids' => $tagids,
        'selected_tagid' => $selected_tagid
    ]);

} catch (Exception $e) {
    error_log("Error fetching monthly FCR data: " . $e->getMessage());
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
}
?>

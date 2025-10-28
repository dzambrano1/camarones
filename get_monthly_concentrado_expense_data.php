<?php
header('Content-Type: application/json');

// Include database connection details
require_once "./pdo_conexion.php";

$data = [];
$tagids = [];

// Get tagid filter from request (optional)
$selected_tagid = isset($_GET['tagid']) ? $_GET['tagid'] : 'all';

try {
    // First, get all available tagids for the dropdown
    $tagid_sql = "
        SELECT DISTINCT cah_concentrado_tagid, 
               (SELECT nombre FROM camarones WHERE tagid = cah_concentrado_tagid LIMIT 1) as nombre
        FROM cah_concentrado 
        WHERE cah_concentrado_tagid IS NOT NULL 
            AND cah_concentrado_racion > 0 
            AND cah_concentrado_costo > 0
        ORDER BY cah_concentrado_tagid ASC
    ";
    
    $tagid_stmt = $conn->prepare($tagid_sql);
    $tagid_stmt->execute();
    $tagid_results = $tagid_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tagid_results as $row) {
        $tagids[] = [
            'tagid' => $row['cah_concentrado_tagid'],
            'nombre' => $row['nombre'] ?? 'Sin nombre'
        ];
    }

    // Query to get monthly concentrado expenses
    // Expense calculation: cah_concentrado_racion * cah_concentrado_costo
    // (Amount in kg × Cost per kg = Total expense)
    $where_clause = "";
    $params = [];
    if ($selected_tagid !== 'all') {
        $where_clause = "AND cah_concentrado_tagid = :tagid";
        $params[':tagid'] = $selected_tagid;
    }
    
    $sql = "
        SELECT 
            DATE_FORMAT(cah_concentrado_fecha_inicio, '%Y-%m') AS month,
            SUM(cah_concentrado_racion * cah_concentrado_costo) AS monthly_expense,
            COUNT(*) AS record_count,
            MIN(cah_concentrado_tagid) AS sample_tagid,
            UNIX_TIMESTAMP(MIN(cah_concentrado_fecha_inicio)) as month_timestamp
        FROM cah_concentrado 
        WHERE cah_concentrado_racion > 0 
            AND cah_concentrado_costo > 0 
            $where_clause
        GROUP BY month 
        ORDER BY month ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $monthly_data = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[] = [
            'month' => $row['month'],
            'monthly_expense' => (float)$row['monthly_expense'],
            'record_count' => (int)$row['record_count'],
            'sample_tagid' => $row['sample_tagid'],
            'month_timestamp' => (int)$row['month_timestamp']
        ];
    }
    
    // Calculate cumulative expenses
    $cumulative_total = 0;
    foreach ($monthly_data as $key => $row) {
        $cumulative_total += $row['monthly_expense'];
        $monthly_data[$key]['cumulative_expense'] = $cumulative_total;
    }
    
    $data = $monthly_data;

    // Return both data and available tagids
    echo json_encode([
        'data' => $data,
        'tagids' => $tagids,
        'selected_tagid' => $selected_tagid
    ]);

} catch (Exception $e) {
    error_log("Error fetching monthly concentrado expense data: " . $e->getMessage());
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
}
?>

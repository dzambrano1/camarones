<?php
header('Content-Type: application/json');

// Include database connection details
require_once "./pdo_conexion.php"; // Adjust path if necessary

// Use mysqli for connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

mysqli_set_charset($conn, "utf8");

$data = [];
$tagids = [];

// Get tagid filter from request (optional)
$selected_tagid = isset($_GET['tagid']) ? mysqli_real_escape_string($conn, $_GET['tagid']) : 'all';

try {
    // First, get all available tagids for the dropdown
    $tagid_sql = "
        SELECT DISTINCT cah_peso_tagid, 
               (SELECT nombre FROM camarones WHERE tagid = cah_peso_tagid LIMIT 1) as nombre
        FROM cah_peso 
        WHERE cah_peso_tagid IS NOT NULL AND cah_peso_promedio > 0
        ORDER BY cah_peso_tagid ASC
    ";
    
    $tagid_result = mysqli_query($conn, $tagid_sql);
    if ($tagid_result) {
        while ($row = mysqli_fetch_assoc($tagid_result)) {
            $tagids[] = [
                'tagid' => $row['cah_peso_tagid'],
                'nombre' => $row['nombre'] ?? 'Sin nombre'
            ];
        }
        mysqli_free_result($tagid_result);
    }

    // Query to get average monthly cah_peso_promedio
    $where_clause = "";
    if ($selected_tagid !== 'all') {
        $where_clause = "AND cah_peso_tagid = '$selected_tagid'";
    }
    
    $sql = "
        SELECT 
            DATE_FORMAT(cah_peso_fecha, '%Y-%m') AS month,
            AVG(cah_peso_promedio) AS avg_peso_promedio,
            COUNT(*) AS record_count,
            MIN(cah_peso_tagid) AS sample_tagid,
            UNIX_TIMESTAMP(MIN(cah_peso_fecha)) as month_timestamp
        FROM cah_peso 
        WHERE cah_peso_promedio > 0 $where_clause
        GROUP BY month 
        ORDER BY month ASC
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'month' => $row['month'],
                'avg_peso_promedio' => (float)$row['avg_peso_promedio'],
                'record_count' => (int)$row['record_count'],
                'sample_tagid' => $row['sample_tagid'],
                'month_timestamp' => (int)$row['month_timestamp']
            ];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error executing average peso query: " . mysqli_error($conn));
    }

    // Return both data and available tagids
    echo json_encode([
        'data' => $data,
        'tagids' => $tagids,
        'selected_tagid' => $selected_tagid
    ]);

} catch (Exception $e) {
    error_log("Error fetching monthly weight value data: " . $e->getMessage());
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
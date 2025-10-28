<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Simple query to get all salinity records
    $sql = "SELECT 
                wsl.id,
                wsl.pond_id,
                wsl.salinity_ppt,
                wsl.source,
                wsl.timestamp,
                wsl.phase,
                wsl.product,
                wsl.product_qty,
                wsl.product_price,
                wsl.created_at
            FROM water_salinity_log wsl
            ORDER BY wsl.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return simple success response
    echo json_encode([
        'success' => true,
        'records' => $results
    ]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching simple salinity data: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos: ' . $e->getMessage()
    ]);
}
?>

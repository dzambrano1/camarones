<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water redox records with animal information if available
    $sql = "SELECT 
                wrl.id,
                wrl.pond_id,
                wrl.redox_mv,
                wrl.source,
                wrl.timestamp,
                wrl.phase,
                wrl.product,
                wrl.product_price,
                wrl.product_qty,
                wrl.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_redox_log wrl
            LEFT JOIN camarones ca ON wrl.pond_id = ca.tagid
            ORDER BY wrl.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode(['success' => true, 'records' => $results]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water redox data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos de redox: ' . $e->getMessage()]);
}

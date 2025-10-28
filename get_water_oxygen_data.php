<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water oxygen records with animal information if available
    $sql = "SELECT 
                wol.id,
                wol.pond_id,
                wol.oxygen_mg_l,
                wol.source,
                wol.timestamp,
                wol.phase,
                wol.product,
                wol.product_qty,
                wol.product_price,
                wol.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_oxygen_log wol
            LEFT JOIN camarones ca ON wol.pond_id = ca.tagid
            ORDER BY wol.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON with success structure
    echo json_encode(['success' => true, 'records' => $results]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water oxygen data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos de oxígeno: ' . $e->getMessage()]);
}
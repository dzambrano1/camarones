<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water nitrite records with animal information if available
    $sql = "SELECT 
                wnl.id,
                wnl.pond_id,
                wnl.nitrite_mg_l,
                wnl.source,
                wnl.timestamp,
                wnl.phase,
                wnl.product,
                wnl.product_price,
                wnl.product_qty,
                wnl.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_nitrite_log wnl
            LEFT JOIN camarones ca ON wnl.pond_id = ca.tagid
            ORDER BY wnl.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode(['success' => true, 'records' => $results]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water nitrite data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos de nitritos: ' . $e->getMessage()]);
}

<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water pH records with animal information if available
    $sql = "SELECT 
                wpl.id,
                wpl.pond_id,
                wpl.ph_level,
                wpl.source,
                wpl.timestamp,
                wpl.phase,
                wpl.product,
                wpl.product_qty,
                wpl.product_price,
                wpl.timestamp,
                wpl.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_ph_log wpl
            LEFT JOIN camarones ca ON wpl.pond_id = ca.tagid
            ORDER BY wpl.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water pH data: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener datos de pH: ' . $e->getMessage()]);
}

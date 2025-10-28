<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water salinity records with animal information if available
    $sql = "SELECT 
                wsl.id,
                wsl.pond_id,
                wsl.salinity_ppt,
                wsl.source,
                wsl.timestamp,
                wsl.phase,
                wsl.product as product,
                wsl.product_qty,
                wsl.product_price,
                wsl.timestamp,
                wsl.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_salinity_log wsl
            LEFT JOIN camarones ca ON wsl.pond_id = ca.tagid
            ORDER BY wsl.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water salinity data: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener datos de salinidad: ' . $e->getMessage()]);
}

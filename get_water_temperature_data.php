<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water temperature records with animal information if available
    $sql = "SELECT 
                wtl.id,
                wtl.pond_id,
                wtl.temperature_celsius,
                wtl.source,
                wtl.fecha,
                wtl.phase,
                wtl.hora,
                wtl.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_temperature_log wtl
            LEFT JOIN camarones ca ON wtl.pond_id = ca.tagid
            ORDER BY wtl.fecha DESC, wtl.hora DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water temperature data: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener datos de temperatura: ' . $e->getMessage()]);
}

<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water alkalinity records with animal information if available
    $sql = "SELECT 
                wal.id,
                wal.pond_id,
                wal.alkalinity_mg_l,
                wal.source,
                wal.timestamp,
                wal.phase,
                wal.timestamp,
                wal.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_alkalinity_log wal
            LEFT JOIN camarones ca ON wal.pond_id = ca.tagid
            ORDER BY wal.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water alkalinity data: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener datos de alcalinidad: ' . $e->getMessage()]);
}
?>

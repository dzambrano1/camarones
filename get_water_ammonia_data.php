<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get water ammonia records with animal information if available
    $sql = "SELECT
                wal.id,
                wal.pond_id,
                wal.total_ammonia_mg_l,
                wal.nh3_mg_l,
                wal.source,
                wal.timestamp,
                wal.phase,
                wal.product,
                wal.product_price,
                wal.product_qty,
                wal.created_at,
                COALESCE(ca.nombre, 'Sin nombre') as estanque_nombre
            FROM water_ammonia_log wal
            LEFT JOIN camarones ca ON wal.pond_id = ca.tagid
            ORDER BY wal.timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON with success structure
    echo json_encode(['success' => true, 'records' => $results]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching water ammonia data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos de amoníaco: ' . $e->getMessage()]);
}

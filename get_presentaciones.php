<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get all presentaciones
    $sql = "SELECT id, cac_presentaciones_nombre as nombre FROM cac_presentaciones ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching presentaciones data: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener datos de presentaciones: ' . $e->getMessage()]);
}
?>

<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get phase options from cac_etapas table
    $sql = "SELECT id, cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode([
        'success' => true,
        'phases' => $results
    ]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching phase options: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener opciones de fases: ' . $e->getMessage()
    ]);
}

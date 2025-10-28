<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get sensor options from cac_sensores table
    $sql = "SELECT id, sensores FROM cac_sensores ORDER BY sensores ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode([
        'success' => true,
        'sensors' => $results
    ]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching sensor options: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener opciones de sensores: ' . $e->getMessage()
    ]);
}

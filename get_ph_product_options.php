<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

try {
    // Query to get product options from cac_ph table
    $sql = "SELECT id, cac_ph_vacuna, cac_ph_dosis, cac_ph_costo 
            FROM cac_ph 
            ORDER BY cac_ph_vacuna ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as JSON
    echo json_encode([
        'success' => true,
        'products' => $results
    ]);
    
} catch (PDOException $e) {
    // Return error message
    error_log("Error fetching product options: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener opciones de productos: ' . $e->getMessage()
    ]);
}

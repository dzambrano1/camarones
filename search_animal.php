<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

try {
    // Verify connection is PDO
    if (!($conn instanceof PDO)) {
        throw new Exception("Error: Database connection is not a PDO instance");
    }
    
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the search query
    $query = trim($_GET['query'] ?? '');
    
    if (empty($query)) {
        throw new Exception('Por favor, ingresa un Tag ID o nombre para buscar');
    }
    
    // Search for animal by tagid or name (case-insensitive)
    $sql = "SELECT id, tagid, nombre, fecha_nacimiento, peso_nacimiento, poblacion, etapa, estatus, 
                   fecha_compra, peso_compra, monto_compra, cantidad_compra,
                   fecha_venta, peso_venta, precio_venta,
                   deceso_causa, deceso_fecha, image
            FROM camarones 
            WHERE tagid = :query OR LOWER(nombre) LIKE LOWER(:query_like)
            ORDER BY 
                CASE WHEN tagid = :query THEN 1 ELSE 2 END,
                nombre ASC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':query', $query, PDO::PARAM_STR);
    $queryLike = '%' . $query . '%';
    $stmt->bindParam(':query_like', $queryLike, PDO::PARAM_STR);
    $stmt->execute();
    
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($animal) {
        // Format dates for display
        if ($animal['fecha_nacimiento']) {
            $animal['fecha_nacimiento_formatted'] = date('d/m/Y', strtotime($animal['fecha_nacimiento']));
        }
        if ($animal['fecha_compra'] && $animal['fecha_compra'] != '0000-00-00') {
            $animal['fecha_compra_formatted'] = date('d/m/Y', strtotime($animal['fecha_compra']));
        }
        if ($animal['fecha_venta'] && $animal['fecha_venta'] != '0000-00-00') {
            $animal['fecha_venta_formatted'] = date('d/m/Y', strtotime($animal['fecha_venta']));
        }
        if ($animal['deceso_fecha'] && $animal['deceso_fecha'] != '0000-00-00') {
            $animal['deceso_fecha_formatted'] = date('d/m/Y', strtotime($animal['deceso_fecha']));
        }
        
        // Format numbers
        $animal['peso_nacimiento_formatted'] = number_format($animal['peso_nacimiento'], 2);
        $animal['poblacion_formatted'] = number_format($animal['poblacion']);
        
        if ($animal['peso_compra'] > 0) {
            $animal['peso_compra_formatted'] = number_format($animal['peso_compra'], 2);
        }
        if ($animal['monto_compra'] > 0) {
            $animal['monto_compra_formatted'] = '$' . number_format($animal['monto_compra'], 2);
        }
        if ($animal['cantidad_compra'] > 0) {
            $animal['cantidad_compra_formatted'] = number_format($animal['cantidad_compra']);
        }
        if ($animal['peso_venta'] > 0) {
            $animal['peso_venta_formatted'] = number_format($animal['peso_venta'], 2);
        }
        if ($animal['precio_venta'] > 0) {
            $animal['precio_venta_formatted'] = '$' . number_format($animal['precio_venta'], 2);
        }
        
        echo json_encode([
            'success' => true,
            'animal' => $animal,
            'message' => 'Animal encontrado exitosamente'
        ]);
    } else {
        // Try to find similar matches for better user experience
        $similarSql = "SELECT tagid, nombre 
                      FROM camarones 
                      WHERE LOWER(nombre) LIKE LOWER(:query_similar) 
                         OR LOWER(tagid) LIKE LOWER(:query_similar)
                      ORDER BY nombre ASC 
                      LIMIT 5";
        
        $similarStmt = $conn->prepare($similarSql);
        $querySimilar = '%' . $query . '%';
        $similarStmt->bindParam(':query_similar', $querySimilar, PDO::PARAM_STR);
        $similarStmt->execute();
        
        $suggestions = $similarStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $message = "No se encontró ningún estanque con Tag ID o nombre '{$query}'";
        
        if (!empty($suggestions)) {
            $message .= "\n\n¿Te refieres a alguno de estos?:\n";
            foreach ($suggestions as $suggestion) {
                $message .= "• {$suggestion['nombre']} (Tag ID: {$suggestion['tagid']})\n";
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => $message,
            'suggestions' => $suggestions
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Database Error in search_animal.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General Error in search_animal.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

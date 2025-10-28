<?php
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $input['action'];

try {
    switch ($action) {
        case 'create':
            // Validate required fields
            $requiredFields = ['pond_id', 'salinity_ppt', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = $input['pond_id'];
            $salinity_ppt = floatval($input['salinity_ppt']);
            $source = $input['source'];
            $phase = $input['phase'];
            $fecha = $input['fecha'];
            $hora = $input['hora'];
            $product = $input['product'] ?? '';
            $product_qty = isset($input['product_qty']) ? floatval($input['product_qty']) : null;
            $product_price = isset($input['product_price']) ? floatval($input['product_price']) : null;
            
            // Validate salinity range
            if ($salinity_ppt < 0.5 || $salinity_ppt > 45) {
                throw new Exception("El valor de Salinidad debe estar entre 0.5 y 45 ppt");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("INSERT INTO water_salinity_log (pond_id, salinity_ppt, source, phase, product, product_qty, product_price, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $salinity_ppt, $source, $phase, $product, $product_qty, $product_price, $timestamp]);
            
            echo json_encode(['success' => true, 'message' => 'Registro creado correctamente']);
            break;
            
        case 'update':
            // Validate required fields
            $requiredFields = ['id', 'pond_id', 'salinity_ppt', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($input['id']);
            $pond_id = $input['pond_id'];
            $salinity_ppt = floatval($input['salinity_ppt']);
            $source = $input['source'];
            $phase = $input['phase'];
            $fecha = $input['fecha'];
            $hora = $input['hora'];
            $product = $input['product'] ?? '';
            $product_qty = isset($input['product_qty']) ? floatval($input['product_qty']) : null;
            $product_price = isset($input['product_price']) ? floatval($input['product_price']) : null;
            
            // Validate salinity range
            if ($salinity_ppt < 0.5 || $salinity_ppt > 45) {
                throw new Exception("El valor de Salinidad debe estar entre 0.5 y 45 ppt");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("UPDATE water_salinity_log SET pond_id = ?, salinity_ppt = ?, source = ?, phase = ?, product = ?, product_qty = ?, product_price = ?, timestamp = ? WHERE id = ?");
            $stmt->execute([$pond_id, $salinity_ppt, $source, $phase, $product, $product_qty, $product_price, $timestamp, $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro o no se realizaron cambios']);
            }
            break;
            
        case 'delete':
            // Validate required fields
            if (!isset($input['id']) || empty($input['id'])) {
                throw new Exception("ID requerido para eliminar");
            }
            
            $id = intval($input['id']);
            
            // Execute query
            $stmt = $conn->prepare("DELETE FROM water_salinity_log WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro']);
            }
            break;
            
        default:
            throw new Exception("Acción no válida: $action");
    }
    
} catch (PDOException $e) {
    // Database error
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // General error
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

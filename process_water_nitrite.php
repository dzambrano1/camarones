<?php
// Include database connection
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Handle different actions
if (isset($input['action'])) {
    $action = $input['action'];
    
    try {
        // INSERT operation
        if ($action === 'insert') {
            // Validate required fields
            $requiredFields = ['pond_id', 'nitrite_mg_l', 'source', 'phase', 'product', 'product_price', 'product_qty', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = $input['pond_id'];
            $nitrite_mg_l = floatval($input['nitrite_mg_l']);
            $source = $input['source'];
            $phase = $input['phase'];
            $product = $input['product'];
            $product_price = floatval($input['product_price']);
            $product_qty = floatval($input['product_qty']);
            $fecha = $input['fecha'];
            $hora = $input['hora'];
            
            // Validate nitrite range (typical safe range 0-1 mg/L for aquaculture)
            if ($nitrite_mg_l < 0 || $nitrite_mg_l > 5) {
                throw new Exception("El valor de nitritos debe estar entre 0 y 5 mg/L");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("INSERT INTO water_nitrite_log (pond_id, nitrite_mg_l, source, phase, product, product_price, product_qty, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $nitrite_mg_l, $source, $phase, $product, $product_price, $product_qty, $timestamp]);
            
            echo json_encode(['success' => true, 'message' => 'Registro de nitritos del agua agregado correctamente']);
        }
        
        // UPDATE operation
        else if ($action === 'update') {
            // Validate required fields
            $requiredFields = ['id', 'nitrite_mg_l', 'source', 'phase', 'product', 'product_price', 'product_qty', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($input['id']);
            $nitrite_mg_l = floatval($input['nitrite_mg_l']);
            $source = $input['source'];
            $phase = $input['phase'];
            $product = $input['product'];
            $product_price = floatval($input['product_price']);
            $product_qty = floatval($input['product_qty']);
            $fecha = $input['fecha'];
            $hora = $input['hora'];
            
            // Validate nitrite range (typical safe range 0-1 mg/L for aquaculture)
            if ($nitrite_mg_l < 0 || $nitrite_mg_l > 5) {
                throw new Exception("El valor de nitritos debe estar entre 0 y 5 mg/L");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("UPDATE water_nitrite_log SET nitrite_mg_l = ?, source = ?, phase = ?, product = ?, product_price = ?, product_qty = ?, timestamp = ? WHERE id = ?");
            $stmt->execute([$nitrite_mg_l, $source, $phase, $product, $product_price, $product_qty, $timestamp, $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro o no se realizaron cambios']);
            }
        }
        
        // DELETE operation
        else if ($action === 'delete') {
            // Validate required fields
            if (!isset($input['id']) || empty($input['id'])) {
                throw new Exception("ID requerido para eliminar");
            }
            
            $id = intval($input['id']);
            
            // Execute query
            $stmt = $conn->prepare("DELETE FROM water_nitrite_log WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro']);
            }
        }
        
        // Invalid action
        else {
            throw new Exception("Acción no válida");
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
} else {
    // No action specified
    echo json_encode(['success' => false, 'message' => 'No se especificó ninguna acción']);
}
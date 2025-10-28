<?php
// Include database connection
require_once './pdo_conexion.php';

// Set response header to JSON
header('Content-Type: application/json');

// Handle different actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    try {
        // INSERT operation
        if ($action === 'insert') {
            // Validate required fields
            $requiredFields = ['pond_id', 'salinity_ppt', 'source', 'phase', 'fecha', 'hora', 'product', 'product_qty', 'product_price'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = intval($_POST['pond_id']);
            $salinity_ppt = floatval($_POST['salinity_ppt']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $product = $_POST['product'];
            $product_qty = floatval($_POST['product_qty']);
            $product_price = floatval($_POST['product_price']);
            
            // Normalize hora to include seconds if not present
            if (strlen($hora) == 5) { // HH:MM format
                $hora = $hora . ':00'; // Add seconds
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora;
            
            // Execute query
            $stmt = $conn->prepare("INSERT INTO water_salinity_log (pond_id, salinity_ppt, source, phase, product, product_qty, product_price, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $salinity_ppt, $source, $phase, $product, $product_qty, $product_price, $timestamp]);
            
            echo json_encode(['success' => true, 'message' => 'Registro de salinidad del agua agregado correctamente']);
        }
        
        // UPDATE operation
        else if ($action === 'update') {
            // Validate required fields
            $requiredFields = ['id', 'salinity_ppt', 'source', 'phase', 'fecha', 'hora', 'product', 'product_qty', 'product_price'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($_POST['id']);
            $salinity_ppt = floatval($_POST['salinity_ppt']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $product = $_POST['product'];
            $product_qty = floatval($_POST['product_qty']);
            $product_price = floatval($_POST['product_price']);
            
            // Normalize hora to include seconds if not present
            if (strlen($hora) == 5) { // HH:MM format
                $hora = $hora . ':00'; // Add seconds
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora;
            
            // Check if record exists
            $checkStmt = $conn->prepare("SELECT id FROM water_salinity_log WHERE id = ?");
            $checkStmt->execute([$id]);
            
            if ($checkStmt->rowCount() === 0) {
                throw new Exception("No se encontró el registro especificado");
            }
            
            // Execute query
            $stmt = $conn->prepare("UPDATE water_salinity_log SET salinity_ppt = ?, source = ?, phase = ?, product = ?, product_qty = ?, product_price = ?, timestamp = ? WHERE id = ?");
            $stmt->execute([$salinity_ppt, $source, $phase, $product, $product_qty, $product_price, $timestamp, $id]);
            
            // Always return success if query executed without error (allow no-change saves)
            echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
        }
        
        // DELETE operation
        else if ($action === 'delete') {
            // Validate required fields
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID requerido para eliminar");
            }
            
            $id = intval($_POST['id']);
            
            // Execute query
            $stmt = $conn->prepare("DELETE FROM water_salinity_log WHERE id = ?");
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
?>

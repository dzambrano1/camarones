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
            $requiredFields = ['pond_id', 'total_ammonia_mg_l', 'nh3_mg_l', 'source', 'phase', 'product', 'product_price', 'product_qty', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = $_POST['pond_id'];
            $total_ammonia_mg_l = floatval($_POST['total_ammonia_mg_l']);
            $nh3_mg_l = floatval($_POST['nh3_mg_l']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $product = $_POST['product'];
            $product_price = floatval($_POST['product_price']);
            $product_qty = floatval($_POST['product_qty']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Validate ammonia range (typical range 0-5 mg/L for total ammonia in aquaculture)
            if ($total_ammonia_mg_l < 0 || $total_ammonia_mg_l > 5) {
                throw new Exception("El valor de amoníaco total debe estar entre 0 y 5 mg/L");
            }
            
            // Validate NH3 range (should be less than total ammonia)
            if ($nh3_mg_l < 0 || $nh3_mg_l > $total_ammonia_mg_l) {
                throw new Exception("El valor de NH₃ debe estar entre 0 y el valor de amoníaco total");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("INSERT INTO water_ammonia_log (pond_id, total_ammonia_mg_l, nh3_mg_l, source, phase, product, product_price, product_qty, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $total_ammonia_mg_l, $nh3_mg_l, $source, $phase, $product, $product_price, $product_qty, $timestamp]);
            
            echo json_encode(['success' => true, 'message' => 'Registro de amoníaco del agua agregado correctamente']);
        }
        
        // UPDATE operation
        else if ($action === 'update') {
            // Validate required fields
            $requiredFields = ['id', 'total_ammonia_mg_l', 'nh3_mg_l', 'source', 'phase', 'product', 'product_price', 'product_qty', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($_POST['id']);
            $total_ammonia_mg_l = floatval($_POST['total_ammonia_mg_l']);
            $nh3_mg_l = floatval($_POST['nh3_mg_l']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $product = $_POST['product'];
            $product_price = floatval($_POST['product_price']);
            $product_qty = floatval($_POST['product_qty']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Validate ammonia range (typical range 0-5 mg/L for total ammonia in aquaculture)
            if ($total_ammonia_mg_l < 0 || $total_ammonia_mg_l > 5) {
                throw new Exception("El valor de amoníaco total debe estar entre 0 y 5 mg/L");
            }
            
            // Validate NH3 range (should be less than total ammonia)
            if ($nh3_mg_l < 0 || $nh3_mg_l > $total_ammonia_mg_l) {
                throw new Exception("El valor de NH₃ debe estar entre 0 y el valor de amoníaco total");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("UPDATE water_ammonia_log SET total_ammonia_mg_l = ?, nh3_mg_l = ?, source = ?, phase = ?, product = ?, product_price = ?, product_qty = ?, timestamp = ? WHERE id = ?");
            $stmt->execute([$total_ammonia_mg_l, $nh3_mg_l, $source, $phase, $product, $product_price, $product_qty, $timestamp, $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro o no se realizaron cambios']);
            }
        }
        
        // DELETE operation
        else if ($action === 'delete') {
            // Validate required fields
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID requerido para eliminar");
            }
            
            $id = intval($_POST['id']);
            
            // Execute query
            $stmt = $conn->prepare("DELETE FROM water_ammonia_log WHERE id = ?");
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

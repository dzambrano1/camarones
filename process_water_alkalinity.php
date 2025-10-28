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
            $requiredFields = ['pond_id', 'alkalinity_mg_l', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = $_POST['pond_id'];
            $alkalinity_mg_l = floatval($_POST['alkalinity_mg_l']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Validate alkalinity range (typical range 80-200 mg/L CaCO3 for aquaculture)
            if ($alkalinity_mg_l < 0 || $alkalinity_mg_l > 500) {
                throw new Exception("El valor de alcalinidad debe estar entre 0 y 500 mg/L CaCO₃");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("INSERT INTO water_alkalinity_log (pond_id, alkalinity_mg_l, source, phase, timestamp) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $alkalinity_mg_l, $source, $phase, $timestamp]);
            
            echo json_encode(['success' => true, 'message' => 'Registro de alcalinidad del agua agregado correctamente']);
        }
        
        // UPDATE operation
        else if ($action === 'update') {
            // Validate required fields
            $requiredFields = ['id', 'alkalinity_mg_l', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($_POST['id']);
            $alkalinity_mg_l = floatval($_POST['alkalinity_mg_l']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Validate alkalinity range (typical range 80-200 mg/L CaCO3 for aquaculture)
            if ($alkalinity_mg_l < 0 || $alkalinity_mg_l > 500) {
                throw new Exception("El valor de alcalinidad debe estar entre 0 y 500 mg/L CaCO₃");
            }
            
            // Combine date and time into timestamp
            $timestamp = $fecha . ' ' . $hora . ':00';
            
            // Execute query
            $stmt = $conn->prepare("UPDATE water_alkalinity_log SET alkalinity_mg_l = ?, source = ?, phase = ?, timestamp = ? WHERE id = ?");
            $stmt->execute([$alkalinity_mg_l, $source, $phase, $timestamp, $id]);
            
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
            $stmt = $conn->prepare("DELETE FROM water_alkalinity_log WHERE id = ?");
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

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
            $requiredFields = ['pond_id', 'temperature_celsius', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $pond_id = $_POST['pond_id'];
            $temperature_celsius = floatval($_POST['temperature_celsius']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Ensure hora has seconds (HH:MM:SS format)
            if (strlen($hora) == 5) { // HH:MM format
                $hora = $hora . ':00'; // Add seconds
            }
            
            // Execute query with separate fecha and hora fields
            $stmt = $conn->prepare("INSERT INTO water_temperature_log (pond_id, temperature_celsius, source, phase, fecha, hora) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pond_id, $temperature_celsius, $source, $phase, $fecha, $hora]);
            
            echo json_encode(['success' => true, 'message' => 'Registro de temperatura del agua agregado correctamente']);
        }
        
        // UPDATE operation
        else if ($action === 'update') {
            // Validate required fields
            $requiredFields = ['id', 'temperature_celsius', 'source', 'phase', 'fecha', 'hora'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Prepare data
            $id = intval($_POST['id']);
            $temperature_celsius = floatval($_POST['temperature_celsius']);
            $source = $_POST['source'];
            $phase = $_POST['phase'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            // Ensure hora has seconds (HH:MM:SS format)
            if (strlen($hora) == 5) { // HH:MM format
                $hora = $hora . ':00'; // Add seconds
            }
            
            // First, check if the record exists
            $checkStmt = $conn->prepare("SELECT * FROM water_temperature_log WHERE id = ?");
            $checkStmt->execute([$id]);
            $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingRecord) {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro con ID: ' . $id]);
                return;
            }
            
            // Execute query with separate fecha and hora fields
            $stmt = $conn->prepare("UPDATE water_temperature_log SET temperature_celsius = ?, source = ?, phase = ?, fecha = ?, hora = ? WHERE id = ?");
            $stmt->execute([$temperature_celsius, $source, $phase, $fecha, $hora, $id]);
            
            // Always return success since the query executed without error
            // Even if no rows were affected (data was identical), this is allowed
            echo json_encode(['success' => true, 'message' => 'Registro procesado correctamente']);
            
            // Log for debugging purposes
            error_log("Update processed - ID: $id, Rows affected: " . $stmt->rowCount() . ", Data: " . json_encode([
                'temperature_celsius' => $temperature_celsius,
                'source' => $source,
                'phase' => $phase,
                'fecha' => $fecha,
                'hora' => $hora
            ]));
        }
        
        // DELETE operation
        else if ($action === 'delete') {
            // Validate required fields
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID requerido para eliminar");
            }
            
            $id = intval($_POST['id']);
            
            // Execute query
            $stmt = $conn->prepare("DELETE FROM water_temperature_log WHERE id = ?");
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

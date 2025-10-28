<?php
require_once './pdo_conexion.php';
// Disable error reporting in output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Use the PDO connection from pdo_conexion.php instead of creating a new mysqli connection
    // The connection is already established as $conn in the included file

    // Get action parameter to determine operation type
    $action = $_POST['action'] ?? 'update';

    // Get form data
    $id = $_POST['id'] ?? ''; // Database ID for updates
    $tagid = $_POST['tagid'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $fecha_compra = $_POST['fecha_compra'] ?? '';
    $etapa = $_POST['etapa'] ?? '';
    $estatus = $_POST['estatus'] ?? '';
    $poblacion = $_POST['poblacion'] ?? '';

    // Validate required fields based on action
    if (empty($tagid)) {
        throw new Exception("Tag ID es requerido");
    }

    if ($action === 'update') {
        if (empty($id)) {
            throw new Exception("ID es requerido para actualizar");
        }
        if (empty($nombre) || empty($fecha_nacimiento)) {
            throw new Exception("Nombre y fecha de nacimiento son requeridos para actualizar");
        }
    }

    // Process image uploads for update and insert actions
    $update_fields = [];
    $params = [];
    
    if ($action === 'update' || $action === 'insert') {
        // Handle main image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
            
            // Full path for file storage
            $targetPath = $uploadDir . $newFileName;

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Tipo de archivo no permitido");
            }
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                throw new Exception("Error al subir la imagen");
            }
            
            // Store the path in the update fields
            $update_fields['image'] = $targetPath;
        }
        
        // Handle image2 upload if present
        if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $fileExtension = strtolower(pathinfo($_FILES['image2']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
            
            // Full path for file storage
            $targetPath = $uploadDir . $newFileName;

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Tipo de archivo no permitido para imagen 2");
            }
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['image2']['tmp_name'], $targetPath)) {
                throw new Exception("Error al subir la imagen 2");
            }
            
            // Store the path in the update fields
            $update_fields['image2'] = $targetPath;
        }
        
        // Handle image3 upload if present
        if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $fileExtension = strtolower(pathinfo($_FILES['image3']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
            
            // Full path for file storage
            $targetPath = $uploadDir . $newFileName;

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Tipo de archivo no permitido para imagen 3");
            }
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['image3']['tmp_name'], $targetPath)) {
                throw new Exception("Error al subir la imagen 3");
            }
            
            // Store the path in the update fields
            $update_fields['image3'] = $targetPath;
        }
        
        // Handle video upload if present
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/videos/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $fileExtension = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
            
            // Full path for file storage
            $targetPath = $uploadDir . $newFileName;

            // Validate file type
            $allowedTypes = ['mp4', 'webm', 'ogg', 'mov'];
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Tipo de archivo de video no permitido");
            }
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['video']['tmp_name'], $targetPath)) {
                throw new Exception("Error al subir el video");
            }
            
            // Store the path in the update fields
            $update_fields['video'] = $targetPath;
        }
    }

    // Handle different operations based on action
    switch ($action) {
        case 'insert':
            // Check if record already exists
            $checkQuery = "SELECT COUNT(*) as count FROM camarones WHERE tagid = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(1, $tagid);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Animal exists, update purchase fields
                $update_fields['fecha_compra'] = $fecha_compra;
                $update_fields['peso_compra'] = $peso_compra;
                $update_fields['monto_compra'] = $monto_compra;
                $update_fields['poblacion'] = $poblacion;

                // Create update query
                $set_clauses = [];
                foreach ($update_fields as $field => $value) {
                    $set_clauses[] = "$field = :$field";
                }
                
                $sql = "UPDATE camarones SET " . implode(", ", $set_clauses) . " WHERE tagid = :tagid";
                
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error en la preparación de la consulta");
                }
                
                // Bind parameters
                foreach ($update_fields as $field => $value) {
                    $stmt->bindParam(":$field", $update_fields[$field]);
                }
                $stmt->bindParam(':tagid', $tagid);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar el registro");
                }
                
                echo json_encode([
                    "success" => true,
                    "message" => "Registro de compra agregado exitosamente",
                    "redirect" => "camarones_register_compras.php"
                ]);
            } else {
                // Animal doesn't exist, insert new record (basic fields + purchase fields)
                $all_fields = [
                    'tagid' => $tagid,
                    'nombre' => $nombre, 
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'fecha_compra' => $fecha_compra,
                    'etapa' => $etapa,
                    'estatus' => $estatus,
                    'peso_compra' => $peso_compra,
                    'monto_compra' => $monto_compra,
                    'poblacion' => $poblacion
                ];
                
                // Merge with uploaded files
                $all_fields = array_merge($all_fields, $update_fields);
                
                $fields = array_keys($all_fields);
                $placeholders = array_map(function($field) { return ":$field"; }, $fields);
                
                $sql = "INSERT INTO camarones (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error en la preparación de la consulta");
                }
                
                // Bind parameters
                foreach ($all_fields as $field => $value) {
                    $stmt->bindParam(":$field", $all_fields[$field]);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar el registro");
                }
                
                echo json_encode([
                    "success" => true,
                    "message" => "Animal agregado exitosamente con registro de compra",
                    "redirect" => "camarones_register_compras.php"
                ]);
            }
            break;
            
        case 'update':
            // Update all fields
            $all_update_fields = [
                'nombre' => $nombre,
                'tagid' => $tagid,
                'fecha_nacimiento' => $fecha_nacimiento,
                'fecha_compra' => $fecha_compra,
                'etapa' => $etapa,
                'estatus' => $estatus,
                'peso_compra' => $peso_compra,
                'monto_compra' => $monto_compra,
                'poblacion' => $poblacion
            ];
            
            // Merge with uploaded files
            $all_update_fields = array_merge($all_update_fields, $update_fields);
            
            // Create update query
            $set_clauses = [];
            foreach ($all_update_fields as $field => $value) {
                $set_clauses[] = "$field = :$field";
            }

            // Use ID instead of tagid for the WHERE clause
            $sql = "UPDATE camarones SET " . implode(", ", $set_clauses) . " WHERE id = :id";
            
            // Prepare and execute the update
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta");
            }
            
            // Bind parameters
            foreach ($all_update_fields as $field => $value) {
                $stmt->bindParam(":$field", $all_update_fields[$field]);
            }
            $stmt->bindParam(':id', $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el registro");
            }
            
            echo json_encode([
                "success" => true,
                "message" => "Animal actualizado exitosamente"
            ]);
            break;
            
        case 'delete':
            // Set purchase fields to NULL (not deleting the entire animal record)
            $sql = "UPDATE camarones SET fecha_compra = NULL, peso_compra = NULL, monto_compra = NULL WHERE tagid = :tagid";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta");
            }
            
            $stmt->bindParam(':tagid', $tagid);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar el registro de compra");
            }
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("No se encontró el registro o no se realizaron cambios");
            }
            
            echo json_encode([
                "success" => true,
                "message" => "Registro de compra eliminado exitosamente",
                "redirect" => "camarones_register_compras.php"
            ]);
            break;
            
        case 'delete_nacimiento':
            // Set birth fields to NULL (not deleting the entire animal record)
            $sql = "UPDATE camarones SET fecha_nacimiento = NULL, peso_nacimiento = NULL WHERE tagid = :tagid";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta");
            }
            
            $stmt->bindParam(':tagid', $tagid);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar el registro de nacimiento");
            }
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("No se encontró el registro o no se realizaron cambios");
            }
            
            echo json_encode([
                "success" => true,
                "message" => "Registro de nacimiento eliminado exitosamente"
            ]);
            break;
            
        default:
            throw new Exception("Acción no válida");
    }

} catch (Exception $e) {
    // Log error to file instead of output
    error_log("Error in camarones_update.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}


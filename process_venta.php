<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once './pdo_conexion.php';

try {
    // Check if connection is PDO
    if (!($conn instanceof PDO)) {
        throw new Exception("Error: Connection is not a PDO instance");
    }
    
    // Set PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Solo se aceptan solicitudes POST.');
    }
    
    // Get and validate action
    $action = $_POST['action'] ?? '';
    if (empty($action)) {
        throw new Exception('Acción no especificada.');
    }
    
    switch ($action) {
        case 'insert':
            handleInsert($conn);
            break;
            
        case 'update':
            handleUpdate($conn);
            break;
            
        case 'delete':
            handleDelete($conn);
            break;
            
        default:
            throw new Exception('Acción no válida: ' . $action);
    }
    
} catch (Exception $e) {
    error_log('Error in process_venta.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'server_error'
    ]);
}

function validateRequiredFields($data, $requiredFields) {
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        throw new Exception('Campos requeridos faltantes: ' . implode(', ', $missingFields));
    }
}

function getAvailablePopulation($conn, $tagid) {
    try {
        // Convert tagid to handle both string and integer types
        $tagid_str = (string) $tagid;
        $tagid_int = (int) $tagid;
        
        // Get initial population from camarones table (tagid is varchar)
        $stmt = $conn->prepare("SELECT poblacion FROM camarones WHERE tagid = :tagid");
        $stmt->bindParam(':tagid', $tagid_str, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("No se encontró el estanque con ID: $tagid_str");
        }
        
        $initialPopulation = (int) $result['poblacion'];
        
        // Calculate total deaths (cah_decesos_tagid is int according to schema)
        $stmt = $conn->prepare("SELECT COALESCE(SUM(cah_decesos_cantidad), 0) as total_deaths FROM cah_decesos WHERE cah_decesos_tagid = :tagid");
        $stmt->bindParam(':tagid', $tagid_int, PDO::PARAM_INT);
        $stmt->execute();
        
        $deathResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalDeaths = (int) $deathResult['total_deaths'];
        
        // Calculate total sales (cah_ventas_tagid is int according to schema)
        $stmt = $conn->prepare("SELECT COALESCE(SUM(cah_ventas_cantidad), 0) as total_sales FROM cah_ventas WHERE cah_ventas_tagid = :tagid");
        $stmt->bindParam(':tagid', $tagid_int, PDO::PARAM_INT);
        $stmt->execute();
        
        $salesResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalSales = (int) $salesResult['total_sales'];
        
        // Calculate available population
        $availablePopulation = $initialPopulation - $totalDeaths - $totalSales;
        
        // Debug logging
        error_log("Population Debug - TagID: $tagid_str, Initial: $initialPopulation, Deaths: $totalDeaths, Sales: $totalSales, Available: $availablePopulation");
        
        return [
            'initial_population' => $initialPopulation,
            'total_deaths' => $totalDeaths,
            'total_sales' => $totalSales,
            'available_population' => max(0, $availablePopulation), // Ensure it's not negative
            'debug_info' => [
                'tagid_str' => $tagid_str,
                'tagid_int' => $tagid_int,
                'calculation' => "$initialPopulation - $totalDeaths - $totalSales = $availablePopulation"
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception('Error al calcular la población disponible: ' . $e->getMessage());
    }
}

function handleInsert($conn) {
    try {
        // Validate required fields
        $requiredFields = ['tagid', 'fecha', 'presentacion', 'camarones', 'precio', 'peso'];
        validateRequiredFields($_POST, $requiredFields);
        
        // Sanitize and validate input data
        $tagid = trim($_POST['tagid']);
        $fecha = trim($_POST['fecha']);
        $presentacion = trim($_POST['presentacion']);
        $camarones = (int) $_POST['camarones'];
        $precio = (float) $_POST['precio'];
        $peso = (float) $_POST['peso'];
        $talla = trim($_POST['talla'] ?? '');
        
        // Validate numeric fields
        if ($camarones <= 0) {
            throw new Exception('La cantidad de camarones debe ser mayor a 0.');
        }
        
        if ($precio < 0) {
            throw new Exception('El precio no puede ser negativo.');
        }
        
        if ($peso <= 0) {
            throw new Exception('El peso debe ser mayor a 0.');
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
            throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD.');
        }
        
        // Get population information and validate
        $populationInfo = getAvailablePopulation($conn, $tagid);
        
        if ($camarones > $populationInfo['available_population']) {
            echo json_encode([
                'success' => false,
                'message' => "No puedes vender $camarones camarones. Solo hay {$populationInfo['available_population']} camarones disponibles.",
                'error_type' => 'validation_error',
                'population_info' => $populationInfo
            ]);
            return;
        }
        
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        try {
            // Insert the sales record
            $stmt = $conn->prepare("
                INSERT INTO cah_ventas (
                    cah_ventas_tagid, 
                    cah_ventas_fecha, 
                    cah_ventas_presentacion, 
                    cah_ventas_cantidad, 
                    cah_ventas_precio, 
                    cah_ventas_peso, 
                    cah_ventas_talla
                ) VALUES (
                    :tagid, 
                    :fecha, 
                    :presentacion, 
                    :cantidad, 
                    :precio, 
                    :peso, 
                    :talla
                )
            ");
            
            // Convert tagid to int for cah_ventas table (cah_ventas_tagid is int)
            $tagid_int = (int) $tagid;
            $stmt->bindParam(':tagid', $tagid_int, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':presentacion', $presentacion, PDO::PARAM_STR);
            $stmt->bindParam(':cantidad', $camarones, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
            $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
            $stmt->bindParam(':talla', $talla, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al insertar el registro de venta en la base de datos.');
            }
            
            // Update population in camarones table by reducing the sold quantity
            $stmt = $conn->prepare("UPDATE camarones SET poblacion = poblacion - :cantidad WHERE tagid = :tagid");
            $stmt->bindParam(':cantidad', $camarones, PDO::PARAM_INT);
            $stmt->bindParam(':tagid', $tagid, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar la población del estanque.');
            }
            
            // Check if the update affected any rows
            if ($stmt->rowCount() === 0) {
                throw new Exception('No se pudo actualizar la población. Verifique que el estanque existe.');
            }
            
            // Commit transaction
            $conn->commit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
        // Calculate new available population after this sale
        $newAvailablePopulation = $populationInfo['available_population'] - $camarones;
        
        echo json_encode([
            'success' => true,
            'message' => 'Registro de venta guardado exitosamente.',
            'inserted_id' => $conn->lastInsertId(),
            'population_info' => [
                'initial_population' => $populationInfo['initial_population'],
                'total_deaths' => $populationInfo['total_deaths'],
                'total_sales' => $populationInfo['total_sales'] + $camarones,
                'available_population' => $newAvailablePopulation
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en inserción: ' . $e->getMessage());
    }
}

function handleUpdate($conn) {
    try {
        // Validate required fields for update
        $requiredFields = ['id', 'tagid', 'fecha', 'presentacion', 'camarones', 'precio', 'peso'];
        validateRequiredFields($_POST, $requiredFields);
        
        // Sanitize and validate input data
        $id = (int) $_POST['id'];
        $tagid = trim($_POST['tagid']);
        $fecha = trim($_POST['fecha']);
        $presentacion = trim($_POST['presentacion']);
        $camarones = (int) $_POST['camarones'];
        $precio = (float) $_POST['precio'];
        $peso = (float) $_POST['peso'];
        $talla = trim($_POST['talla'] ?? '');
        
        // Validate ID
        if ($id <= 0) {
            throw new Exception('ID de registro inválido.');
        }
        
        // Validate numeric fields
        if ($camarones <= 0) {
            throw new Exception('La cantidad de camarones debe ser mayor a 0.');
        }
        
        if ($precio < 0) {
            throw new Exception('El precio no puede ser negativo.');
        }
        
        if ($peso <= 0) {
            throw new Exception('El peso debe ser mayor a 0.');
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
            throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD.');
        }
        
        // Get current record to check previous quantity
        $stmt = $conn->prepare("SELECT cah_ventas_cantidad, cah_ventas_tagid FROM cah_ventas WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $currentRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$currentRecord) {
            throw new Exception('Registro de venta no encontrado.');
        }
        
        $previousQuantity = (int) $currentRecord['cah_ventas_cantidad'];
        $recordTagid = $currentRecord['cah_ventas_tagid'];
        
        // Validate that tagid matches (prevent moving sales between ponds)
        if ($recordTagid != $tagid) {
            throw new Exception('No se puede cambiar el ID del estanque en una venta existente.');
        }
        
        // Get population information
        $populationInfo = getAvailablePopulation($conn, $tagid);
        
        // Calculate available population considering we're updating (add back the previous quantity)
        $availableForUpdate = $populationInfo['available_population'] + $previousQuantity;
        
        if ($camarones > $availableForUpdate) {
            echo json_encode([
                'success' => false,
                'message' => "No puedes vender $camarones camarones. Solo hay $availableForUpdate camarones disponibles (incluyendo los {$previousQuantity} de este registro).",
                'error_type' => 'validation_error',
                'population_info' => $populationInfo,
                'previous_quantity' => $previousQuantity,
                'available_for_update' => $availableForUpdate
            ]);
            return;
        }
        
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        try {
            // Update the sales record
            $stmt = $conn->prepare("
                UPDATE cah_ventas SET 
                    cah_ventas_fecha = :fecha,
                    cah_ventas_presentacion = :presentacion,
                    cah_ventas_cantidad = :cantidad,
                    cah_ventas_precio = :precio,
                    cah_ventas_peso = :peso,
                    cah_ventas_talla = :talla
                WHERE id = :id
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':presentacion', $presentacion, PDO::PARAM_STR);
            $stmt->bindParam(':cantidad', $camarones, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
            $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
            $stmt->bindParam(':talla', $talla, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el registro de venta en la base de datos.');
            }
            
            // Calculate the difference in quantity to adjust population
            $quantityDifference = $camarones - $previousQuantity;
            
            // Update population in camarones table
            // If quantityDifference is positive, we're selling more (reduce population more)
            // If quantityDifference is negative, we're selling less (increase population back)
            if ($quantityDifference != 0) {
                $stmt = $conn->prepare("UPDATE camarones SET poblacion = poblacion - :cantidad WHERE tagid = :tagid");
                $stmt->bindParam(':cantidad', $quantityDifference, PDO::PARAM_INT);
                $stmt->bindParam(':tagid', $tagid, PDO::PARAM_STR);
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al actualizar la población del estanque.');
                }
                
                // Check if the update affected any rows
                if ($stmt->rowCount() === 0) {
                    throw new Exception('No se pudo actualizar la población. Verifique que el estanque existe.');
                }
            }
            
            // Commit transaction
            $conn->commit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
        // Calculate new available population after this update
        $quantityDifference = $camarones - $previousQuantity;
        $newAvailablePopulation = $populationInfo['available_population'] - $quantityDifference;
        
        echo json_encode([
            'success' => true,
            'message' => 'Registro de venta actualizado exitosamente.',
            'updated_id' => $id,
            'quantity_difference' => $quantityDifference,
            'population_info' => [
                'initial_population' => $populationInfo['initial_population'],
                'total_deaths' => $populationInfo['total_deaths'],
                'total_sales' => $populationInfo['total_sales'] + $quantityDifference,
                'available_population' => $newAvailablePopulation
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en actualización: ' . $e->getMessage());
    }
}

function handleDelete($conn) {
    try {
        // Validate required fields for delete
        $requiredFields = ['id'];
        validateRequiredFields($_POST, $requiredFields);
        
        // Sanitize input data
        $id = (int) $_POST['id'];
        
        // Validate ID
        if ($id <= 0) {
            throw new Exception('ID de registro inválido.');
        }
        
        // Get current record information before deleting
        $stmt = $conn->prepare("
            SELECT 
                cah_ventas_cantidad, 
                cah_ventas_tagid,
                cah_ventas_fecha,
                cah_ventas_presentacion
            FROM cah_ventas 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $currentRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$currentRecord) {
            throw new Exception('Registro de venta no encontrado.');
        }
        
        $quantity = (int) $currentRecord['cah_ventas_cantidad'];
        $tagid = $currentRecord['cah_ventas_tagid'];
        $fecha = $currentRecord['cah_ventas_fecha'];
        $presentacion = $currentRecord['cah_ventas_presentacion'];
        
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        try {
            // Delete the sales record
            $stmt = $conn->prepare("DELETE FROM cah_ventas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al eliminar el registro de venta de la base de datos.');
            }
            
            // Check if the delete affected any rows
            if ($stmt->rowCount() === 0) {
                throw new Exception('No se encontró el registro de venta para eliminar.');
            }
            
            // Restore population in camarones table by adding back the sold quantity
            $stmt = $conn->prepare("UPDATE camarones SET poblacion = poblacion + :cantidad WHERE tagid = :tagid");
            $stmt->bindParam(':cantidad', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':tagid', $tagid, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al restaurar la población del estanque.');
            }
            
            // Check if the update affected any rows
            if ($stmt->rowCount() === 0) {
                throw new Exception('No se pudo restaurar la población. Verifique que el estanque existe.');
            }
            
            // Commit transaction
            $conn->commit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw $e;
        }
        
        // Get updated population info
        $populationInfo = getAvailablePopulation($conn, $tagid);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registro de venta eliminado exitosamente. Se restauraron ' . $quantity . ' camarones a la población.',
            'deleted_id' => $id,
            'restored_quantity' => $quantity,
            'sale_info' => [
                'fecha' => $fecha,
                'presentacion' => $presentacion,
                'cantidad' => $quantity,
                'tagid' => $tagid
            ],
            'population_info' => [
                'initial_population' => $populationInfo['initial_population'],
                'total_deaths' => $populationInfo['total_deaths'],
                'total_sales' => $populationInfo['total_sales'],
                'available_population' => $populationInfo['available_population']
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en eliminación: ' . $e->getMessage());
    }
}
?>

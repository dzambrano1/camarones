<?php
require_once './pdo_conexion.php';

// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Set content type to JSON
header('Content-Type: application/json');

/**
 * Get current population data for a specific tank
 */
function getCurrentPopulationData($conn, $tagid) {
    $sql = "SELECT poblacion, poblacion_actual, supervivencia FROM camarones WHERE tagid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tagid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Calculate total deaths for a specific tank
 */
function getTotalDeaths($conn, $tagid) {
    $sql = "SELECT SUM(cah_decesos_cantidad) as total_deaths FROM cah_decesos WHERE cah_decesos_tagid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tagid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return intval($result['total_deaths'] ?? 0);
}

/**
 * Update poblacion_actual and supervivencia in camarones table
 */
function updatePopulationAndSurvival($conn, $tagid) {
    // Get initial population
    $populationData = getCurrentPopulationData($conn, $tagid);
    if (!$populationData) {
        throw new Exception("No se encontró el estanque con tagid: $tagid");
    }
    
    $initialPopulation = intval($populationData['poblacion']);
    $totalDeaths = getTotalDeaths($conn, $tagid);
    
    // Calculate current population and survival rate
    $currentPopulation = $initialPopulation - $totalDeaths;
    $survivalRate = $initialPopulation > 0 ? (($currentPopulation / $initialPopulation) * 100) : 0;
    
    // Update camarones table
    $sql = "UPDATE camarones SET 
            poblacion_actual = ?, 
            supervivencia = ? 
            WHERE tagid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$currentPopulation, $survivalRate, $tagid]);
    
    return [
        'initial_population' => $initialPopulation,
        'current_population' => $currentPopulation,
        'total_deaths' => $totalDeaths,
        'survival_rate' => $survivalRate
    ];
}

try {
    // Get action parameter
    $action = $_POST['action'] ?? '';

    // Get form data for death record
    $tagid = $_POST['tagid'] ?? '';
    $causa = $_POST['causa'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $id = $_POST['id'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $peso = $_POST['peso'] ?? '';
    $cantidad = $_POST['cantidad'] ?? '';
    $poblacion = $_POST['poblacion'] ?? '';
    $supervivencia = $_POST['supervivencia'] ?? '';

    // Debug logging
    error_log("POST data received: " . print_r($_POST, true));
    error_log("Action: '$action', TagID: '$tagid', Causa: '$causa', Fecha: '$fecha', ID: '$id', Precio: '$precio', Peso: '$peso', Cantidad: '$cantidad', Poblacion: '$poblacion', Supervivencia: '$supervivencia'");

    // Validate required fields
    if (empty($action)) {
        throw new Exception("Acción no válida o datos no proporcionados");
    }

    // Handle different operations
    switch ($action) {
        case 'insert':
            // Validate required fields for insert
            if (empty($tagid) || empty($causa) || empty($fecha) || empty($precio) || empty($peso) || empty($cantidad)) {
                throw new Exception("Tag ID, causa, fecha, precio, peso y cantidad son requeridos para registrar un deceso");
            }
            
            // Get current population data
            $populationData = getCurrentPopulationData($conn, $tagid);
            if (!$populationData) {
                throw new Exception("No se encontró un estanque con el Tag ID especificado");
            }
            
            $currentPopulationActual = intval($populationData['poblacion_actual'] ?? $populationData['poblacion']);
            $cantidadDeceso = intval($cantidad);
            
            // Validate that death quantity doesn't exceed current population
            if ($currentPopulationActual < $cantidadDeceso) {
                throw new Exception("No puedes registrar $cantidadDeceso decesos. Solo hay $currentPopulationActual camarones disponibles en la población actual.");
            }
            
            // Begin transaction
            $conn->beginTransaction();
            
            try {
                // Insert into cah_decesos table
                $insertDecesoSql = "INSERT INTO cah_decesos (
                    cah_decesos_tagid,
                    cah_decesos_fecha,
                    cah_decesos_causa,
                    cah_decesos_precio,
                    cah_decesos_peso,
                    cah_decesos_cantidad
                ) VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($insertDecesoSql);
                $stmt->execute([$tagid, $fecha, $causa, floatval($precio), floatval($peso), $cantidadDeceso]);

                // Update poblacion_actual and supervivencia automatically
                $updatedData = updatePopulationAndSurvival($conn, $tagid);

                // Commit transaction
                $conn->commit();
                
                echo json_encode([
                    "success" => true,
                    "message" => "Deceso registrado exitosamente",
                    "population_info" => [
                        "initial_population" => $updatedData['initial_population'],
                        "current_population" => $updatedData['current_population'],
                        "total_deaths" => $updatedData['total_deaths'],
                        "survival_rate" => round($updatedData['survival_rate'], 2)
                    ]
                ]);
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollBack();
                throw $e;
            }
            break;
            
        case 'update':
            // Validate required fields for update
            if (empty($id) || empty($causa) || empty($fecha) || empty($precio) || empty($peso) || empty($cantidad)) {
                throw new Exception("ID, causa, fecha, precio, peso y cantidad son requeridos para actualizar un deceso");
            }
            
            // Begin transaction
            $conn->beginTransaction();
            
            try {
                // Get current death record information from cah_decesos
                $checkSql = "SELECT cah_decesos_tagid, cah_decesos_cantidad FROM cah_decesos WHERE id = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute([$id]);
                $currentRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$currentRecord) {
                    throw new Exception("No se encontró un registro de deceso con el ID especificado");
                }
                
                $recordTagid = $currentRecord['cah_decesos_tagid'];
                $oldCantidad = intval($currentRecord['cah_decesos_cantidad']);
                $newCantidad = intval($cantidad);
                
                // Get current population data to validate
                $populationData = getCurrentPopulationData($conn, $recordTagid);
                if (!$populationData) {
                    throw new Exception("No se encontró el estanque asociado al registro de deceso");
                }
                
                // Calculate what the new population would be after this change
                $currentTotalDeaths = getTotalDeaths($conn, $recordTagid);
                $adjustedTotalDeaths = $currentTotalDeaths - $oldCantidad + $newCantidad;
                $initialPopulation = intval($populationData['poblacion']);
                $newPopulationActual = $initialPopulation - $adjustedTotalDeaths;
                
                // Validate that the new death count doesn't result in negative population
                if ($newPopulationActual < 0) {
                    throw new Exception("No puedes actualizar a $newCantidad decesos. Esto resultaría en una población negativa. Población inicial: $initialPopulation, Total de decesos resultante: $adjustedTotalDeaths");
                }

                // Update cah_decesos table using the record ID
                $updateDecesoSql = "UPDATE cah_decesos SET
                        cah_decesos_fecha = ?,
                        cah_decesos_causa = ?,
                        cah_decesos_precio = ?,
                        cah_decesos_peso = ?,
                        cah_decesos_cantidad = ?
                        WHERE id = ?";
                
                $stmt = $conn->prepare($updateDecesoSql);
                $stmt->execute([$fecha, $causa, floatval($precio), floatval($peso), $newCantidad, $id]);

                // Update poblacion_actual and supervivencia automatically
                $updatedData = updatePopulationAndSurvival($conn, $recordTagid);

                // Commit transaction
                $conn->commit();
                
                echo json_encode([
                    "success" => true,
                    "message" => "Deceso actualizado exitosamente",
                    "population_info" => [
                        "initial_population" => $updatedData['initial_population'],
                        "current_population" => $updatedData['current_population'],
                        "total_deaths" => $updatedData['total_deaths'],
                        "survival_rate" => round($updatedData['survival_rate'], 2),
                        "quantity_change" => ($newCantidad - $oldCantidad)
                    ]
                ]);
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollBack();
                throw $e;
            }
            break;
            
        case 'delete':
            // For delete, we need the id from cah_decesos table
            if (empty($id)) {
                throw new Exception("ID es requerido para eliminar un deceso");
            }
            
            // Begin transaction
            $conn->beginTransaction();
            
            try {
                // Get death record information before deletion
                $checkSql = "SELECT cah_decesos_tagid, cah_decesos_cantidad FROM cah_decesos WHERE id = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute([$id]);
                $deathRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$deathRecord) {
                    throw new Exception("No se encontró el registro de deceso");
                }
                
                $recordTagid = $deathRecord['cah_decesos_tagid'];
                $cantidadMuertos = intval($deathRecord['cah_decesos_cantidad']);
                
                // Delete from cah_decesos table
                $deleteDecesoSql = "DELETE FROM cah_decesos WHERE id = ?";
                $stmt = $conn->prepare($deleteDecesoSql);
                $stmt->execute([$id]);

                // Update poblacion_actual and supervivencia automatically
                $updatedData = updatePopulationAndSurvival($conn, $recordTagid);

                // Commit transaction
                $conn->commit();
                
                echo json_encode([
                    "success" => true,
                    "message" => "Deceso eliminado exitosamente. Se han restaurado $cantidadMuertos camarones a la población.",
                    "population_info" => [
                        "initial_population" => $updatedData['initial_population'],
                        "current_population" => $updatedData['current_population'],
                        "total_deaths" => $updatedData['total_deaths'],
                        "survival_rate" => round($updatedData['survival_rate'], 2),
                        "restored_quantity" => $cantidadMuertos
                    ]
                ]);
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollBack();
                throw $e;
            }
            break;
            
        default:
            throw new Exception("Acción no válida: $action");
    }

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
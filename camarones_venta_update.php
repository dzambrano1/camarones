<?php
require_once './pdo_conexion.php';

// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $action = $_POST['action'] ?? '';
    
                 if ($action === 'update') {
                // Validate required fields
                $tagid = trim($_POST['tagid'] ?? '');
                $precio_venta = floatval($_POST['precio_venta'] ?? 0);
                $peso_venta = floatval($_POST['peso_venta'] ?? 0);
                $camarones_vendidos = intval($_POST['camarones_Vendidos'] ?? 0);
                $presentacion = trim($_POST['presentacion'] ?? '');
                $fecha_venta = trim($_POST['fecha_venta'] ?? '');
                $estatus = trim($_POST['estatus'] ?? 'Vendido');
                
                if (empty($tagid) || $precio_venta <= 0 || $peso_venta <= 0 || $camarones_vendidos <= 0 || empty($fecha_venta) || empty($presentacion)) {
                    throw new Exception('Todos los campos son requeridos y deben tener valores válidos');
                }
        
        // Check if animal exists and get current population info
        $checkQuery = "SELECT tagid, poblacion FROM camarones WHERE tagid = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute([$tagid]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$animal) {
            throw new Exception('Camarón con Tag ID ' . $tagid . ' no encontrado');
        }
        
        // Get current sold quantity from cah_ventas table
        $ventasQuery = "SELECT cah_ventas_cantidad FROM cah_ventas WHERE cah_ventas_tagid = ?";
        $stmt = $conn->prepare($ventasQuery);
        $stmt->execute([$tagid]);
        $ventasData = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldCamaronesVendidos = $ventasData ? $ventasData['cah_ventas_cantidad'] : 0;
        
        // Calculate population adjustment
        $populationAdjustment = $oldCamaronesVendidos - $camarones_vendidos; // Positive if reducing, negative if increasing
        
        // Check if the new quantity would exceed available population
        $newPopulation = $animal['poblacion'] + $populationAdjustment;
        if ($newPopulation < 0) {
            throw new Exception('No hay suficientes camarones disponibles. La cantidad solicitada excedería la población disponible.');
        }
                
                // Calculate total amount: (precio * peso) * camarones_vendidos
                $totalAmount = ($precio_venta * $peso_venta) * $camarones_vendidos;
        
        // Begin transaction
        $conn->beginTransaction();
        
        try {
                        // Update camarones table with new values
            $updateQuery = "UPDATE camarones SET 
                            estatus = ?
                           WHERE tagid = ?";
            
            $stmt = $conn->prepare($updateQuery);
            $stmt->execute([$estatus, $tagid]);
            
                                 // Update cah_ventas table with new values including quantity and presentacion
                     $updateVentasQuery = "UPDATE cah_ventas SET 
                                             cah_ventas_precio = ?,
                                             cah_ventas_peso = ?,
                                             cah_ventas_cantidad = ?,
                                             cah_ventas_presentacion = ?,
                                             cah_ventas_fecha = ?
                                            WHERE cah_ventas_tagid = ?";
                     
                                          $stmt = $conn->prepare($updateVentasQuery);
                     $stmt->execute([$precio_venta, $peso_venta, $camarones_vendidos, $presentacion, $fecha_venta, $tagid]);
                     
                     // Update population in camarones table to reflect the quantity change
                     $updatePopulationQuery = "UPDATE camarones SET poblacion = ? WHERE tagid = ?";
                     $stmt = $conn->prepare($updatePopulationQuery);
                     $stmt->execute([$newPopulation, $tagid]);
                     
                     // Commit transaction
                     $conn->commit();
            
                                 echo json_encode([
                         'success' => true,
                         'message' => 'Información de venta actualizada correctamente',
                         'total_amount' => $totalAmount,
                         'population_adjustment' => $populationAdjustment,
                         'new_population' => $newPopulation
                     ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            throw $e;
        }
        
    } elseif ($action === 'delete') {
        $tagid = trim($_POST['tagid'] ?? '');
        
        if (empty($tagid)) {
            throw new Exception('Tag ID es requerido');
        }
        
        // Check if animal exists
        $checkQuery = "SELECT tagid, poblacion FROM camarones WHERE tagid = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute([$tagid]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$animal) {
            throw new Exception('Camarón con Tag ID ' . $tagid . ' no encontrado');
        }
        
        // Get current sold quantity from cah_ventas table
        $ventasQuery = "SELECT cah_ventas_cantidad FROM cah_ventas WHERE cah_ventas_tagid = ?";
        $stmt = $conn->prepare($ventasQuery);
        $stmt->execute([$tagid]);
        $ventasData = $stmt->fetch(PDO::FETCH_ASSOC);
        $camaronesVendidos = $ventasData ? $ventasData['cah_ventas_cantidad'] : 0;
        
        // Begin transaction
        $conn->beginTransaction();
        
        try {
            // Restore population and clear sale information in camarones table
            $restorePopulation = $animal['poblacion'] + $camaronesVendidos;
            
            $updateQuery = "UPDATE camarones SET 
                            poblacion = ?,
                            estatus = 'Activo'
                           WHERE tagid = ?";
            
            $stmt = $conn->prepare($updateQuery);
            $stmt->execute([$restorePopulation, $tagid]);
            
            // Delete from cah_ventas table
            $deleteQuery = "DELETE FROM cah_ventas WHERE cah_ventas_tagid = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->execute([$tagid]);
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Registro de venta eliminado correctamente. El animal ha vuelto a estar disponible para venta.',
                'restored_population' => $restorePopulation
            ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            throw $e;
        }
        
    } else {
        throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error in camarones_venta_update.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
<?php
require_once './pdo_conexion.php';

// Set header for JSON response
header('Content-Type: application/json');

try {
    // Test data for ammonia measurements
    $testData = [
        ['pond_id' => '001', 'total_ammonia_mg_l' => 0.5, 'nh3_mg_l' => 0.02, 'source' => 'espectrofotometro', 'phase' => 'juvenile', 'timestamp' => '2024-01-15 08:00:00'],
        ['pond_id' => '002', 'total_ammonia_mg_l' => 1.2, 'nh3_mg_l' => 0.05, 'source' => 'kit_test', 'phase' => 'growout', 'timestamp' => '2024-01-15 12:30:00'],
        ['pond_id' => '003', 'total_ammonia_mg_l' => 0.3, 'nh3_mg_l' => 0.01, 'source' => 'laboratorio', 'phase' => 'PL', 'timestamp' => '2024-01-15 16:45:00'],
        ['pond_id' => '001', 'total_ammonia_mg_l' => 0.8, 'nh3_mg_l' => 0.03, 'source' => 'sensor', 'phase' => 'juvenile', 'timestamp' => '2024-01-16 09:15:00'],
        ['pond_id' => '004', 'total_ammonia_mg_l' => 1.0, 'nh3_mg_l' => 0.04, 'source' => 'manual', 'phase' => 'preharvest', 'timestamp' => '2024-01-16 14:20:00']
    ];
    
    // Insert test data
    $insertQuery = "INSERT INTO water_ammonia_log (pond_id, total_ammonia_mg_l, nh3_mg_l, source, phase, timestamp) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    $insertedCount = 0;
    foreach ($testData as $record) {
        try {
            $stmt->execute([
                $record['pond_id'],
                $record['total_ammonia_mg_l'],
                $record['nh3_mg_l'],
                $record['source'],
                $record['phase'],
                $record['timestamp']
            ]);
            $insertedCount++;
        } catch (PDOException $e) {
            // Skip if record already exists (duplicate entry)
            if ($e->getCode() != 23000) {
                throw $e;
            }
        }
    }
    
    // Get all records to verify
    $selectQuery = "SELECT * FROM water_ammonia_log ORDER BY timestamp DESC";
    $stmt = $conn->prepare($selectQuery);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => "Test completado. Se insertaron $insertedCount nuevos registros.",
        'total_records' => count($results),
        'sample_data' => array_slice($results, 0, 3) // Show first 3 records
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error general: ' . $e->getMessage()
    ]);
}
?>

<?php
require_once './pdo_conexion.php';

// Set header for JSON response
header('Content-Type: application/json');

try {
    // Check if table already exists
    $checkTableQuery = "SHOW TABLES LIKE 'water_redox_log'";
    $stmt = $conn->prepare($checkTableQuery);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'La tabla water_redox_log ya existe'
        ]);
        exit;
    }
    
    // Create the table
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS `water_redox_log` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pond_id` varchar(50) NOT NULL,
        `redox_mv` decimal(6,2) NOT NULL,
        `source` varchar(50) NOT NULL,
        `timestamp` datetime NOT NULL,
        `phase` varchar(50) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_pond_id` (`pond_id`),
        KEY `idx_timestamp` (`timestamp`),
        KEY `idx_phase` (`phase`),
        KEY `idx_source` (`source`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $stmt = $conn->prepare($createTableQuery);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tabla water_redox_log creada exitosamente'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la tabla: ' . $e->getMessage()
    ]);
}
?>

<?php
require_once './pdo_conexion.php';

// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }

    $tagid = trim($_GET['tagid'] ?? '');
    
    if (empty($tagid)) {
        throw new Exception('Tag ID es requerido');
    }
    
    // Get population count for the specified tagid
    $query = "SELECT poblacion FROM camarones WHERE tagid = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$tagid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'poblacion' => intval($result['poblacion'])
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Animal con Tag ID ' . $tagid . ' no encontrado'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error in get_poblacion_count.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

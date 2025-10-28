<?php
require_once './pdo_conexion.php';
require_once './update_edad_helper.php';

// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get specific tagid if provided
    $tagid = $_GET['tagid'] ?? $_POST['tagid'] ?? null;
    
    // Update ages using helper function
    $updateResult = updateCamaronesEdad($conn, $tagid);
    
    if ($updateResult['success']) {
        // Get detailed information about updated records
        $infoResult = getCamaronesEdadInfo($conn, $tagid);
        
        echo json_encode([
            "success" => true,
            "message" => $updateResult['message'],
            "affected_rows" => $updateResult['affected_rows'],
            "updated_records" => $infoResult['records'] ?? [],
            "total_records" => $infoResult['total_records'] ?? 0,
            "update_timestamp" => $updateResult['update_timestamp'],
            "tagid_filter" => $tagid
        ]);
    } else {
        echo json_encode($updateResult);
    }
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar edades: " . $e->getMessage()
    ]);
}
?>

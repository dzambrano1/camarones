<?php
header('Content-Type: application/json');
require_once './pdo_conexion.php'; // Ensure this path is correct

$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if (isset($_GET['tagid']) && !empty($_GET['tagid'])) {
    $tagid = $_GET['tagid'];

    try {
        // Check if we're getting a specific purchase record ID
        $compra_id = $_GET['compra_id'] ?? null;
        
        if ($compra_id) {
            // Get specific purchase record with animal details
            $sql = "SELECT 
                        a.id, a.tagid, a.nombre, DATE_FORMAT(a.fecha_nacimiento, '%Y-%m-%d') as fecha_nacimiento, 
                        DATE_FORMAT(a.fecha_compra, '%Y-%m-%d') as fecha_compra, a.genero, a.raza, 
                        a.etapa, a.grupo, a.estatus, a.image, a.image2, a.image3, a.video, a.poblacion,
                        c.id as compra_id, c.ah_compra_cantidad, c.ah_compra_peso, c.ah_compra_precio,
                        DATE_FORMAT(c.ah_compra_fecha, '%Y-%m-%d') as ah_compra_fecha
                    FROM cah_compras c
                    INNER JOIN camarones a ON c.cah_compras_tagid = a.tagid
                    WHERE c.id = :compra_id";
        } else {
            // Get latest purchase record for the tagid
            $sql = "SELECT 
                        a.id, a.tagid, a.nombre, DATE_FORMAT(a.fecha_nacimiento, '%Y-%m-%d') as fecha_nacimiento, 
                        DATE_FORMAT(a.fecha_compra, '%Y-%m-%d') as fecha_compra, a.genero, a.raza, 
                        a.etapa, a.grupo, a.estatus, a.image, a.image2, a.image3, a.video, a.poblacion,
                        c.id as compra_id, c.ah_compra_cantidad, c.ah_compra_peso, c.ah_compra_precio,
                        DATE_FORMAT(c.ah_compra_fecha, '%Y-%m-%d') as ah_compra_fecha
                    FROM camarones a
                    LEFT JOIN cah_compras c ON a.tagid = c.cah_compras_tagid
                    WHERE a.tagid = :tagid
                    ORDER BY c.id DESC LIMIT 1";
        }
        
        $stmt = $conn->prepare($sql);
        
        // Check if statement preparation was successful
        if ($stmt instanceof PDOStatement) {
            if ($compra_id) {
                $stmt->bindParam(':compra_id', $compra_id, PDO::PARAM_STR);
            } else {
                $stmt->bindParam(':tagid', $tagid, PDO::PARAM_STR);
            }
            $stmt->execute(); // Execute should throw PDOException on failure
            
            // Fetch the data. fetch() should be called on a PDOStatement.
            $animalData = $stmt->fetch(PDO::FETCH_ASSOC); 
            
            if ($animalData === false) { // Check if fetch returned false (no rows found)
                $response = ['success' => false, 'message' => 'Animal con Tag ID ' . htmlspecialchars($tagid) . ' no encontrado.'];
            } else {
                $response = ['success' => true, 'data' => $animalData];
            }
        } else {
             // This path indicates prepare() failed, which should have thrown an exception
             // with ERRMODE_EXCEPTION set, but we add it for robustness.
            throw new Exception("Falló la preparación de la consulta SQL.");
        }

    } catch (PDOException $e) {
        error_log("Database Error in camarones_get_details.php: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Error al consultar la base de datos: ' . $e->getMessage()];
    } catch (Exception $e) {
        error_log("General Error in camarones_get_details.php: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()];
    }

} else {
    $response = ['success' => false, 'message' => 'Tag ID no proporcionado.'];
}

$conn = null;
echo json_encode($response);
exit;

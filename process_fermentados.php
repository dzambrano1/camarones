<?php
require_once './pdo_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = array();
    
    // Debug: Log all POST data
    error_log("Harinas Process - Action: " . $_POST['action']);
    error_log("Harinas Process - POST data: " . print_r($_POST, true));
    
    if ($_POST['action'] === 'insert' && isset($_POST['tagid'], $_POST['racion'], $_POST['producto'], $_POST['etapa'], $_POST['costo'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO cah_fermentados (cah_fermentados_tagid, cah_fermentados_racion, cah_fermentados_producto, cah_fermentados_etapa, cah_fermentados_costo, cah_fermentados_fecha_inicio, cah_fermentados_fecha_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['tagid'],
                $_POST['racion'],
                $_POST['producto'],
                $_POST['etapa'],
                $_POST['costo'],
                $_POST['fecha_inicio'],
                $_POST['fecha_fin']
            ]);
            
            $response = array(
                'success' => true,
                'message' => 'Registro de harina agregado correctamente',
                'redirect' => 'camarones_register_fermentados.php'
            );
            
        } catch (PDOException $e) {
            $response = array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM cah_fermentados WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            if ($stmt->rowCount() > 0) {
                $response = array(
                    'success' => true,
                    'message' => 'Registro de harina eliminado correctamente',
                    'redirect' => 'camarones_register_fermentados.php'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'No se encontró el registro a eliminar'
                );
            }
            
        } catch (PDOException $e) {
            $response = array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    } elseif ($_POST['action'] === 'update' && isset($_POST['id'], $_POST['tagid'], $_POST['racion'], $_POST['producto'], $_POST['etapa'], $_POST['costo'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
        try {
            $stmt = $conn->prepare("UPDATE cah_fermentados SET cah_fermentados_tagid = ?, cah_fermentados_racion = ?, cah_fermentados_producto = ?, cah_fermentados_etapa = ?, cah_fermentados_costo = ?, cah_fermentados_fecha_inicio = ?, cah_fermentados_fecha_fin = ? WHERE id = ?");
            $stmt->execute([
                $_POST['tagid'],
                $_POST['racion'],
                $_POST['producto'],
                $_POST['etapa'],
                $_POST['costo'],
                $_POST['fecha_inicio'],
                $_POST['fecha_fin'],
                $_POST['id']
            ]);
            
            $response = array(
                'success' => true,
                'message' => 'Registro de harina actualizado correctamente',
                'redirect' => 'camarones_register_fermentados.php'
            );
            
        } catch (PDOException $e) {
            $response = array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Acción no válida o datos no proporcionados'
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If we get here, something went wrong
header('Content-Type: application/json');
echo json_encode(array(
    'success' => false,
    'message' => 'Solicitud no válida'
));
?>

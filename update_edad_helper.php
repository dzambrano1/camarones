<?php
/**
 * Helper function to update edad column in camarones table
 * This function calculates and updates the age in days from fecha_nacimiento to current date
 * 
 * @param PDO $conn Database connection
 * @param string|null $tagid Optional - update specific tagid only, null for all records
 * @return array Result information
 */
function updateCamaronesEdad($conn, $tagid = null) {
    try {
        $sql = "UPDATE camarones 
                SET edad = TIMESTAMPDIFF(DAY, fecha_nacimiento, CURDATE()) 
                WHERE fecha_nacimiento IS NOT NULL";
        
        $params = [];
        
        // If specific tagid is provided, add WHERE condition
        if ($tagid !== null) {
            $sql .= " AND tagid = ?";
            $params[] = $tagid;
        }
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        
        $affectedRows = $stmt->rowCount();
        
        return [
            'success' => true,
            'affected_rows' => $affectedRows,
            'message' => "Edades actualizadas para $affectedRows registros",
            'update_timestamp' => date('Y-m-d H:i:s')
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Error al actualizar edades: ' . $e->getMessage()
        ];
    }
}

/**
 * Get age information for specific tagid or all records
 * 
 * @param PDO $conn Database connection
 * @param string|null $tagid Optional - get specific tagid only, null for all records
 * @return array Age information
 */
function getCamaronesEdadInfo($conn, $tagid = null) {
    try {
        $sql = "SELECT tagid, nombre, fecha_nacimiento, edad,
                TIMESTAMPDIFF(DAY, fecha_nacimiento, CURDATE()) AS calculated_days,
                CASE 
                    WHEN edad = TIMESTAMPDIFF(DAY, fecha_nacimiento, CURDATE()) THEN 'Actualizada'
                    ELSE 'Desactualizada'
                END AS status
                FROM camarones 
                WHERE fecha_nacimiento IS NOT NULL";
        
        $params = [];
        
        if ($tagid !== null) {
            $sql .= " AND tagid = ?";
            $params[] = $tagid;
        }
        
        $sql .= " ORDER BY tagid ASC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'records' => $records,
            'total_records' => count($records)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
?>

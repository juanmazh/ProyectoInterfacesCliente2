<?php
include 'db.php';

// Obtener asignaciones por guía o por ruta
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['guia_id'])) {
        $guia_id = $_GET['guia_id'];
        $result = $conn->query("SELECT * FROM asignaciones WHERE guia_id = $guia_id");
    } elseif (isset($_GET['ruta_id'])) {
        $ruta_id = $_GET['ruta_id'];
        $result = $conn->query("SELECT * FROM asignaciones WHERE ruta_id = $ruta_id");
    } elseif  (isset($_GET['fecha'])) {
        $fecha = $_GET['fecha'];

        // Consulta para obtener guías disponibles en la fecha dada
        $query = "
            SELECT u.id, u.nombre, u.email
            FROM usuarios u
            WHERE u.rol = 'guia'
            AND u.id NOT IN (
                SELECT a.guia_id
                FROM asignaciones a
                JOIN rutas r ON a.ruta_id = r.id
                WHERE r.fecha = ?
            )
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();

        $guias = [];
        while ($row = $result->fetch_assoc()) {
            $guias[] = $row;
        }

        echo json_encode($guias);
        $conn->close();

    } 

    else {
        $result = $conn->query("SELECT * FROM asignaciones");
    }

    $asignaciones = [];
    while ($row = $result->fetch_assoc()) {
        $asignaciones[] = $row;
    }
    echo json_encode($asignaciones);
}

// Crear una nueva asignación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ruta_id = $data['ruta_id'];
    $guia_id = $data['guia_id'];

    $query = "INSERT INTO asignaciones (ruta_id, guia_id) VALUES ($ruta_id, $guia_id)";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Asignación creada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear asignación']);
    }
}

// Eliminar una asignación
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM asignaciones WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Asignación eliminada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar asignación']);
    }
}



?>

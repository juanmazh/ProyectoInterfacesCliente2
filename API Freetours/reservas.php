<?php
include 'db.php';

// Obtener todas las reservas o reservas específicas de un usuario
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        $result = $conn->query("SELECT * FROM reservas WHERE user_id = $user_id");
    } else {
        $result = $conn->query("SELECT * FROM reservas");
    }

    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    echo json_encode($reservas);
}

// Crear una nueva reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'];
    $ruta_id = $data['ruta_id'];
    $num_personas = $data['num_personas'];

    $query = "INSERT INTO reservas (user_id, ruta_id, num_personas) 
              VALUES ($user_id, $ruta_id, $num_personas)";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Reserva creada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear reserva']);
    }
}

// Actualizar una reserva existente
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $num_personas = $data['num_personas'];

    $query = "UPDATE reservas SET num_personas = $num_personas WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Reserva actualizada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar reserva']);
    }
}

// Eliminar una reserva
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM reservas WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Reserva eliminada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar reserva']);
    }
}
?>

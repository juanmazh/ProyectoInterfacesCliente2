<?php
include 'db.php';

// Obtener todas las rutas
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT * FROM rutas");
    $rutas = [];
    while ($row = $result->fetch_assoc()) {
        $rutas[] = $row;
    }
    echo json_encode($rutas);
}

// Crear una nueva ruta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $titulo = $data['titulo'];
    $localidad = $data['localidad'];
    $descripcion = $data['descripcion'];
    $foto = $data['foto'];
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $latitud = $data['latitud'];
    $longitud = $data['longitud'];

    $query = "INSERT INTO rutas (titulo, localidad, descripcion, foto, fecha, hora, latitud, longitud) 
              VALUES ('$titulo', '$localidad', '$descripcion', '$foto', '$fecha', '$hora', '$latitud', '$longitud')";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Ruta creada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear ruta']);
    }
}

// Actualizar una ruta
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $titulo = $data['titulo'];
    $localidad = $data['localidad'];
    $descripcion = $data['descripcion'];
    $foto = $data['foto'];
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $latitud = $data['latitud'];
    $longitud = $data['longitud'];

    $query = "UPDATE rutas SET titulo = '$titulo', localidad = '$localidad', descripcion = '$descripcion', 
              foto = '$foto', fecha = '$fecha', hora = '$hora', latitud = '$latitud', longitud = '$longitud' WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Ruta actualizada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar ruta']);
    }
}

// Eliminar una ruta
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM rutas WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Ruta eliminada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar ruta']);
    }
}
?>

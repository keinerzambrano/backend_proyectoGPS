<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "conexion.php";

$metodo = $_SERVER['REQUEST_METHOD'];

/*
|--------------------------------------------------------------------------
| CONSULTAR PEDIDOS
|--------------------------------------------------------------------------
*/

if ($metodo === 'GET') {

    $sql = "SELECT 
                id_pedido,
                cliente,
                origen,
                destino,
                vehiculo,
                estado,
                fecha
            FROM pedidos
            ORDER BY id_pedido DESC";

    $resultado = $conn->query($sql);

    if (!$resultado) {

        echo json_encode([
            "success" => false,
            "message" => "Error al consultar pedidos"
        ]);

        exit();
    }

    $pedidos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $pedidos[] = $fila;
    }

    echo json_encode([
        "success" => true,
        "pedidos" => $pedidos
    ]);

    exit();
}


/*
|--------------------------------------------------------------------------
| INSERTAR PEDIDO
|--------------------------------------------------------------------------
*/

if ($metodo === 'POST') {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!$datos) {

        echo json_encode([
            "success" => false,
            "message" => "No se recibieron datos"
        ]);

        exit();
    }

    $cliente = trim($datos['cliente'] ?? '');
    $origen = trim($datos['origen'] ?? '');
    $destino = trim($datos['destino'] ?? '');
    $vehiculo = trim($datos['vehiculo'] ?? '');
    $estado = trim($datos['estado'] ?? 'Pendiente');
    $fecha = trim($datos['fecha'] ?? '');

    if (
        $cliente === '' ||
        $origen === '' ||
        $destino === '' ||
        $vehiculo === '' ||
        $estado === '' ||
        $fecha === ''
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Todos los campos son obligatorios"
        ]);

        exit();
    }

    $stmt = $conn->prepare("
        INSERT INTO pedidos
        (cliente, origen, destino, vehiculo, estado, fecha)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssss",
        $cliente,
        $origen,
        $destino,
        $vehiculo,
        $estado,
        $fecha
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Pedido insertado correctamente",
            "id_pedido" => $conn->insert_id
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al insertar pedido"
        ]);
    }

    $stmt->close();

    exit();
}


/*
|--------------------------------------------------------------------------
| EDITAR PEDIDO
|--------------------------------------------------------------------------
*/

if ($metodo === 'PUT') {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!$datos) {

        echo json_encode([
            "success" => false,
            "message" => "No se recibieron datos"
        ]);

        exit();
    }

    $id_pedido = intval($datos['id_pedido'] ?? 0);

    $cliente = trim($datos['cliente'] ?? '');
    $origen = trim($datos['origen'] ?? '');
    $destino = trim($datos['destino'] ?? '');
    $vehiculo = trim($datos['vehiculo'] ?? '');
    $estado = trim($datos['estado'] ?? '');
    $fecha = trim($datos['fecha'] ?? '');

    if ($id_pedido <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "ID de pedido inválido"
        ]);

        exit();
    }

    if (
        $cliente === '' ||
        $origen === '' ||
        $destino === '' ||
        $vehiculo === '' ||
        $estado === '' ||
        $fecha === ''
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Todos los campos son obligatorios"
        ]);

        exit();
    }

    $stmt = $conn->prepare("
        UPDATE pedidos
        SET cliente = ?,
            origen = ?,
            destino = ?,
            vehiculo = ?,
            estado = ?,
            fecha = ?
        WHERE id_pedido = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $cliente,
        $origen,
        $destino,
        $vehiculo,
        $estado,
        $fecha,
        $id_pedido
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Pedido actualizado correctamente"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar pedido"
        ]);
    }

    $stmt->close();

    exit();
}


/*
|--------------------------------------------------------------------------
| ELIMINAR PEDIDO
|--------------------------------------------------------------------------
*/

if ($metodo === 'DELETE') {

    $datos = json_decode(file_get_contents("php://input"), true);

    $id_pedido = intval($datos['id_pedido'] ?? 0);

    if ($id_pedido <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "ID de pedido inválido"
        ]);

        exit();
    }

    $stmt = $conn->prepare("
        DELETE FROM pedidos
        WHERE id_pedido = ?
    ");

    $stmt->bind_param("i", $id_pedido);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Pedido eliminado correctamente"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al eliminar pedido"
        ]);
    }

    $stmt->close();

    exit();
}


/*
|--------------------------------------------------------------------------
| MÉTODO NO PERMITIDO
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => false,
    "message" => "Método no permitido"
]);

?>
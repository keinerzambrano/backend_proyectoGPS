

<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// ======================================================
// OPTIONS
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}


// ======================================================
// CONEXIÓN
// ======================================================

require_once "conexion.php";


// ======================================================
// GET - CONSULTAR CLIENTES
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sql = "SELECT
                id_cliente,
                nombre_completo,
                correo_electronico,
                telefono,
                direccion_envio
            FROM clientes
            ORDER BY id_cliente DESC";

    $resultado = $conn->query($sql);

    if (!$resultado) {

        echo json_encode([
            "success" => false,
            "message" => "Error al consultar clientes: " . $conn->error
        ]);

        exit();
    }

    $clientes = [];

    while ($fila = $resultado->fetch_assoc()) {
        $clientes[] = $fila;
    }

    echo json_encode([
        "success" => true,
        "clientes" => $clientes
    ]);

    $conn->close();

    exit();
}


// ======================================================
// POST - INSERTAR CLIENTE
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $input = file_get_contents("php://input");

    $data = json_decode($input, true);


    if ($data === null) {

        echo json_encode([
            "success" => false,
            "message" => "No se recibieron datos JSON válidos"
        ]);

        exit();
    }


    $nombre = trim($data["nombre_completo"] ?? "");
    $correo = trim($data["correo_electronico"] ?? "");
    $telefono = trim($data["telefono"] ?? "");
    $direccion = trim($data["direccion_envio"] ?? "");


    // VALIDAR CAMPOS

    if (
        $nombre === "" ||
        $correo === "" ||
        $telefono === "" ||
        $direccion === ""
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Todos los campos son obligatorios"
        ]);

        exit();
    }


    // VALIDAR CORREO

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico no es válido"
        ]);

        exit();
    }


    // VERIFICAR CORREO REPETIDO

    $sqlVerificar = "
        SELECT id_cliente
        FROM clientes
        WHERE correo_electronico = ?
    ";

    $stmtVerificar = $conn->prepare($sqlVerificar);

    $stmtVerificar->bind_param("s", $correo);

    $stmtVerificar->execute();

    $stmtVerificar->store_result();


    if ($stmtVerificar->num_rows > 0) {

        $stmtVerificar->close();
        $conn->close();

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico ya está registrado"
        ]);

        exit();
    }


    $stmtVerificar->close();


    // INSERTAR

    $sql = "
        INSERT INTO clientes
        (
            nombre_completo,
            correo_electronico,
            telefono,
            direccion_envio
        )
        VALUES (?, ?, ?, ?)
    ";


    $stmt = $conn->prepare($sql);


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Error al preparar la inserción: " . $conn->error
        ]);

        exit();
    }


    $stmt->bind_param(
        "ssss",
        $nombre,
        $correo,
        $telefono,
        $direccion
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Cliente insertado correctamente",
            "id_cliente" => $stmt->insert_id
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al insertar: " . $stmt->error
        ]);
    }


    $stmt->close();
    $conn->close();

    exit();
}


// ======================================================
// PUT - EDITAR CLIENTE
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    $input = file_get_contents("php://input");

    $data = json_decode($input, true);


    if ($data === null) {

        echo json_encode([
            "success" => false,
            "message" => "Datos JSON inválidos"
        ]);

        exit();
    }


    $id = intval($data["id_cliente"] ?? 0);

    $nombre = trim($data["nombre_completo"] ?? "");
    $correo = trim($data["correo_electronico"] ?? "");
    $telefono = trim($data["telefono"] ?? "");
    $direccion = trim($data["direccion_envio"] ?? "");


    // VALIDAR ID

    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "ID de cliente inválido"
        ]);

        exit();
    }


    // VALIDAR CAMPOS

    if (
        $nombre === "" ||
        $correo === "" ||
        $telefono === "" ||
        $direccion === ""
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Todos los campos son obligatorios"
        ]);

        exit();
    }


    // VALIDAR CORREO

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico no es válido"
        ]);

        exit();
    }


    // VERIFICAR SI EL CORREO PERTENECE A OTRO CLIENTE

    $sqlVerificar = "
        SELECT id_cliente
        FROM clientes
        WHERE correo_electronico = ?
        AND id_cliente != ?
    ";


    $stmtVerificar = $conn->prepare($sqlVerificar);

    $stmtVerificar->bind_param(
        "si",
        $correo,
        $id
    );

    $stmtVerificar->execute();

    $stmtVerificar->store_result();


    if ($stmtVerificar->num_rows > 0) {

        $stmtVerificar->close();
        $conn->close();

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico ya pertenece a otro cliente"
        ]);

        exit();
    }


    $stmtVerificar->close();


    // ACTUALIZAR

    $sql = "
        UPDATE clientes
        SET
            nombre_completo = ?,
            correo_electronico = ?,
            telefono = ?,
            direccion_envio = ?
        WHERE id_cliente = ?
    ";


    $stmt = $conn->prepare($sql);


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Error al preparar la actualización"
        ]);

        exit();
    }


    $stmt->bind_param(
        "ssssi",
        $nombre,
        $correo,
        $telefono,
        $direccion,
        $id
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Cliente actualizado correctamente"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar: " . $stmt->error
        ]);
    }


    $stmt->close();
    $conn->close();

    exit();
}


// ======================================================
// DELETE - ELIMINAR CLIENTE
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    $input = file_get_contents("php://input");

    $data = json_decode($input, true);


    if ($data === null) {

        echo json_encode([
            "success" => false,
            "message" => "Datos JSON inválidos"
        ]);

        exit();
    }


    $id = intval($data["id_cliente"] ?? 0);


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "ID de cliente inválido"
        ]);

        exit();
    }


    // ELIMINAR

    $sql = "
        DELETE FROM clientes
        WHERE id_cliente = ?
    ";


    $stmt = $conn->prepare($sql);


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Error al preparar la eliminación"
        ]);

        exit();
    }


    $stmt->bind_param("i", $id);


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Cliente eliminado correctamente"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Error al eliminar: " . $stmt->error
        ]);
    }


    $stmt->close();
    $conn->close();

    exit();
}


// ======================================================
// MÉTODO NO PERMITIDO
// ======================================================

echo json_encode([
    "success" => false,
    "message" => "Método no permitido"
]);

$conn->close();

?>

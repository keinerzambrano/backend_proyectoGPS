<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

$usuario = trim($datos['usuario'] ?? '');
$password = $datos['password'] ?? '';
$nombre = trim($datos['nombre_completo'] ?? '');
$rol = trim($datos['rol'] ?? 'invitado');


if (
    $usuario === '' ||
    $password === '' ||
    $nombre === ''
) {

    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios"
    ]);

    exit();
}


if (!in_array($rol, ['administrador', 'invitado'])) {

    echo json_encode([
        "success" => false,
        "message" => "Rol inválido"
    ]);

    exit();
}


$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


$stmt = $conn->prepare("
    INSERT INTO usuarios
    (usuario, password, nombre_completo, rol)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "ssss",
    $usuario,
    $passwordHash,
    $nombre,
    $rol
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Usuario creado correctamente",
        "id_usuario" => $conn->insert_id
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "No se pudo crear el usuario"
    ]);
}

?>
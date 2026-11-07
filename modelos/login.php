<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "conexion.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);

    exit();
}


$datos = json_decode(file_get_contents("php://input"), true);

$usuario = trim($datos['usuario'] ?? '');
$password = $datos['password'] ?? '';


if ($usuario === '' || $password === '') {

    echo json_encode([
        "success" => false,
        "message" => "Usuario y contraseña son obligatorios"
    ]);

    exit();
}


/*
|--------------------------------------------------------------------------
| BUSCAR USUARIO
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id_usuario,
        usuario,
        password,
        nombre_completo,
        rol,
        estado
    FROM usuarios
    WHERE usuario = ?
    LIMIT 1
");

$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Usuario o contraseña incorrectos"
    ]);

    exit();
}


$user = $resultado->fetch_assoc();


/*
|--------------------------------------------------------------------------
| COMPROBAR ESTADO
|--------------------------------------------------------------------------
*/

if ($user['estado'] !== 'activo') {

    echo json_encode([
        "success" => false,
        "message" => "El usuario está inactivo"
    ]);

    exit();
}


/*
|--------------------------------------------------------------------------
| COMPROBAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

if (!password_verify($password, $user['password'])) {

    echo json_encode([
        "success" => false,
        "message" => "Contraseña incorrecta"
    ]);

    exit();
}

/*
|--------------------------------------------------------------------------
| CREAR TOKEN
|--------------------------------------------------------------------------
*/

$token = bin2hex(random_bytes(32));

$fechaExpiracion = date(
    'Y-m-d H:i:s',
    strtotime('+8 hours')
);


$stmtSesion = $conn->prepare("
    INSERT INTO sesiones
    (id_usuario, token, fecha_expiracion)
    VALUES (?, ?, ?)
");

$stmtSesion->bind_param(
    "iss",
    $user['id_usuario'],
    $token,
    $fechaExpiracion
);

$stmtSesion->execute();


/*
|--------------------------------------------------------------------------
| RESPUESTA
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "message" => "Inicio de sesión correcto",

    "usuario" => [
        "id_usuario" => $user['id_usuario'],
        "usuario" => $user['usuario'],
        "nombre_completo" => $user['nombre_completo'],
        "rol" => $user['rol']
    ],

    "token" => $token
]);

?>
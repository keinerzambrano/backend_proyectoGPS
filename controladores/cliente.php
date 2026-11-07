
<?php

require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/clientes.php';

$objCliente = new cliente($conexion);

$control = $_GET['control'] ?? $_POST['control'] ?? '';

switch ($control) {


case 'consulta':

    header('Content-Type: application/json; charset=utf-8');

    $respuesta = $objCliente->consulta();

    echo json_encode(
        $respuesta,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    break;

    case 'insertar':

        $respuesta = $objCliente->insertar(
            $_POST['nombre_completo'],
            $_POST['correo_electronico'],
            $_POST['telefono'],
            $_POST['direccion_envio']
        );

        echo json_encode($respuesta);
        break;

    case 'modificar':

        $respuesta = $objCliente->modificar(
            $_POST['id_cliente'],
            $_POST['nombre_completo'],
            $_POST['correo_electronico'],
            $_POST['telefono'],
            $_POST['direccion_envio']
        );

        echo json_encode($respuesta);
        break;

    case 'eliminar':

        $respuesta = $objCliente->eliminar(
            $_POST['id_cliente']
        );

        echo json_encode($respuesta);
        break;

    default:

        echo json_encode([
            "resultado" => "error",
            "mensaje" => "Acción no válida"
        ]);

        break;
}
?>

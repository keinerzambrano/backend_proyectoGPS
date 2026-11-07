
<?php

// CONECTAR MODELO Y BASE DE DATOS
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/pedido.php';

// CREAR OBJETO
$objPedido = new pedido($conexion);

// RECIBIR CONTROL
$control = $_GET['control'] ?? $_POST['control'] ?? '';

header('Content-Type: application/json; charset=utf-8');

switch ($control) {

    // CONSULTAR PEDIDOS
    case 'consulta':

        $respuesta = $objPedido->consulta();

        echo json_encode(
            $respuesta,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        break;


    // INSERTAR PEDIDO
    case 'insertar':

        $respuesta = $objPedido->insertar(
            $_POST['id_cliente'],
            $_POST['fecha_pedido'],
            $_POST['total_pedido']
        );

        echo json_encode($respuesta);
        break;


    // MODIFICAR PEDIDO
    case 'modificar':

        $respuesta = $objPedido->modificar(
            $_POST['id_pedido'],
            $_POST['id_cliente'],
            $_POST['fecha_pedido'],
            $_POST['total_pedido']
        );

        echo json_encode($respuesta);
        break;


    // ELIMINAR PEDIDO
    case 'eliminar':

        $respuesta = $objPedido->eliminar(
            $_POST['id_pedido']
        );

        echo json_encode($respuesta);
        break;


    // CONTROL NO RECONOCIDO
    default:

        echo json_encode([
            "resultado" => "error",
            "mensaje" => "Acción no válida"
        ]);

        break;
}

?>

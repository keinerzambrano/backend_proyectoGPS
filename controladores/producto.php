<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

require_once("../modelos/conexion.php");
require_once("../modelos/productos.php");

$control = $_GET['control'] ?? '';
$productos = new productos($conexion);

switch ($control) {


    case 'consulta':
    $resultado = $productos->consulta();
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    break;

    case 'eliminar':
        $id = $_GET['id'];
        $resultado = $productos->eliminar($id);
        echo json_encode($resultado);
        break;

    case 'insertar':
        $nombre_producto = $_POST['nombre_producto'];
        $descripcion_producto = $_POST['descripcion_producto'];

        $resultado = $productos->insertar(
            $nombre_producto,
            $descripcion_producto
        );

        echo json_encode($resultado);
        break;

    case 'modificar':
        $id_producto = $_POST['id_producto'];
        $nombre_producto = $_POST['nombre_producto'];
        $descripcion_producto = $_POST['descripcion_producto'];

        $resultado = $productos->modificar(
            $id_producto,
            $nombre_producto,
            $descripcion_producto
        );

        echo json_encode($resultado);
        break;

    default:
        echo json_encode([
            'error' => 'Control no válido'
        ]);
        break;
}
?>
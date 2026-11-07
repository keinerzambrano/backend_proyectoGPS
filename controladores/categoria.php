
<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// INCLUIR ARCHIVOS
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/categoria.php';

try {

    // CREAR OBJETO
    $objCategoria = new Categoria($conexion);

    // RECIBIR ACCIÓN
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? 'consultar';

    switch ($accion) {

        // CONSULTAR CATEGORÍAS
        case 'consultar':

            $resultado = $objCategoria->consulta();

            echo json_encode(
                $resultado,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            break;

        // INSERTAR CATEGORÍA
        case 'insertar':

            $nombre = trim($_POST['nombre_categoria'] ?? '');
            $descripcion = trim($_POST['descripcion_categoria'] ?? '');

            if ($nombre === '') {

                echo json_encode([
                    'resultado' => 'error',
                    'mensaje' => 'El nombre es obligatorio'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                break;
            }

            $resultado = $objCategoria->insertar($nombre, $descripcion);

            echo json_encode(
                $resultado,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            break;

        // MODIFICAR CATEGORÍA
        case 'modificar':

            $id = $_POST['id_categoria'] ?? '';
            $nombre = trim($_POST['nombre_categoria'] ?? '');
            $descripcion = trim($_POST['descripcion_categoria'] ?? '');

            if (!is_numeric($id) || (int)$id <= 0 || $nombre === '') {

                echo json_encode([
                    'resultado' => 'error',
                    'mensaje' => 'Datos no válidos'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                break;
            }

            $resultado = $objCategoria->modificar(
                (int)$id,
                $nombre,
                $descripcion
            );

            echo json_encode(
                $resultado,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            break;

        // ELIMINAR CATEGORÍA
        case 'eliminar':

            $id = $_POST['id_categoria'] ?? '';

            if (!is_numeric($id) || (int)$id <= 0) {

                echo json_encode([
                    'resultado' => 'error',
                    'mensaje' => 'ID no válido'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                break;
            }

            $resultado = $objCategoria->eliminar((int)$id);

            echo json_encode(
                $resultado,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            break;

        // ACCIÓN NO VÁLIDA
        default:

            http_response_code(400);

            echo json_encode([
                'resultado' => 'error',
                'mensaje' => 'Acción no válida'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            break;
    }

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'resultado' => 'error',
        'mensaje' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

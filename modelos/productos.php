<?php

class productos {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Consultar productos
public function consulta() {

    $sql = "SELECT * FROM productos ORDER BY id_producto DESC";

    $resultado = mysqli_query($this->conexion, $sql)
        or die("Error al consultar productos: " . mysqli_error($this->conexion));

    $vec = [];

    while ($row = mysqli_fetch_assoc($resultado)) {
        $vec[] = $row;
    }

    return $vec;
}

    // Eliminar producto
    public function eliminar($id) {

        $sql = "DELETE FROM productos WHERE id_producto = " . (int)$id;

        mysqli_query($this->conexion, $sql)
            or die("Error al eliminar el producto: " . mysqli_error($this->conexion));

        return [
            "resultado" => "ok",
            "mensaje" => "Producto eliminado correctamente"
        ];
    }

    // Insertar producto
    public function insertar($nombre_producto, $precio_unitario, $stock_disponible, $id_categoria) {

        $sql = "INSERT INTO productos
                (nombre_producto, precio_unitario, stock_disponible, id_categoria)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sdii",
            $nombre_producto,
            $precio_unitario,
            $stock_disponible,
            $id_categoria
        );

        mysqli_stmt_execute($stmt)
            or die("Error al insertar el producto: " . mysqli_error($this->conexion));

        mysqli_stmt_close($stmt);

        return [
            "resultado" => "ok",
            "mensaje" => "Producto insertado correctamente"
        ];
    }

    // Modificar producto
    public function modificar($id_producto, $nombre_producto, $precio_unitario, $stock_disponible, $id_categoria) {

        $sql = "UPDATE productos
                SET nombre_producto = ?,
                    precio_unitario = ?,
                    stock_disponible = ?,
                    id_categoria = ?
                WHERE id_producto = ?";

        $stmt = mysqli_prepare($this->conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sdiii",
            $nombre_producto,
            $precio_unitario,
            $stock_disponible,
            $id_categoria,
            $id_producto
        );

        mysqli_stmt_execute($stmt)
            or die("Error al modificar el producto: " . mysqli_error($this->conexion));

        mysqli_stmt_close($stmt);

        return [
            "resultado" => "ok",
            "mensaje" => "Producto modificado correctamente"
        ];
    }
}
?>
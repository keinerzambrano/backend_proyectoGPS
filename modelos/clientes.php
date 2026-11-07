<?php

class cliente {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }


// Consultar todos los clientes
public function consulta() {

    $sql = "SELECT
                id_cliente,
                nombre_completo,
                correo_electronico,
                telefono
            FROM clientes
            ORDER BY nombre_completo ASC";

    $resultado = mysqli_query($this->conexion, $sql);

    if (!$resultado) {
        die("Error al consultar clientes: " .
            mysqli_error($this->conexion));
    }

    $vec = [];

    while ($row = mysqli_fetch_assoc($resultado)) {
        $vec[] = $row;
    }

    return $vec;
}

    // Eliminar un cliente
    public function eliminar($id) {

        $sql = "DELETE FROM clientes WHERE id_cliente = ?";

        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $vec = [
            "resultado" => "ok",
            "mensaje" => "Cliente eliminado correctamente"
        ];

        mysqli_stmt_close($stmt);

        return $vec;
    }

    // Insertar un cliente
    public function insertar(
        $nombre_completo,
        $correo_electronico,
        $telefono,
        $direccion_envio
    ) {

        $sql = "INSERT INTO clientes
                (nombre_completo, correo_electronico,
                 telefono, direccion_envio)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nombre_completo,
            $correo_electronico,
            $telefono,
            $direccion_envio
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return [
            "resultado" => "ok",
            "mensaje" => "Cliente insertado correctamente"
        ];
    }

    // Modificar un cliente
    public function modificar(
        $id_cliente,
        $nombre_completo,
        $correo_electronico,
        $telefono,
        $direccion_envio
    ) {

        $sql = "UPDATE clientes
                SET nombre_completo = ?,
                    correo_electronico = ?,
                    telefono = ?,
                    direccion_envio = ?
                WHERE id_cliente = ?";

        $stmt = mysqli_prepare($this->conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nombre_completo,
            $correo_electronico,
            $telefono,
            $direccion_envio,
            $id_cliente
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return [
            "resultado" => "ok",
            "mensaje" => "Cliente modificado correctamente"
        ];
    }

}
?>

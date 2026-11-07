
<?php

class Categoria {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function consulta() {

        $sql = "SELECT
                    id_categoria,
                    nombre_categoria,
                    descripcion
                FROM categoria
                ORDER BY id_categoria DESC";

        $resultado = mysqli_query($this->conexion, $sql);

        if (!$resultado) {
            throw new Exception(mysqli_error($this->conexion));
        }

        $vec = [];

        while ($row = mysqli_fetch_assoc($resultado)) {
            $vec[] = $row;
        }

        return $vec;
    }

}

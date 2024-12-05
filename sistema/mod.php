<?php
require '../php/conexionbd.php';

if (!empty($_POST)) {
    $alert = '';
    if (empty($_POST['idv']) || empty($_POST['numv']) || empty($_POST['subt'])) {
        $alert = '<br>¡Todos los campos son obligatorios!</br>';
    } else {
        $id = intval($_GET['id']); // Asegura que el ID sea un número entero
        $cant = intval($_POST['numv']); // Sanitiza el valor
        $sub = floatval($_POST['subt']); // Asegura que sea decimal
        $fec = mysqli_real_escape_string($conn, $_POST['fec']); // Sanitiza la fecha

        $query = "UPDATE ventas SET num_ventas = '$cant', Total_Ventas = '$sub', Fecha = '$fec' WHERE Id = '$id'";
        $result = mysqli_query($conn, $query);

        // if ($result) {
        //     header('location: modificar.php');
        //     exit;
        // } else {
        //     die("Error al actualizar: " . mysqli_error($conn));
        // }
    }
}

// Verificación inicial del ID
// if (empty($_GET['idv'])) {
//     header('location: modificar.php');
//     exit;
// }

$idvnt = intval($_GET['id']); // Asegura que el ID sea válido

$querys = "SELECT vd.Id, vd.Id_Venta, vd.Subtotal, vd.Fecha_Detalle, vd.Cantidad, p.nombre AS NombreProducto, vd.Id_Producto 
           FROM venta_detalle vd 
           INNER JOIN inventario p ON vd.Id_Producto = p.id 
           WHERE vd.Id_Venta = $idvnt";

$sql = mysqli_query($conn, $querys);

if (!$sql) {
    die("Error en la consulta: " . mysqli_error($conn));
}

$result_sql = mysqli_num_rows($sql);

// if ($result_sql <= 0) {
//     header('location: modificar.php');
//     exit;
// }


//Seccion de refresh

if (isset($_POST["commit"])) {
    foreach ($_POST['id_detalle'] as $key => $id_detalle) {
        $idPr = intval($_POST['prod'][$key]);
        $cantidades = intval($_POST['numv'][$key]);
        $fecha_detalle = $_POST['fec'][$key];

        if (strtotime($fecha_detalle)) {
            $fecha_detalle = date('Y-m-d', strtotime($fecha_detalle));  
        } else {
            die("Fecha no válida");
        }

        // Obtener el producto
        $productoSeleccion = "SELECT id, nombre, precio_unitario FROM inventario WHERE id = $idPr";
        $qer = mysqli_query($conn, $productoSeleccion);
        if (!$qer) {
            die("Error en la consulta del producto: " . mysqli_error($conn));
        }

        $productosEnArray = mysqli_fetch_assoc($qer);
        $newProducto = $productosEnArray["id"];
        $newPrecio = $productosEnArray["precio_unitario"];

        // Actualizar el detalle de venta
        $resultados = mysqli_query($conn, 
            "UPDATE venta_detalle
            SET Id_Producto = $newProducto, Precio_Unitario = $newPrecio, Cantidad = $cantidades, Subtotal = $cantidades * $newPrecio
            ,Fecha_Detalle = '$fecha_detalle'
            WHERE Id = $id_detalle"
        );

        if (!$resultados) {
            die("Error al actualizar el detalle de venta: " . mysqli_error($conn));
        }
    }

    // Actualizar la venta
    $InsertVentas = "UPDATE ventas SET Total_Ventas = $cantidades * $newPrecio, Fecha = '$fecha_detalle', num_ventas = $cantidades WHERE Id = $idvnt";
    $com = mysqli_query($conn, $InsertVentas);

    if ($com) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $idvnt);
        exit;
    } else {
        die("Error al actualizar la venta: " . mysqli_error($conn));
    }
}




?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            color: white;
            background-color: #282c34; /* Ajusta según tu diseño */
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto; /* Centra el contenedor */
            padding: 20px;
            text-align: center;
        }

        .table-container {
            margin: 0 auto; /* Centra la tabla */
            display: flex;
            justify-content: center; /* Asegura que la tabla esté centrada horizontalmente */
            align-items: center; /* Centra verticalmente si es necesario */
        }

        table {
            margin: 0 auto; /* Centra la tabla dentro del contenedor */
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            width: auto;
            padding: 10px 20px;
            display: inline-block;
        }
    </style>
    <meta charset="utf-8">
    <title>Control de Ventas</title>
    <link rel="icon" href="../images/modif.png">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
</head>
<body>
    <div>
        <ul>
            <li style="float:left; transform: translate(35%,0%);">
                <a href="modificar.php" class="sesion"> < Regresar</a>
            </li>
        </ul>
    </div>
    <div class="form-container">
        <u><h2 class = "text-primary">Modificar Venta</h2></u>
        <form method="POST">
    <div class="table-container">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Id_Producto</th>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Precio p/producto</th>
                    <th scope="col">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($data = mysqli_fetch_array($sql)) : ?>
                    <tr>
                        <td><input type="text" name="idv[<?php echo $data['Id']; ?>]" value="<?php echo $data['Id_Venta']; ?>" disabled></td>
                        <td>
                            <input type="hidden" name="id_detalle[<?php echo $data['Id']; ?>]" value="<?php echo $data['Id']; ?>">
                            <input type="number" name="prod[<?php echo $data['Id']; ?>]" value="<?php echo $data['Id_Producto']; ?>" required>
                        </td>
                        <td><input type="text" name="name[<?php echo $data['Id']; ?>]" value="<?php echo $data['NombreProducto']; ?>" readonly></td>
                        <td><input type="number" name="numv[<?php echo $data['Id']; ?>]" value="<?php echo $data['Cantidad']; ?>" required></td>
                        <td><input type="text" name="subt[<?php echo $data['Id']; ?>]" value="<?php echo $data['Subtotal']; ?>" disabled></td>
                        <td><input type="date" name="fec[<?php echo $data['Id']; ?>]" value="<?php echo $data['Fecha_Detalle']; ?>" required></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="button-container">
        <button type="submit" class="btn btn-warning btn-sm" name="commit" onclick="return confirm('¿Estás seguro de que quieres cambiar el producto?');">
            Confirmar
        </button>
    </div>
</form>

    </div>
</body>
</html>

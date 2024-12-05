<?php
    require '../php/conexionbd.php';
    session_start();

    // Verificar si la sesión está activa y si el usuario tiene permisos adecuados
    if (empty($_SESSION['active'])) {
        header('location: ../');
        exit;
    }
    if ($_SESSION['typus'] == "user") {
        header('location: ../');
        exit;
    }

    // Consulta para obtener los datos de ventas
    $query = "SELECT Id, Total_Ventas, Fecha, Id_Empleado, num_ventas FROM ventas";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conn));
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"> 
    <title>Control de Ventas</title>
    <link rel="icon" href="../images/modif.png">
    <link rel="stylesheet" href="../styles/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
</head>
<body style="color: white;"> 
    <div>
        <ul class="nav justify-content-center bg-dark p-3">
            <li class="nav-item"><a class="nav-link text-white" href="index.php">Ver</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="registrar.php">Registrar</a></li>
            <li class="nav-item"><a class="nav-link text-white active" href="modificar.php">Modificar</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="eliminar.php">Eliminar</a></li>
            <li class="nav-item ms-auto"><a class="nav-link text-white" href="../php/salir.php">Cerrar sesión</a></li>
        </ul>
    </div>
    <div class="container my-5 text-center">
        <h2 class="text-primary"><u>Modificar Venta</u></h2>
    </div>
    <div class="container">
        <table class="table table-striped table-bordered table-hover table-responsive">
            <thead class="thead-dark">
                <tr>
                    <th>Num. Registro</th>
                    <th>Total de venta</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($ventas = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $id = htmlspecialchars($ventas['Id']);
                            $total_ventas = htmlspecialchars($ventas['Total_Ventas']);
                            $fecha = htmlspecialchars($ventas['Fecha']);
                            $num_ventas = htmlspecialchars($ventas['num_ventas']);


                            //InnerJoin para consulta.
                            $queryProductos = "SELECT vd.Id_Producto, p.nombre AS NombreProducto, vd.Cantidad 
                            FROM venta_detalle vd INNER JOIN inventario p ON vd.Id_Producto = p.id 
                            WHERE vd.Id_Venta = $id;";
                            //Entonces, para llamar a el nombre simplemente 'NombreProducto' dentro del array.
                        

                            $result_products = mysqli_query($conn, $queryProductos);
                            //Hasta este punto, ejecutas el QueryProductos, lo cual nos devuelve valores: id_producto y Cantidades.
                            //Entonces, tenemos que extraer el id del producto y posteriormente 

                ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $num_ventas; ?></td>
                        <td><?php echo $total_ventas; ?></td>
                        <td><?php echo $fecha; ?></td>
                        <td>
                            <a href="mod.php?id=<?php echo $id; ?>" class="btn btn-warning btn-sm">Modificar</a>
                            <button class="btn btn-info btn-sm" data-bs-toggle="collapse" data-bs-target="#productos-<?php echo $id; ?>" aria-expanded="false" aria-controls="productos-<?php echo $id; ?>">
                                Ver productos
                            </button>
                        </td>
                    </tr>
                    <tr id="productos-<?php echo $id; ?>" class="collapse">
                        <td colspan="5">
                            <div class="card card-body">
                                <h5>Productos de la venta <?php echo $id; ?>:</h5>
                                <ul class ="list-group">
                                <?php
                                    if ($result_products && mysqli_num_rows($result_products) > 0) {
                                        while ($producto = mysqli_fetch_array($result_products, MYSQLI_ASSOC)) {
                                            $producto_id = htmlspecialchars($producto['Id_Producto']);
                                            $producto_cantidad = htmlspecialchars($producto['Cantidad']);
                                            $producto_Nombre = htmlspecialchars($producto['NombreProducto']);
                                            // Cada producto con salto de línea
                                            echo "<li class=\"list-group-item\">Id: $producto_id $producto_Nombre (Cantidad: $producto_cantidad)<br></li>";
                                        }
                                    } else {
                                        echo "<p>No hay productos registrados para esta venta.</p>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay registros disponibles.</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php mysqli_close($conn); // Cerrar la conexión ?>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

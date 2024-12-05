<?php
session_start();
require '../php/conexionbd.php';

if (isset($_POST['eliminar_producto']) && isset($_POST['idproducto'])) {
    $idProductoEliminar = $_POST['idproducto'];

    foreach ($_SESSION['productos'] as $key => $producto) {
        if ($producto['idproducto'] == $idProductoEliminar) {
            unset($_SESSION['productos'][$key]);
            break;
        }
    }
    $_SESSION['productos'] = array_values($_SESSION['productos']);
}

if (isset($_POST['idproducto']) && !empty($_POST['idproducto']) &&
    isset($_POST['cantidad']) && !empty($_POST['cantidad']) &&
    isset($_POST['fecventas']) && !empty($_POST['fecventas'])) {

    $idproducto = $_POST['idproducto'];
    $cantidad = $_POST['cantidad'];
    $fecventas = $_POST['fecventas'];

    // Consulta para obtener los detalles del producto
    $sql = "SELECT * FROM inventario WHERE id = '$idproducto'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $producto = mysqli_fetch_assoc($result);
        $stock = $producto['stock'];
        $precio = $producto['precio_unitario'];

        // Comprobar la cantidad total de ese producto ya agregada en la sesión
        $cantidadTotalEnSesion = 0;
        if (isset($_SESSION['productos'])) {
            foreach ($_SESSION['productos'] as $prod) {
                if ($prod['idproducto'] == $idproducto) {
                    $cantidadTotalEnSesion += $prod['cantidad'];
                }
            }
        }
        if (($cantidadTotalEnSesion + $cantidad) <= $stock) {
            $subtotal = $precio * $cantidad;
            if (!isset($_SESSION['productos'])) {
                $_SESSION['productos'] = array();
            }

            $_SESSION['productos'][] = array(
                'idproducto' => $idproducto,
                'nombre' => $producto['nombre'],
                'precio' => $precio,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
                'fecventas' => $fecventas
            );

            $alert = "Producto agregado correctamente.";
        } else {
            $alert = "No puedes agregar más productos. Solo hay $stock disponibles en stock.";
        }
    } else {
        $alert = "Producto no encontrado.";
    }
}





// Verificar si los productos existen en la sesión
if (isset($_POST["commit"])) {
    if (isset($_SESSION['productos']) && count($_SESSION['productos']) > 0) {
        $totalVenta = 0;
        $idEmpleado = 1; // Cambiar por el ID real del empleado desde la sesión o fuente relevante
        $fechaVenta = date('Y-m-d');

        // Calcular el total de la venta
        foreach ($_SESSION['productos'] as $producto) {
            $totalVenta += $producto['subtotal'];
            $cantidadProductos = $producto['cantidad'];
        }

        // Insertar en la tabla ventas (una sola vez)
        $sqlVenta = "INSERT INTO ventas (Total_Ventas, Fecha, Id_Empleado, num_ventas) 
                     VALUES ($totalVenta, '$fechaVenta', $idEmpleado,$cantidadProductos)";
        echo "<pre>$sqlVenta</pre>"; // Depuración

        if (mysqli_query($conn, $sqlVenta)) {
            $idVenta = mysqli_insert_id($conn);

            // Insertar cada detalle en la tabla venta_detalle
            foreach ($_SESSION['productos'] as $producto) {
                $idProducto = $producto['idproducto'];
                $precio = $producto['precio'];
                $cantidad = $producto['cantidad'];
                $subtotal = $producto['subtotal'];
                $fecventas = $producto['fecventas'];

                $sqlDetalle = "INSERT INTO venta_detalle (Id_Producto, Cantidad, Precio_Unitario, Subtotal, Fecha_Detalle, Id_Venta) 
                               VALUES ($idProducto, $cantidad, $precio, $subtotal, '$fecventas', $idVenta)";
                echo "<pre>$sqlDetalle</pre>"; // Depuración

                if (!mysqli_query($conn, $sqlDetalle)) {
                    echo "Error al registrar el detalle de venta: " . mysqli_error($conn);
                }

                // Actualizar el stock
                $sqlUpdateStock = "UPDATE inventario SET stock = stock - $cantidad WHERE id = $idProducto";
                echo "<pre>$sqlUpdateStock</pre>"; // Depuración

                if (!mysqli_query($conn, $sqlUpdateStock)) {
                    echo "Error al actualizar el stock: " . mysqli_error($conn);
                }
            }

            echo "Venta registrada correctamente.";
        } else {
            echo "Error al registrar la venta: " . mysqli_error($conn);
        }

        // Limpiar la sesión después de registrar la venta
        unset($_SESSION['productos']);
    } else {
        echo "No hay productos para registrar.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
	<style>
    /* Styles for the form and table */
    .container {
      display: flex;
      justify-content: space-between;
      max-width: 1200px;
      margin: 0 auto;
      padding-top: 50px;
    }

    .form-container {
      max-width: 400px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-container {
      flex: 1;
      margin-left: 20px;
    }

    .alert {
      font-size: 14px;
    }

    /* Aseguramos que el formulario y la tabla no se superpongan */
    .form-container input, .form-container button {
      margin-bottom: 15px;
    }
	</style>
    <meta charset="utf-8"> 
    <title>Control de Ventas</title>
    <link rel="icon" href="../images/check.png">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
</head>
<body style="color: white;">
    <div>
        <ul class="nav justify-content-center bg-dark p-3">
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php">Ver</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="registrar.php">Registrar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white active" href="modificar.php">Modificar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="eliminar.php">Eliminar</a>
            </li>
            <li class="nav-item ms-auto">
                <a class="nav-link text-white" href="../php/salir.php">Cerrar sesión</a>
            </li>
        </ul>
    </div>
    <div class="container">
        <div class="form-container">
            <form action="" method="POST">
                <div class="mb-3">
                    <i class="fas fa-clipboard-check"></i>
                    <input type="text" placeholder="ID de producto" name="idproducto" autocomplete="off" required class="form-control">
                </div>
                <div class="mb-3">
                    <i class="fas fa-dollar-sign"></i>
                    <input type="text" placeholder="Cantidad" name="cantidad" autocomplete="off" required class="form-control">
                </div>
                <div class="mb-3">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="text" placeholder="AAAA-MM-DD" name="fecventas" autocomplete="off" required class="form-control">
                </div>
                <button type="submit" class="btn btn-warning btn-sm" value="registrar">Añadir</button>
            </form>
            <div>
            <form method="POST">
                    <button type="submit" class="btn btn-warning btn-sm" name = "commit">Registrar Venta</button>
            </form>
                
            </div>
        </div>
        <div>
           
        </div>
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaVentas">
                    <?php
                    if (isset($_SESSION['productos']) && count($_SESSION['productos']) > 0) {
                        foreach ($_SESSION['productos'] as $producto) {
                            echo "<tr id='producto_{$producto['idproducto']}'>
                                <td>{$producto['idproducto']}</td>
                                <td>{$producto['nombre']}</td>
                                <td>{$producto['precio']}</td>
                                <td>{$producto['cantidad']}</td>
                                <td>{$producto['subtotal']}</td>
                                <td>{$producto['fecventas']}</td>
                                <td>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='idproducto' value='{$producto['idproducto']}'>
                                        <button type='submit' name='eliminar_producto' class='btn btn-warning btn-sm'>
                                            <i class='fas fa-trash-alt'></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div class="alert alert-info">
                <strong>Total de la Venta: </strong> $<?php 
                    $totalVenta = 0;
                    if (isset($_SESSION['productos']) && count($_SESSION['productos']) > 0) {
                        foreach ($_SESSION['productos'] as $producto) {
                            $totalVenta += $producto['subtotal'];
                        }
                    }
                    echo number_format($totalVenta, 2); // Muestra el total con dos decimales
                ?>
        </div>
        </div>
    </div>
</body>
</html>

<?php
    require '../php/conexionbd.php';
    session_start();

    if (empty($_SESSION['active']) || $_SESSION['typus'] == "user") {
        header('location: ../');
        exit();
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $idr = $_POST['id'];

        $sqlUpdate = "UPDATE venta_detalle SET Id_Venta = NULL WHERE Id_Venta = $idr";
        $sqlDelete = "DELETE FROM ventas WHERE id = '$idr'";

        if (mysqli_query($conn, $sqlUpdate) && mysqli_query($conn, $sqlDelete)) {
            header('Location: eliminar.php');
            exit();
        } else {
            echo "Error al eliminar el registro: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"> 
    <title>Control de Ventas</title>
    <link rel="icon" href="../images/cross.png">
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
    <div class="container my-5">
        <div class="text-center mb-4">
            <h2 class="text-primary"><u>Eliminar Venta</u></h2>
        </div>

        <form action="eliminar.php" method="POST">
            <div class="mb-3">
                <label for="id" class="form-label">Número de registro</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    <input type="text" class="form-control" id="id" name="id" placeholder="Número de registro" autocomplete="off" required>
                </div>
            </div>
            <button type="submit" class="btn btn-warning btn-sm" onclick="">Eliminar</button>
        </form>
    </div>

    <!-- Tabla de ventas -->
    <div class="container my-5">
        <table class="table table-striped table-bordered table-hover table-responsive">
            <thead class="thead-dark">
                <tr>
                    <th>Num. Registro</th>
                    <th>Número de ventas</th>
                    <th>Total en ventas</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM ventas";
                    $result = mysqli_query($conn, $sql);
                    while ($ventas = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td><?php echo $ventas['Id']; ?></td>
                    <td><?php echo $ventas['num_ventas']; ?></td>
                    <td><?php echo $ventas['Total_Ventas']; ?></td>
                    <td><?php echo $ventas['Fecha']; ?></td>
                    <td>
                        <form action="eliminar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $ventas['Id']; ?>">
                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta venta?');">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

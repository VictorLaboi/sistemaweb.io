<?php
	require '../php/conexionbd.php';
	$alert = '';
	session_start();
	if (empty($_SESSION['active'])) {
		header('location: ../');
	}	


?>
<!DOCTYPE html>
<html>
<head> 
	<meta charset="utf-8"> 
	<title>Control de Ventas</title>
	<link rel="icon" href="../images/clipboard.png">
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
            <h2 class="text-primary"><u>Ventas Registradas</u></h2>
        </div>
	</div>
	<div>
	<div class="container my-5">
    <table class="table table-striped table-bordered table-hover table-responsive">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Num. Registro</th>
                <th scope="col">Número de ventas</th>
                <th scope="col">Total en ventas</th>
                <th scope="col">Fecha</th>
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
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>


	</div>
</body>
</html>
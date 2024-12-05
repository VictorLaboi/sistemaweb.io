<?php
session_start(); 
$alert = '';

if (!empty($_SESSION['active'])) { 
    header('location: sistema/');
    exit;
}

if (!empty($_POST)) { 
    if (empty($_POST['username1']) || empty($_POST['password1'])) {
        $alert = 'Ingrese su usuario y contraseña';
    } else {
        require 'php/conexionbd.php';

 
        $user = trim($_POST['username1']);
        $psw = trim($_POST['password1']);
        $st = $conn->prepare("SELECT id, username,rol FROM usuarios WHERE username = ? AND password = ?");
        $st->bind_param("ss", $user, $psw);

        if ($st->execute()) {
            $result = $st->get_result();
            if ($result->num_rows > 0) { 
                $data = $result->fetch_assoc();
                $_SESSION['active'] = true;
                $_SESSION['idus'] = $data['id'];
                $_SESSION['usernameus'] = $data['username'];
                $_SESSION['typus'] = $data['rol'];

                header('location: sistema/');
                exit;
            } else {
                $alert = 'Usuario y/o contraseña incorrecto';
                session_destroy();
            }
            $result->free();
        } else {
            $alert = 'Error al ejecutar la consulta';
        }
        $st->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head> <!--Atributos necesarios para el funcionamiento-->

	<meta charset="utf-8"> 
	<title>Control de Ventas</title>
	<link rel="icon" href="images/open-book.png">
	<link rel="stylesheet" href="styles/style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<style>
        body {
            background-color: #f0f8ff; /* Fondo azul claro */
        }
        .container {
            max-width: 400px; /* Máximo ancho del formulario */
            margin-top: 50px;
        }
        .form-container {
            background-color: #ffffff; /* Fondo blanco para el formulario */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .alert {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="" method="POST">
                <div class="mb-3">
                    <h1 class="text-center">Iniciar sesión</h1>
                    <label for="exampleInputEmail1" class="form-label">Usuario</label>
                    <input type="text" class="form-control" placeholder="Usuario" name="username1" autocomplete="off" required>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Contraseña" name="password1" autocomplete="off" required>
                </div>

				<div class="alert alert-danger text-center" <?php echo isset($alert) && $alert != '' ? '' : 'style="display: none;"'; ?>>
    			<?php echo isset($alert) ? $alert : ''; ?>
				</div>
                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
        </div>
    </div>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
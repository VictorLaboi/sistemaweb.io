<?php
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "sistemaweb3";

	$conn = new mysqli($host,$user,$pass,$db);


	if (!$conn) {
		echo "Error en la conexión";
	}
?> 
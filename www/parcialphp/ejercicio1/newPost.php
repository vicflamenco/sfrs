<!DOCTYPE html>
<html>
<head>
	<title>Registro usuario</title>
	<meta charset="utf8">
</head>
<body>

	<?php

		$txtuserid = $_POST['txtuserid'];
		$txtnombres = $_POST['txtnombres'];
		$txtapellidos = $_POST['txtapellidos'];
		$txtpassw = $_POST['txtpassw'];
		$txtgrupo = $_POST['txtgrupo'];
		$txtestado = $_POST['txtestado'];

		$conn = new mysqli("mysql3.gear.host", "vicflamenco1", "victor!", "dbusuarios");


		$sql = "INSERT INTO usuarios (userid, nombres, apellidos, passw, idgrupo, estado) VALUES (";
		$sql = $sql . "'" . $txtuserid . "','" . $txtnombres . "','" . $txtapellidos . "','" . $txtpassw;
		$sql = $sql . "'," . $txtgrupo . ",'" . $txtestado . "');";

		$query = mysqli_query($conn, $sql);

		if ($conn->affected_rows > 0)
		{
			?>
				<h3>Usuario registrado con éxito</h3>
			<?php
		} else {
			?>
				<h3>Usuario no pudo ser registrado. Ocurrió un error.</h3>
			<?php
		}
	?>

	<a href="users.php">
		Volver
	</a>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>Nuevo usuario</title>
</head>
<body>
	<h1>Nuevo usuario</h1>

	<form action="newPost.php" method="POST">
		Cod Usuario: <input type="text" name="txtuserid" required/>
		<br/>
		<br/>
		Nombres: <input type="text" name="txtnombres" required/>
		<br/>
		<br/>
		Apellidos: <input type="text" name="txtapellidos" required/>
		<br/>
		<br/>
		Contraseña: <input type="password" name="txtpassw" required/>
		<br/>
		<br/>
		Grupo:
		<select name="txtgrupo"> 
			<?php
				$conn = new mysqli("mysql3.gear.host", "vicflamenco1", "victor!", "dbusuarios");
				$sql = "SELECT * FROM grupos";
					$grupos = mysqli_query($conn, $sql);

					if (mysqli_num_rows($grupos) > 0) {

						
						while ($row = mysqli_fetch_array($grupos, MYSQLI_ASSOC)) {
							echo "<option value=" . $row['id'] . ">";
							echo $row['grupo'];
							echo "</option>";
						}
						
					}
			?>

		</select>
		<br/>
		<br/>
		Estado: <input type="text" name="txtestado" required/>
		<br/>
		<br/>

		<input type="submit" value="Crear" />
	</form>
</body>
</html>
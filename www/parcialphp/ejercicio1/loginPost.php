<!DOCTYPE html>
<html>
<head>
	<title>Logueo</title>
	<meta charset="utf8">
</head>
<body>


	<?php


		session_start();


		function login()
		{	
			if (isset($_POST['txtUser']) && isset($_POST['txtPass']))
			{
				$txtUser = $_POST['txtUser'];
				$txtPass = $_POST['txtPass'];

				$conn = new mysqli("mysql3.gear.host", "vicflamenco1", "victor!", "dbusuarios");
				$sql = "SELECT * FROM usuarios WHERE userid = '" . $txtUser . "' AND passw = '" . $txtPass . "'";
				$usuarios = mysqli_query($conn, $sql);

				if (mysqli_num_rows($usuarios) > 0) {

					$_SESSION['userid'] = $txtUser;
					$_SESSION['state'] = 1;
					return true;
				}	
			}
			return false;
		}

	?>


	<?php
		if(!login()) {

			?>
			<h1>Usuario no logueado o credenciales incorrectas.</h1>
			<a href="login.php">
			Regresar
			</a>
			<?php
		}
		else
		{

	?>
			<h1>Bienvenido <?php echo $_SESSION['userid']; ?>
			<br/>
			<br/>
			<a href="users.php">Ir a catálogo de usuarios</a>

	<?php

		}
	?>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<title>
		Login
	</title>
	<meta charset="utf8">
</head>

<body>
	<?php

		session_start();
		$_SESSION['userid'] = "";
		$_SESSION['state'] = 0;
	?>

	<h1>Login</h1>

	<form method="POST" action="loginPost.php">
		Usuario: <input type="text" name="txtUser" required/>
		<br/>
		<br/>
		Contraseña: <input type="password" name="txtPass" required/>
		<br/>
		<br/>
		<input type="submit" value="Iniciar sesión" />
	</form>
</body>

</html>
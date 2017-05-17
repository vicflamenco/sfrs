<!DOCTYPE html>
<html>
<head>
	<title>Cargar archivo</title>
	<meta charset="utf8">
</head>
<body>

	<h1>Carga de archivo generado de máquina</h1>

	<p>Formatos admitidos: <strong>*.txt</strong></p>


	<form method="POST" action="read.php" enctype="multipart/form-data">

		<input type="file" name="txtfile" required />

		<input type="submit" value="Cargar" />

	</form>

</body>
</html>
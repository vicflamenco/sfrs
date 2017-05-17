<!DOCTYPE html>
<html>
<head>
	<title>Leer archivo cargado</title>
	<meta charset="utf8">
</head>
<body>

	<h1>Lectura de archivo cargado</h1>

	<?php

		function startsWith($string, $query)
		{
			return substr($string, 0, strlen($query)) == $query;
		}

		if ($_FILES['txtfile']["error"] > 0)
		{
			echo "<p>Error al cargar el archivo: " . $_FILES['txtfile']['error'] . "</p>";			
		}
		else 
		{
			$filename = $_FILES['txtfile']['name'];
			$extensiones_permitidas = ['txt'];
			$partes = explode(".", $filename);
			$extension = end($partes); 
			if(!in_array($extension, $extensiones_permitidas))
			{
				echo "<p>Extensión no válida: " . $extension . "</p>";
			}
			else 
			{
				try
				{
					$path = "archivos/".$_FILES['txtfile']['name'];

					move_uploaded_file($_FILES['txtfile']['tmp_name'],$path);

					$archivo = fopen($path, "r");

					$conn = new mysqli("mysql3.gear.host", "vicflamenco", "victor!", "dbmachines");


					$machine = "";
					$date = "";
					$pack = -1;

					$registros = 0;

					echo '<p>================================ INICIA LECTURA DE ARCHIVO ================================</p>';
					while(!feof($archivo))
					{
						$line = fgets($archivo);
						echo $line . "<br />";

						if (startsWith($line, "[MACHINE]"))
						{
							$machine = explode(" ",$line)[1];
						}
						else if (startsWith($line, "[DATE]"))
						{
							$partes = explode(" ",$line);
							unset($partes[0]);
							$date = implode(" ",$partes);
						}
						else if (startsWith($line, "[PACK]"))
						{
							$pack = explode(" ",$line)[1];
						}
						else if ($line != "" && !startsWith($line,"-") && !startsWith($line,"No"))
						{
							$valores = explode("\t",$line);
							
							if (sizeof($valores) == 5)
							{
								$correlative = $valores[0];
								$ni = $valores[1];
								$fe = $valores[2];
								$cu = $valores[3];
								$si = $valores[4];
								
								$sql = "INSERT INTO measurement (machine,dttm,pack,correlative,ni,fe,cu,si) VALUES (";
								$sql = $sql . "'" . $machine . "','" . $date . "','" . $pack . "'," . $correlative;
								$sql = $sql . "," . $ni . "," . $fe . "," . $cu . "," . $si . ");";

								$query = mysqli_query($conn, $sql);

								if ($conn->affected_rows > 0)
								{
									$registros++;
								}
							}
						}
						
					}
					echo '<p>================================ FINALIZA LECTURA DE ARCHIVO ================================</p>';

					echo '<br/><br/>Se cargaron ' . $registros . " registros satisfactoriamente";
					fclose($archivo);
				}
				catch (Exception $e)
				{
					echo "<p>Ocurrió un error: " . $e->getMessage() . "</p>";
				}
			}
		}
	?>
	<br/>
	<br/>

	<a href="index.php">
		Regresar
	</a>
</body>
</html>
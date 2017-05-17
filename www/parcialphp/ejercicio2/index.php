<!DOCTYPE html>
<html>
<head>
	<title>Registros cargados a la base de datos</title>
	<meta charset="utf8">

</head>
<body>


		<h1>Catálogo de registros cargados mediante archivos de texto</h1>

	<a href="load.php">
		Cargar nuevo archivo
	</a>
	<br/>
	<br/>

	<form action="index.php" method="GET">

		Buscar por código de PACK: 
		<?php
			if ($_GET != null && isset($_GET['query']))
			{
				echo '<input type="text" placeholder="Codigo PACK" name="query" value="' . $_GET['query'] . '">'; 
				
			} else {
				echo '<input type="text" placeholder="Codigo PACK" name="query">';
			}
		?>
		
		<input type="submit" value="Buscar" />
	</form>

	<br/>

	<table border="1">

		<thead>
			<tr>
				<td>Machine</td>
				<td>Date</td>
				<td>Pack</td>
				<td>Correlativo</td>
				<td>Ni</td>
				<td>Fe</td>
				<td>Cu</td>
				<td>Si</td>
			</tr>
		</thead>

		<tbody>

			<?php  

				$conn = new mysqli("mysql3.gear.host", "vicflamenco", "victor!", "dbmachines");

				$sql = "SELECT * FROM measurement";

				if ($_GET != null && isset($_GET['query']))
				{
					$sql = $sql . " WHERE pack LIKE '%" . $_GET['query'] . "%'";
				}

				$measurements = mysqli_query($conn, $sql);

				if (mysqli_num_rows($measurements) > 0) {

					while ($row = mysqli_fetch_array($measurements, MYSQLI_ASSOC)) {

						echo "<tr>";

						echo "<td>".$row['machine']."</td>";
						echo "<td>".$row['dttm']."</td>";
						echo "<td>".$row['pack']."</td>";
						echo "<td>".$row['correlative']."</td>";
						echo "<td>".$row['ni']."</td>";
						echo "<td>".$row['fe']."</td>";
						echo "<td>".$row['cu']."</td>";
						echo "<td>".$row['si']."</td>";

						echo "</tr>";
					}


				} else {

			?> 
						    <tr>
						    	<td colspan="8">
						    		No se encontraron registros
						    	</td>
						    </tr> 

			<?php 
					}
			?> 
		</tbody>
	</table>

</body>
</html>
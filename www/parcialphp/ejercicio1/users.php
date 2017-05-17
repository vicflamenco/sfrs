<!DOCTYPE html>
<html>
<head>
	<title>Catálogo usuarios</title>
	<meta charset="utf8">

</head>
<body>


		<h1>Catálogo de usuarios</h1>

	<a href="new.php">
		Nuevo
	</a>
	<br/>
	<br/>

	<form action="users.php" method="GET">

		Buscar por código de usuario: 
		<?php
			if ($_GET != null && isset($_GET['query']))
			{
				echo '<input type="text" placeholder="Codigo usuario" name="query" value="' . $_GET['query'] . '">'; 
				
			} else {
				echo '<input type="text" placeholder="Codigo usuario" name="query">';
			}
		?>
		
		<input type="submit" value="Buscar" />
	</form>

	<br/>

	<table border="1">

		<thead>
			<tr>
				<td>UserId</td>
				<td>Nombres</td>
				<td>Apellidos</td>
				<td>Grupo</td>
				<td>Estado</td>
			</tr>
		</thead>

		<tbody>

			<?php 
				session_start();

				$conn = new mysqli("mysql3.gear.host", "vicflamenco1", "victor!");

				if ($conn->connect_error) { 

			?> 

				    <tr>
				    	<td colspan="5">
				    		Error de conexión a BD
				    	</td>
				    </tr> 

			<?php 

				} 
				else if (mysqli_select_db($conn, "dbUsuarios"))
				{
					$sql = "SELECT * FROM usuarios";

					if ($_GET != null && isset($_GET['query']))
					{
						$sql = $sql . " WHERE userid LIKE '%" . $_GET['query'] . "%'";
					}

					$usuarios = mysqli_query($conn, $sql);

					if (mysqli_num_rows($usuarios) > 0) {

						while ($row = mysqli_fetch_array($usuarios, MYSQLI_ASSOC)) {

							echo "<tr>";

							echo "<td>".$row['userid']."</td>";
							echo "<td>".$row['nombres']."</td>";
							echo "<td>".$row['apellidos']."</td>";
							echo "<td>".$row['idgrupo']."</td>";
							echo "<td>".$row['estado']."</td>";

							echo "</tr>";
						}


					} else {

			?> 
						    <tr>
						    	<td colspan="5">
						    		No se encontraron usuarios
						    	</td>
						    </tr> 
			<?php 

					}

					$conn->close();
				}
				else
				{
			?> 

				    <tr>
				    	<td colspan="5">
				    		Error de selección de BD
				    	</td>
				    </tr> 

			<?php 

				} 

			?> 
		</tbody>
	</table>

	<br/>
	<br/>

	<a href="login.php">
		Salir
	</a>
	
</body>
</html>
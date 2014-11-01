<?php
	define("SERVERNAME","team01project.db.6194647.hostedresource.com");
	define("DATABASENAME","team01project");
	define("USR","team01project");
	define("PASS","Pass4team01!");

	$con = mysqli_connect(constant('SERVERNAME'), constant('USR'), constant('PASS'), constant('DATABASENAME'));
	if($con->connect_error) {
		die("Connection failed: " . $con->connect_error);			// Fail if we can't connect
	}
	$stmt = "SELECT client_id,client_username,client_password FROM accounts";
	$result = mysqli_query($con,$stmt);											// Get all client records
	$con->close();																					// ...and close the connection
	
	echo "<table>\n";
	echo "<thead><tr><td>ID</td><td>Username</td><td>Hash</td></tr></thead>\n";
	
	while($row = mysqli_fetch_array($result)) {
		echo "<tr><td>".$row['client_id']."</td>";
		echo "<td>".$row['client_username']."</td>";
		echo "<td>".$row['client_password']."</td></tr>";
	}
	
	echo "</table>\n";
?>
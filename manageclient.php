<!-- 
	- - - - - - - - - - - -

	Title: A Novel Concept

	Version: 1.1

	Date: September 2014

	- - - - - - - - - - - -
	"A Novel Concept"
	Client Management Page
	Jeff Codling
 -->
<!-- 
Login System Client Management
by Jeff Codling - c0471944
 -->
<html>
<head>
</head>
<body>
<h1>Client management console.</h1>
<?php
	define("SERVERNAME","team01project.db.6194647.hostedresource.com");
	define("DATABASENAME","team01project");
	define("USR","team01project");
	define("PASS","Pass4team01!");

	function DoSQL($statement) {
		$con = mysqli_connect(constant('SERVERNAME'), constant('USR'), constant('PASS'), constant('DATABASENAME'));
		if($con->connect_error) {
			die("Connection failed: " . $con->connect_error);			// Fail if we can't connect
		}
		$result = mysqli_query($con,$statement);											// Get all client records
		$con->close();																					// ...and close the connection
		return $result;
	}
	
	$displayOriginal = true;
	
	if(isset($_POST['return'])) {
		header("Location: books.php");
	}
	
	if(isset($_REQUEST['modify'])) {
		$username = $_REQUEST['username'];
		$newpass = $_REQUEST['newpass'];
		
		$stmt = "SELECT client_username,client_password FROM accounts WHERE client_id=".$_REQUEST['clientid'];
		$results = DoSQL($stmt);
		$row = mysqli_fetch_array($results);
		if($results) {
			$clientUsername = $_REQUEST['username'];
			$newPassword = $_REQUEST['newpass'];
			$newHashed = $newPassword;
			if(password_verify($_REQUEST['oldpass'],$row['client_password'])) {
				$stmt = "UPDATE accounts SET client_username=\"".$clientUsername."\",client_password=\"".$newHashed."\"";
				$stmt = $stmt." WHERE client_id=".$_REQUEST['clientid'];
				
				if(DoSQL($stmt)) {
					echo "Successful<hr>\n";
				} else {
					echo "Not Successful<hr>\n";
				}
			} else {
				echo "<br>Old Password much match.<br>\n";
			}
		} else {
			die("No record found for client id of ".$_REQUEST['clientid']);
		}
	}
	
	if(isset($_REQUEST['deleteok'])) {
		if(isset($_REQUEST['deletebox'])) {
			echo "Delete client ID of ".$_REQUEST['clientid']."<br>\n";
			
			$stmt = "DELETE FROM accounts WHERE client_id=".$_REQUEST['clientid'];
			if(DoSQL($stmt)) {
				echo "<hr>Record deleted.<hr>\n";
			} else {
				echo "<hr>Error: Couldn't delete the record.<hr>\n";
				$displayOriginal = false;
			}
		} else {
			echo "<hr>Delete canceled.<hr>";
		}
	}

	if(isset($_REQUEST['submit'])) {
		if(isset($_REQUEST['task'])) {
			$clientToUse = $_REQUEST['clientid'];
			echo "<hr>\n";
			$stmt = "nothing";
			if(!strcmp($_REQUEST['task'],"show")) {
				$stmt = "SELECT client_id, client_username, client_password FROM accounts";
				if($_REQUEST['clientid'] != "all") {
					$stmt = $stmt." WHERE client_id=";
					$stmt = $stmt.$_REQUEST['clientid'];
				}
				$result = DoSQL($stmt);
				while($row = mysqli_fetch_array($result)) {
					echo "ID: ".$row['client_id']."<br>";
					echo "Username: ".$row['client_username']."<br>";
					echo "Password: ".$row['client_password']."<hr>\n";
				}
				$displayOriginal = true;
			}
			if(!strcmp($_REQUEST['task'],"modify")) {
				if($_REQUEST['clientid'] == "all") {
					echo "Select an ID to modify. You can not modify ALL at once.<hr>";
				} else {
					$stmt = "SELECT client_id,client_username,client_password FROM accounts WHERE client_id=";
					$stmt = $stmt.$_REQUEST['clientid'];
					$result = DoSQL($stmt);
					$row = mysqli_fetch_array($result);
					echo "<form name=\"modifyform\" method=\"post\">\n";
					echo "Client ID: ".$row['client_id']."\n";
					echo "<input type=\"hidden\" name=\"clientid\" value=\"".$row['client_id']."\">\n";
					echo "Username : <input type=\"text\" name=\"username\" value=\"".$row['client_username']."\">\n";
					echo "Password : <input type=\"password\" name=\"oldpass\" placeholder=\"Current Password\">\n";
					echo "New Pass : <input type=\"password\" name=\"newpass\" placeholder=\"New Password\">\n";
					echo "<input type=\"submit\" name=\"modify\" value=\"Modify\">\n";
					echo "</form>\n";
					$displayOriginal = false;
				}
			}
			if(!strcmp($_REQUEST['task'],"delete")) {
				if($_REQUEST['clientid'] == "all") {
					echo "Select an ID to delete. You can not delete ALL at once.<hr>";
				} else {
					$stmt = "SELECT client_username, client_password FROM accounts WHERE client_id=".$_REQUEST['clientid'];
					$results = DoSQL($stmt);
					if($results) {
						$row = mysqli_fetch_array($results);
						echo "Client ID : ".$_REQUEST['clientid']."\tUsername: ".$row['client_username']."<br>\n";
						echo "<form name=\"deleteform\" method=\"post\">\n";
						echo "<input type=\"checkbox\" name=\"deletebox\" value=\"ok\"> Verify delete.\n";
						echo "<input type=\"hidden\" name=\"clientid\" value=\"".$_REQUEST['clientid']."\">";
						echo "<input type=\"submit\" name=\"deleteok\" value=\"Verify\">\n";
						echo "</form>\n";
					} else {
						echo "<hr>No record found.<hr>";
						$displayOriginal = false;
					}
					echo "<form name=\"deleteform\" method=\"post\">\n";
					echo "<input type=\"hidden\" name=\"clientid\" value=\"".$_REQUEST['clientid']."\">\n";
					echo "</form>\n";
					$displayOriginal = false;
				}
			}
			if($stmt != "nothing"){
			}
			echo "<hr>\n";
		}
	}
	
	if($displayOriginal) {
		echo "<br><br>";
		$stmt = "SELECT client_id,client_username,client_password FROM accounts ORDER BY client_id";
		$result = DoSQL($stmt);
	
		echo "<form name=\"clients\" action=\"".htmlentities($_SERVER['PHP_SELF'])."\" method=\"post\">";
		echo "I want to ";
		echo "<select name=\"task\">\n";
		echo "<option value=\"show\">Show</option>\n";
		echo "<option value=\"modify\">Modify</option>\n";
		echo "<option value=\"delete\">Delete</option>\n";
		echo "</select>\n";
	
		echo " the client ID of ".$_REQUEST['clientid'];
		echo ".\n";
		echo "<input type=\"hidden\" name=\"clientid\" value=\"".$_REQUEST['clientid']."\">\n";
		echo "<input type=\"submit\" name=\"submit\" value=\"Go\">\n";
		echo "</form>\n";
	}
?>
<hr>
<form name="returnform" action="books.php" method="post">
<input type="submit" name="return" value="Return">
</form>
</body>
</html>

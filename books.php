<!DOCTYPE html>
<!--
	- - - - - - - - - - - -

	Title: A Novel Concept

	Version: 1.1

	Date: September 2014

	- - - - - - - - - - - -
	"A Novel Concept"
	Books Page
	Dylan Huculak
	Jeff Codling
>>>>
	(IF YOU ADD TO CODE, INCLUDE NAME HERE)
	2014 -->
<html lang="en">  
  <head>
	<meta charset="UTF-8" />
	<title>A Novel Concept</title>
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
	<!-- HTML5 shim for IE backwards compatibility -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  
<?php				// PHP Login system by Jeff Codling
// *********** PHP Server Credentials
define("SERVERNAME","team01project.db.6194647.hostedresource.com");
define("DATABASENAME","team01project");
define("USR","team01project");
define("PASS","Pass4team01!");

// *********** PHP Settings Variable List
$timelimit = 300;			// Amount of time in seconds that a client will stay logged in without doing something. (300 = 5 mins.)
$itemsPerPage = 20;		// Number of items to display per page

// *********** PHP Function Area

// Function DisplayTopForm - Displays the login area at the top right of the page when not logged in
function DisplayTopForm($username,$userplace,$password,$passplace) { ?>
	<form name="login" action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
		<span>Username</span><input type="text" name="username"<?php
			if($username != "") {
				echo "value=\"".$username."\"";
			} else {
				echo "placeholder=\"".$userplace."\"";
			}
		?> autofocus>
		<span>Password</span><input type="password" name="password"<?php
			if($password != "") {
				echo "value=\"".$password."\"";
			} else {
				echo "placeholder=\"".$passplace."\"";
			}
		?>>
		<input type="submit" name="login" value="Login">
		<input type="submit" name="reg" value="Register">
	</form><?php
}

// Function GetUserInfo - Checks username and password against database
function GetUserInfo($username,$password) {
	$con = mysqli_connect(constant('SERVERNAME'), constant('USR'), constant('PASS'), constant('DATABASENAME'));
	if($con->connect_error) {
		die("Connection failed: " . $con->connect_error);			// Fail if we can't connect
	}
	$stmt = "SELECT client_id,client_username,client_password FROM accounts";
	$result = mysqli_query($con,$stmt);											// Get all client records
	$con->close();																					// ...and close the connection
	if($result == "") {
		die("Query failed.<br>");
	}
	$flag = false;																					// Assume failure to find username

// 	echo "<div class='dialog'><span>GetUserInfo Function</span>\n";	// Debug dialog container

// 	echo "Searching records...<br>";
	
	while($row = mysqli_fetch_array($result)) {							// Check all records for entered username
		if($row['client_username'] == $username) {						// Found username
// 			echo "Found username...<br>";
			if(!strcmp($password, $row['client_password'])) {		// Check stored hash against entered password hash
				$flag = $row['client_id'];																// Match so return value should be client_id
// 				echo "Found password match: ".$row['client_id']." :: ".$row['client_username']."<br>";
				$_SESSION['clientid'] = $row['client_id'];
				$_SESSION['timestamp'] = time();
				$_SESSION['username'] = $row['client_username'];
				header("Location: ".htmlentities($_SERVER['PHP_SELF']));
				break;
			} else {
// 				echo "Wrong password!<br>";
				$flag = -1;
				break;
			}
		}
	}
	
// 	echo "</div>\n";		// End of debug dialog container
	
	return $flag;																						// Return false or client_id of successful record
}
// Function RegisterUser
function RegisterUser($username,$password,$submission) {	// Registration of new user
	if($submission) {																				// Is this the first time around or not
	
		echo "<div class='dialog'><span>RegisterUser Function</span>\n";	// Debug dialog container
		
		$flag = true;
		echo "Registering ".$_POST['username'].".<br>";
		
		$con = mysqli_connect(constant('SERVERNAME'), constant('USR'), constant('PASS'), constant('DATABASENAME'));
		if($con->connect_error) {
			die("Connection failed: " . $con->connect_error);
		}

		$stmt = "SELECT client_id FROM accounts";
		
		$result = mysqli_query($con, $stmt);									// Get all records

		$highestClient = 0;
		if($result != "") {																		// Find highest client_id
			while($row = mysqli_fetch_array($result)) {
				if($row['client_id'] > $highestClient)
					$highestClient = $row['client_id'];
			}
		}
		$highestClient++;																			// ...and add one to it for new one
		
		$OKToGo = true;																				// Assume we are good to go
		$result = mysqli_query($con,"SELECT client_id,client_username FROM accounts");
		while($row = mysqli_fetch_array($result)) {						// Check for existing username
			echo $row['client_username']."<br>";
			if($row['client_username'] == $_POST['username']) {
// 					echo "Username already exists. Please enter a new one.<br>";
				$OKToGo = false;																	// Exists so fail and take appropriate action
				break;
			}
		}
		if($OKToGo) {																					// Username is valid so insert new account info into DB
			$hashedPassword = $_POST['password'];								// New password

			$stmt = "INSERT INTO accounts (client_id, client_username, client_password) ";
			$stmt = $stmt."VALUES (".$highestClient.", \"".$_POST['username']."\", \"".$hashedPassword."\")";
// 				echo $stmt."<br><br>";
			$result = mysqli_query($con,$stmt);									// Do the insert

			if($result) {																				// Check if successful
// 					echo "Successful<br>";
				$flag = true;
				$_SESSION['clientid'] = $highestClient;
				$_SESSION['timestamp'] = time();
				$_SESSION['username'] = $_POST['username'];
				header("Location: ".htmlentities($_SERVER['PHP_SELF']));
			} else {
				echo "Not successful<br>";
				$flag = false;
			}
		} else {										// Username exists so ask for a new one
			DisplayLoginForm("Enter a different username and password below:","Create an Account",NULL,NULL,"register","Register");
		}
		
		echo "</div>\n";		// End of debug dialog container
		
		$con->close();							// Done with the DB
	} else {											// Ask for username and password to register
		$flag = false;
		DisplayLoginForm("Enter your new account's username and password below:","Create an Account",$username,$password,"register","Register");
	}
	return $flag;									// Returns TRUE if successful, FALSE if not
}

// Function DisplayLoginForm:
function DisplayLoginForm($dialog,$title,$username,$password,$buttonname,$buttontitle) {
	?>
	<form class="loginform" action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
		<div class="logintitle"><span><?php echo $title; ?></span></div>
		<div class="formdialog"><?php echo $dialog; ?></div>
		<div class="fieldlable">Username</div>
		<input type="text" name="username" <?php
			if(isset($username) || $username != "")
				echo "value=\"".$username."\"";								// If username defined, display it
			else
				echo "placeholder=\"Enter a username here\"";
		?> autofocus><br>
		<div class="fieldlable">Password</div>
		<input type="password" name="password" <?php
			if(isset($password) || $password != "")
				echo "value=\"".$password."\"";								// If password defined, display it
			else
				echo "placeholder=\"Enter a password here\"";
		?>><br>
		<input class="submitbutton" type="submit" name="<?php echo $buttonname ?>" value="<?php echo $buttontitle; ?>">
		<input class="logoutbutton" type="submit" name="cancel" value="Cancel">
		<?php
		if(isset($_SESSION['clientid'])) {
			echo "<input class=\"logoutbutton\" type=\"submit\" name=\"logout\" value=\"Logout\">";
		}
		if(isset($_POST['register']))	{				// If this is for registering then set the appropriate input flag
			echo "<input type=\"hidden\" name=\"register\" value=\"true\">";
		}
		?>
	</form>
	<?php
}
// Function Display Paging
function DisplayPaging($page,$totalrows) {
	echo "<div class='paging'><span>";
	if($page==0) {
	echo "first";
	} else {
		echo "<a href=\"?page=0";
		if(isset($_REQUEST['sort'])) {
			echo "&sort=".$_REQUEST['sort'];
		}
		echo "\">first</a>";
	}
	echo " | ";
	if($page == 0) {
		echo "X";
	} else {
		echo "<a href=\"?page=".($page-1);
		if(isset($_REQUEST['sort'])) {
			echo "&sort=".$_REQUEST['sort'];
		}
		echo "\">".$page."</a>";
	}
	echo " | ".($page+1)." | ";
	if(($page+1) >= $totalrows) {
		echo "X";
	}else {
		echo "<a href=\"?page=".($page+1);
		if(isset($_REQUEST['sort'])) {
			echo "&sort=".$_REQUEST['sort'];
		}
		echo "\">".($page+2)."</a>";
	}
	echo " | ";
	if(($page+1)>=$totalrows) {
		echo "last";
	} else {
		echo "<a href=\"?page=".($totalrows-1);
		if(isset($_REQUEST['sort'])) {
			echo "&sort=".$_REQUEST['sort'];
		}
		echo "\">last</a>";
	}
	echo "</span></div>\n";
}
?>
  <body>
	<div id="box">
		<div id="headerBg">
			<div id="header"><img src="images/headerBgRight.jpg" align="right">
				<div id="links" class="logintop"> <!-- Login system by Jeff Codling -->
				
					<?php
					session_start();

					if(isset($_POST['logout']) || isset($_POST['cancel'])) {		// Client logged out so
						session_destroy();						// destroy the session
						unset($_POST);								// clear all variables
						header("Location: ".htmlentities($_SERVER['PHP_SELF']));	// and reload the page to reset
					}

					if(isset($_SESSION['clientid'])) {
						if(isset($_SESSION['timestamp'])) {		// Check for timelimit for auto logoff
							$sessionTime = $_SESSION['timestamp'] + $timelimit;
// 							echo $sessionTime." > ".$_SERVER['REQUEST_TIME']." = ".($sessionTime-$_SERVER['REQUEST_TIME'])."<br>\n";
							if($sessionTime < $_SERVER['REQUEST_TIME']) {
								session_destroy();
								unset($_POST);
								header("Location: ".htmlentities($_SERVER['PHP_SELF']));
							}
						}
					
						if(isset($_POST['manage'])) {
							
							// load client management page here
							echo "<form id=\"manform\" name=\"mancon\" action=\"manageclient.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"clientid\" value=\"".$_SESSION['clientid']."\">";
							echo "</form>\n";?>
							<script>
								document.getElementById('manform').submit(); // SUBMIT FORM
							</script>
							<?php
						}
						$clientid = $_SESSION['clientid'];												// Client logged in so seesion time reset
						$_SESSION['timestamp'] = time();
						echo "<form name=\"logout\" action=\"".htmlentities($_SERVER['PHP_SELF'])."\" method=\"post\">";
						echo "<span class=\"welcomemessage\">Welcome ";
						if(isset($_SESSION['username'])) {
							echo $_SESSION['username'];
						} else {
							echo "UNDEFINED";
						}
						echo " <input type=\"submit\" name=\"logout\" value=\"Logout\">";
						echo "<input type=\"submit\" name=\"manage\" value=\"Manage\">";
						echo "</span></form>";
					} else {
						$clientid = 0;
						DisplayTopForm("","enter username","","enter password");
					}

					if(isset($_POST['reg'])) {								// Registration button clicked
						if(isset($_POST['username'])) {
							$username = $_POST['username'];
						} else {
							$username = "";
						}
						if(isset($_POST['password'])) {
							$password = $_POST['password'];
						} else {
							$password = "";
						}
						RegisterUser($username,$password,false);
					}
					if(isset($_POST['register'])) {		// Have we filled out the username and password?
						RegisterUser($_POST['username'],$_POST['password'],true);		// If so then carry on inside function
					} else {

						if(isset($_POST['login'])) {																// Is the login form submitted?

// 						echo "<div class='dialog'><span>Main Function</span>\n";		// Debug information container

							if($_POST['username'] != "" && $_POST['password'] != "") {// Is the username and password filled in?
// 								echo "Credentials Entered<br>";
// 								echo "Username: ".$_POST['username']."<br>";
// 								echo "Password: ".$_POST['password']."<br>";
								$user = true;								// We have a username and password
								$pass = true;
								$userid = GetUserInfo($_POST['username'],$_POST['password']);
								if($userid != false) {								// ...and we found a record
									if($userid == -1) {
										echo "<div class=\"dialog\">Username/Password wrong.</div>";
										$user == -1;
									} else {
//			 							echo "Record found. :: Client ID is ".$_SESSION['clientid']."<br>";
										$_SESSION['timestamp'] = time();
										$clientid = $userid;
									}
								} else {										// Couldn't find username
									echo "<div class=\"dialog\">Username/Password wrong.</div>";
								}
							} else {
								if($_POST['username'] != "") {
//	 							echo "Please enter a password<br>";			// No password entered
								$pass = NULL;
							} else {
//	 							echo "Please enter an username<br>";		// No username entered
								$user = NULL;
							}
							if($clientid == 0) {
								if(isset($_POST['submit'])) {
									if($_POST['password'] == "") {
										DisplayLoginForm("Password is required.","Login",$_POST['username'],NULL,"submit","Login");
									} else if($_POST['username'] == "") {
										DisplayLoginForm("Username is required.","Login",NULL,$_POST['password'],"submit","Login");
									}
								}
							}
						}

// 						echo "</div>\n";		// End of debug information container

					}
				}
?>

<!-- 
					<a href="index.html">Home</a> | 
					<a href="wishList.html">Wish List (0)</a> | 
					<a href="myAccount.html">My Account</a> | 
					<a href="cart.html">Shopping cart (0)</a> | 
					<a href="checkout.html">Checkout</a>
 -->
				</div>
				<div id="bookMenu">
<!-- 
					<ul>
						<li>
							<a href="books.html/#hardcover">Hardcover</a>
						</li>
						<li>
							<a href="books.html/#softcover">Softcover</a>
						</li>
						<li>
							<a href="books.html/#eBooks">E-Books</a>
						</li>
						<li>
							<a href="books.html/#magazines">Magazines</a>
						</li>
					</ul>
 -->
				</div>
			</div>
		</div>
		<div id="contentBg">
			<div id="content">			<!-- Bookshelf Grid by Jeff Codling -->
			
					<?php
// *********** PHP Display Books ***********
					$servername = constant('SERVERNAME');
					$username = constant('USR');
					$password = constant('PASS');
					$databaseName = constant('DATABASENAME');

					$con = mysqli_connect($servername, $username, $password, $databaseName);
					if($con->connect_error) {
						die("Connection failed: " . $con->connect_error);
					}
					$result = mysqli_query($con, "SELECT count(*) FROM books");		// Get the total number of items
					if(! $result ) {
						die("Could not get count of books.");
					}
					$row = mysqli_fetch_array($result, MYSQL_NUM);								// Do the query
					
					$totalItems = $row[0];
					$totalrows = $totalItems/$itemsPerPage;														// Calculate number of pages
					
					if(isset($_REQUEST['page'])) {					// If a page is specified set accordingly
						$page = $_REQUEST['page'];
					} else {
						$page = 0;
					}

					$totalOnPage = $totalItems-($page*$itemsPerPage);
					
// 					echo "<hr>:totalrows: ".$totalrows." :Total Rows: ".$row[0]." :Page: ".$page." :totalOnPage: ".$totalOnPage."<hr>";

					// Build the SQL statement
					// SQL to get the current page of items with needed joins for authors
					$stmt = "SELECT b.book_id,book_title,book_plot,book_price,author_last,author_first,author_middle FROM books b";
					$stmt = $stmt." JOIN book_authors j ON b.book_id=j.book_id JOIN authors a ON j.author_id=a.author_id";
					
					$whereclause = "";											// Start with blank additional statements
					$limitclause = "";											// and build as needed
					$orderbyclause = "";

					if(isset($_REQUEST['id'])){							// Add WHERE statement if a single item is selected
						$whereclause = " WHERE b.book_id='".$_REQUEST['id']."'";
					} else {																// If all items selected use the LIMIT statement to make pages
						$limitclause = " LIMIT ".($page*$itemsPerPage).", ".$itemsPerPage;
					}
					if(isset($_REQUEST['sort'])) {		// Add ORDER BY if sorting is selected
						$orderbyclause = " ORDER BY ";
						switch ($_REQUEST['sort']) {
							case "price" :
								$orderbyclause.="book_price";
								break;
							case "authorlast" :
								$orderbyclause.="author_last";
								break;
							case "authorfirst" :
								$orderbyclause.="author_first";
								break;
							default :
								$orderbyclause.="book_title";
						}
					} else {
						$orderbyclause = " ORDER BY ";
						$orderbyclause.="book_title";
					}
					
					$stmt.= $whereclause.$orderbyclause.$limitclause;		// build appropriate SQL statement in proper order
					
// 					echo "<hr>".$stmt."<hr>";
					
					$result = mysqli_query($con,$stmt);			// Do the query
					
					if(isset($_REQUEST['id'])){							// If a single item is selected show only one item
						if($row = mysqli_fetch_array($result)) {
							echo "<div class='fullbook'>";
							echo "<img class='coverart' src='images/";
							if(file_exists("images/".$row['book_id'])) {
								echo $row['book_id'];
							} else {
								echo "coverart";
							}
							echo ".jpg'>";
							echo "<div class='id'>Book ID: ".$row['book_id']."</div>";
							echo "<div class='title'>Title: ".$row['book_title']."</div>";
							echo "<div class='first'>Author: ".$row['author_last'].", ".$row['author_first']." ".$row['author_middle']."</div>";
							echo "<div class='plot'>Plot: ".$row['book_plot']."</div>";
							echo "<div class='price'>Price: ";
							printf("$ %1\$.2f",$row['book_price']);
							echo "</div>";
							echo "</div>";
							echo "<a href='books.php'><div class='backbutton'>back</div></a>\n";
						} else {
							echo "<div class='fullbook'><div class='title'>Error</div></div></div>\n";
						}
	
					} else {																// Else show the page of items
						echo "<div class='booklisttitle'><span>Available Books";
						echo "<form name=\"sortby\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">";	// Sorting option list
						echo "Sorted by <select name=\"sort\" onchange=\"this.form.submit()\">";
						echo "<option value=\"title\"";
						if(isset($_REQUEST['sort'])) {
							if($_REQUEST['sort'] == 'title') {
								echo " selected";
							}
						} else {
							echo " selected";
						}
						echo ">Title</option>";
						echo "<option value=\"price\"";
						if(isset($_REQUEST['sort'])) {
							if($_REQUEST['sort'] == 'price') {
								echo " selected";
							}
						}
						echo ">Price</option>";
						echo "<option value=\"authorlast\"";
						if(isset($_REQUEST['sort'])) {
							if($_REQUEST['sort'] == 'authorlast') {
								echo " selected";
							}
						}
						echo ">Author Last Name</option>";
						echo "<option value=\"authorfirst\"";
						if(isset($_REQUEST['sort'])) {
							if($_REQUEST['sort'] == 'authorfirst') {
								echo " selected";
							}
						}
						echo ">Author First Name</option>";
						echo "</select> ";
						echo "</form></span>";
						echo "</div>";												// End of sorting options
						echo "<div class='bookcontainer'>";
						if($totalOnPage>10) {										// If the items will go off screen add pagination to the top
							DisplayPaging($page,$totalrows);
						}
						while($row = mysqli_fetch_array($result)) {
							echo "<div class='book'>";
							echo "<a href='books.php?id=".$row['book_id']."'>";
							echo "<div class='coverart'><img src='images/";
							if(file_exists("images/".$row['book_id'].".jpg")) {
								echo $row['book_id'];
							} else {
								echo "coverart";
							}
							echo ".jpg'></div>\n";
							echo "<div class='title'>".$row['book_title']."</div>\n";
							echo "<div class='author'>".$row['author_first']." ";
							echo strtoupper(substr($row['author_middle'], 0, 1));
							echo ". ".$row['author_last']."</div>\n";
							echo "</a></div>\n";
						}
						echo "</div>\n";
						DisplayPaging($page,$totalrows);
					}
					$con->close();

					?>

				</div> <!-- End bookshelf grid addition by Jeff Codling -->
				
			</div>
		<div id="footerBg">
			<div id="footer">
				<div id="footerInfo"> <!--LEFT COLUMN-->
					<h3>INFORMATION</h3>
					<ul class="footerList">
						<li><a href="info.html/#about">About Us</a></li>
						<li><a href="info.html/#delivery">Delivery Information</a></li>
						<li><a href="info.html/#privacy">Privacy Policy</a></li>
						<li><a href="info.html/#terms">Terms & Conditions</a></li>
					</ul>
				</div>
				<div id="footerAccount"> <!--RIGHT COLUMN-->
					<h3>ACCOUNT</h3>
					<ul class="footerList">
						<li><a href="account.html/#myAccount">My Account</a></li>
						<li><a href="account.html/#history">Order History</a></li>
						<li><a href="account.html/#wishList">Wish List</a></li>				
					</ul>
				</div>
				<div id="footerService"> <!--CENTER COLUMN-->
					<h3>CUSTOMER SERVICE</h3>
					<ul class="footerList">
						<li><a href="service.html/#siteMap">Site Map</a></li>
						<li><a href="service.html/#returns">Returns</a></li>
						<li><a href="service.html/#contact">Contact Us</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
  </body>
</html>
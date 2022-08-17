<?php
	session_start();
?>

<!DOCTYPE html>
<html>

<body>
	
<?php
	include("HangmanConnectInfo.php");
	session_unset();
	
	if(array_key_exists('Login', $_POST)) {
		//echo "The user tried to log in <br>";
		$Username = $_POST["Username"];
		$UserPassword = $_POST["Password"];
		//echo "Username: " . $Username . " . Password: " . $UserPassword;
		
		if (!empty($Username) && !empty($UserPassword)) {
			//here is where we do all the logic for actually logging in.

			//Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
				

			//check connection
			if ($conn->connect_error){
				die("Connection failed: " . $conn->connect_error);
			}


			$sql = "SELECT Salt, HashPassword FROM User WHERE Username = '" . $Username . "'";

			$result = $conn->query($sql);

			if ($result->num_rows == 0) {
				echo"<br>This username doesn't exist, please try again.";
			}
			else {
				$row = $result->fetch_assoc();
				$dbSalt = $row["Salt"];
				$dbHashed = $row["HashPassword"];

				$usersHash = hash('sha256', $UserPassword . $dbSalt);

				if ($usersHash == $dbHashed) {
					//the password and username are correct, redirect them to the game page
					$_SESSION["Username"] = $Username;
					header("Location: Hangman.php");
					$conn->close();
					exit();
				}
				else {
					//the password does not match the username's password, so tell them an error.
					echo"<br>Login failed, password does not match password for that username.";
				}
			}
		
		
			$conn->close();
		} 
		else {
			echo "<br> The username and/or pass are empty.";
		}
	}
	
?>


	<form method="post">
		<p>Username: <input type="text" name="Username"/></p>
		<p>Password: <input type="text" name="Password"/></p>
		
		<input type="submit" value="Login" name="Login"/>
		
		
	</form>

	
</body>

</html>
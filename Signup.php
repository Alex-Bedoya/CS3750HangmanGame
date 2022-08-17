<?php
	session_start();
?>

<!DOCTYPE html>
<html>

<body>

<?php
	include("HangmanConnectInfo.php");
	session_unset();
	
	if(array_key_exists('signup', $_POST)) {
	
		$Username = $_POST["Username"];
		$UserPassword = $_POST["Password"];
		$UserConPass = $_POST["PasswordConfirm"];

		if (empty($Username) || empty($UserPassword) || empty($UserConPass)) {
			echo"<br>Error: Please fill in all three fields.";
			//return;
		}
		else {

			if ($UserPassword != $UserConPass) {
				echo "<br>Error: The password and Password confirmation must match";
			}
			else {
				//do logic for hashing, and committing to database
				
				$salt = random_bytes(4);
				
				$hashedPass = hash('sha256', $UserPassword . $salt);


				//Create connection
				$conn = new mysqli($servername, $username, $password, $dbname);
				

				//check connection
				if ($conn->connect_error){
					die("Connection failed: " . $conn->connect_error);
				}

				$sql = "SELECT Username FROM User WHERE Username = '" . $Username . "'";
				
				$result = $conn->query($sql);
				

				if ($result->num_rows > 0) {
					echo"<br>This username already exists, please try a different one";
				} 
				else {
					//if username doesn't exist, insert username and password into the db. 
					$sql = "INSERT INTO User (Username, Salt, HashPassword) VALUES ('" . $Username . "', '" . $salt . "', '" . $hashedPass . "')";
				
					if ($conn->query($sql) === TRUE) {
						//person correctly signed up and logged in, take them to the game
						echo "<br>New record created successfully";
						$_SESSION["Username"] = $Username;
						header("Location: Hangman.php");
						exit();
					}
					else {
						//error for some reaosn.
						echo "<br>Error: " . $sql . "<br>" . $conn_error;
					}
				}
				
				$conn->close();
				
			}
		}
		
	}
	
?>

	<form method="post">
		<p>Username: <input type="text" name="Username"/></p>
		<p>Password: <input type="text" name="Password"/></p>
		<p>Confirm Password: <input type="text" name="PasswordConfirm"/></p>
		
		<input type="submit" value="Sign Up" name="signup"/>
		
		
	</form>

	
</body>

</html>
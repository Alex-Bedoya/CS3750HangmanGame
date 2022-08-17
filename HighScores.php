<?php
	session_start();
?>

<!DOCTYPE html>
<html>

<body>

<?php 
	include("HangmanConnectInfo.php");

	if(array_key_exists('Logout', $_POST)) {
		//remove all session variables
		session_unset();

		//delete 
		session_destroy();
		header("Location: StartPage.html");
		exit();
	}


	$wordID = $_SESSION['WordID'];

	//echo"<br> this is the wordID: " . $wordID;

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}

	$sql = "SELECT Word FROM WordList WHERE WordID = '$wordID'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		//this just makes sure that it received a word.
		$row = mysqli_fetch_row($result);
		$Word = strtoupper($row[0]);
	}

	




	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error){
		echo"<br>connection failed";
		die("Connection failed: " . $conn->connect_error);
	}

	$WordLen = $_SESSION["WordLength"];

	echo"<h2>High scores for the hangman game for words with a length of $WordLen</h2>";
	echo"The word you guessed correctly was: " . $Word;

	//echo"<br>this is the word length: " . $WordLen;

	$sql = "SELECT Username, NumberOfFails 
			FROM HighScores 
			WHERE WordLength = $WordLen
			ORDER BY NumberOfFails
			LIMIT 10";


	$result = $conn->query($sql);


	echo "<table border='1'>
		<tr>
		<th>Username</th>
		<th>Fails</th>
		</tr>";


	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			echo "<tr>
			<th>" . $row["Username"] . "</th>
			<th>" . $row["NumberOfFails"] . "</th>
			</tr>";
		}
	}
	else {
		echo "0 results found";
	}

	$conn->close();


?>

<br>
	<p>Do you want to play again?</p>
	<form action="Hangman.php" method="post">
		<input type="submit" value="Play Again" name="PlayAgain"/>
	</form>
	<br><br>
	<form action="Hangman.php" method="post">
		<input type="submit" value="Logout" name="Logout"/>
	</form>

	<p></p>
<br>
</body>

</html>
<?php
	session_start();
?>

<!DOCTYPE html>
<html>

<body>

<?php 
	echo"<p>Welcome to " . $_SESSION["Username"] . "'s Hangman game!</p>"; 
?>

	<form method="post">
		<p>Guess a letter: <input type="text" name="UserGuess"/></p>
		
		<input type="submit" value="Make Guess" name="MakeGuess"/>
	</form>

	<br>

	<form method="post">
		<input type="submit" value="Logout" name="Logout"/>
	</form>

<?php
	include("HangmanConnectInfo.php");
	
	
	if(array_key_exists('PlayAgain', $_POST) || array_key_exists('Login', $_POST) || array_key_exists('signup', $_POST)) {
		unset($_SESSION['guessedLetters']);
		unset($_SESSION['fails']);
		unset($_SESSION['WordID']);
		unset($_SESSION['Revealed']);
	}


	if(array_key_exists('Logout', $_POST)) {
		//remove all session variables
		session_unset();

		//delete 
		session_destroy();
		header("Location: StartPage.html");
		exit();
	}


	if(!isset($_SESSION['guessedLetters'])) {
		$_SESSION['guessedLetters'] = array();
	}

	$guessedLetters = $_SESSION['guessedLetters'];
	$UserGuess = strtoupper($_POST["UserGuess"]);
	$Guesslen = strlen($UserGuess);

	$tempGuessedLetters = $guessedLetters;
	
	if(array_key_exists('MakeGuess', $_POST) && $Guesslen == 1) {
		if (!in_array($UserGuess, $guessedLetters)) {
			$guessedLetters[] = $UserGuess;
		}
		else {
			echo"<br> You have already guessed that letter";
		}
	}


	if(!isset($_SESSION['fails'])) {
		$_SESSION['fails'] = 0;
	}
	
	//get the word from the database
	if(!isset($_SESSION['WordID'])) {
		$_SESSION['WordID'] = rand(1,100);
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

	
	
	
	$Wordlen = strlen($Word);
	
	$WordArr = str_split($Word);

	$Revealed = array();

	if(!isset($_SESSION['Revealed'])) {
		for ($x = 0; $x < $Wordlen; $x++) {
			$Revealed[] = "_";
		}
		$_SESSION['Revealed'] = $Revealed;
	}
	else {
		$Revealed = $_SESSION['Revealed'];
	}

		if ($UserGuess != "") {

		if ($Guesslen != 1) {
			echo "<br> Error: You need to make a single letter guess, try again. <br>";
			//return;
		}
		else {
			$isFail = true;
			for ($x = 0; $x < $Wordlen; $x++) {
				//echo"<br> Word Array letter: " . $WordArr[$x] . "  .  UserGuess Letter: " . $UserGuess . "  .  Revealed char: " . $Revealed[$x];

				if ($WordArr[$x] == $UserGuess) {
					$Revealed[$x] = $WordArr[$x];
					$isFail = false;
				}
			}

			if ($isFail && !in_array($UserGuess, $tempGuessedLetters)) {
				$_SESSION['fails']++;
				echo "<br> Incorrect, that letter was not in the word";
			}
		}

	}

	echo"<br> Total missed guesses: " . $_SESSION['fails'];

	echo"<br> revealed letters: ";
	for ($x = 0; $x < $Wordlen; $x++) {
		echo $Revealed[$x] . " ";
	}

	echo"<br> Letters Guessed: ";
	for ($x = 0; $x < count($guessedLetters); $x++) {
		echo $guessedLetters[$x] . " ";
	}
	

	$isFinished = true;
	for ($x = 0; $x < $Wordlen; $x++) {
		if ($Revealed[$x] == "_") {
			$isFinished = false;
			break;
		}
	}

	if ($isFinished) {
		//here is where we show the high scores page, after the word is complete
		echo"<br> the word is complete, good job!";

		$_SESSION["WordLength"] = $Wordlen;

		$sql = "INSERT INTO HighScores (Username, WordLength, NumberOfFails) VALUES ('" . $_SESSION["Username"] . "', '" . $Wordlen . "', '" . $_SESSION['fails'] . "')";
				
		if ($conn->query($sql) === TRUE) {
			//person correctly signed up and logged in, take them to the game
			//echo "<br>New record created successfully";
			//$_SESSION["Username"] = $Username;
			header("Location: HighScores.php");
			exit();
		}
		else {
			//error for some reaosn.
			echo "<br>Error: " . $sql . "<br>" . $conn_error;
		}

	}
		
	$_SESSION['Revealed'] = $Revealed;
	$_SESSION['guessedLetters'] = $guessedLetters;

	$conn->close();




?>



	
</body>

</html>
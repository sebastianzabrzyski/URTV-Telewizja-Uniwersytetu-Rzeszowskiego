<?php

if( isset( $_POST['id'] ) )
{
	$user_id = "";
	require_once("functions.php");
	sprawdzZalogowanie("","");
	$conn = polaczDB();
	$user_id = $conn->real_escape_string($user_id);
	$query = "SELECT Streamkey_active
	FROM streams
	WHERE User_ID ={$user_id};";
	$result = queryDB($conn,$query);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$streamkey = $row["Streamkey_active"];
			echo $streamkey;
			exit;
		}
	} else {
		echo "";
		exit;
	}
}
?>

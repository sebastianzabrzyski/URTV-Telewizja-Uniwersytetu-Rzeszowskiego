<?php
if( isset( $_POST['id'] ) )
{
	$user_id = "";
	require_once("functions.php");
	sprawdzZalogowanie("","");
	$new_key = mt_rand(1000000000, 9999999999);
	$conn = polaczDB();
	$query = "UPDATE streams SET Streamkey_active = {$new_key}, Streamkey_last = NULL
	$user_id = $conn->real_escape_string($user_id);
	WHERE User_ID ={$user_id};";
	$result = queryDB($conn,$query);
	if ($result === true) {
		echo $new_key;
		exit;
	} else {
		echo "";
		exit;
	}
}
?>

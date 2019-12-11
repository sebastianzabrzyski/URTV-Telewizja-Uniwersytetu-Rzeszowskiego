<?php

if( isset( $_POST['id'] ) )
{
    $id_uzytkownika = "";
    require_once("functions.php");
    sprawdzZalogowanie("","");
    $polaczenie_BD = polaczDB();
    $id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
    $zapytanie_SQL = "SELECT Streamkey_active
	FROM streams
	WHERE User_ID ={$id_uzytkownika};";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if ($wynik->num_rows > 0) {
        // output data of each row
        while($wiersz = $wynik->fetch_assoc()) {
            $streamkey = $wiersz["Streamkey_active"];
            echo $streamkey;
            exit;
        }
    } else {
        echo "";
        exit;
    }
}
?>

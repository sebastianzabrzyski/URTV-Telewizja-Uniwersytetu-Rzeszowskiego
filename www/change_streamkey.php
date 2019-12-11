<!-- Skrypt generujący nowy kod transmisji na żywo -->

<?php
if( isset( $_POST['id'] ) )
{
    $id_uzytkownika = "";
    require_once("functions.php");
    sprawdzZalogowanie("","");
    $new_key = mt_rand(1000000000, 9999999999);
    $polaczenie_BD = polaczDB();
    $zapytanie_SQL = "UPDATE streams SET Streamkey_active = {$new_key}, Streamkey_last = NULL
	$id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
	WHERE User_ID ={$id_uzytkownika};";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
    if ($wynik === true) {
        echo $new_key;
        exit;
    } else {
        echo "";
        exit;
    }
}
?>

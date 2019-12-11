<?php

require_once("functions.php");
ustawSesje();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $kod = $_GET['mail'];
    $polaczenie_BD = polaczDB();
    $kod = $polaczenie_BD->real_escape_string($kod);
    $zapytanie_SQL = "DELETE FROM newsletter WHERE md5(Email) = '{$kod}';";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if($polaczenie_BD->affected_rows > 0) {

        pokazKomunikat("Zostałeś wypisany z newslettera");
        przekierowanie("./index.php");

    } else {

        pokazKomunikat("Nie odnaleziono takiego adresu e-mail w naszej bazie");
        przekierowanie("./index.php");
    }
}
?>

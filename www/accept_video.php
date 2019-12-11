<!-- Skrypt do akceptowania materiałów wideo przesłanych przez użytkowników -->


<?php
require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_filmu = $_GET['id'];
    sprawdzZalogowanie("","./login.php?return=accept_video.php?id={$id_filmu}");

    if($uprawnienia == "Administrator") {
        $polaczenie_BD = polaczDB();
        $id_filmu = $polaczenie_BD->real_escape_string($id_filmu);
        $zapytanie_SQL = "UPDATE movies SET Verified = 'Tak' WHERE ID = '{$id_filmu}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        if($polaczenie_BD->affected_rows > 0) {
            pokazKomunikat("Film został zaakceptowany");
            przekierowanie("./admin.php");
        } else {
            pokazKomunikat("Nie udało się zaakceptować filmu");
            przekierowanie("./admin.php");
        }
    } else {
        pokazKomunikat("Nie posiadasz wymaganych uprawnień");
        przekierowanie("./index.php");
    }
}
?>

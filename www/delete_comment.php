<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $id_komentarza = $_GET['id'];
  $tryb = $_GET['tryb'];

  sprawdzZalogowanie("","./login.php?return=delete_comment.php?tryb={$tryb}&id={$id_komentarza}");

  $polaczenie_BD = polaczDB();
  $id_komentarza = $polaczenie_BD->real_escape_string($id_komentarza);
  if($tryb == "film") {
    $zapytanie_SQL = "SELECT User_ID, Movie_ID FROM comments WHERE ID = {$id_komentarza};";
  } else {
    $zapytanie_SQL = "SELECT User_ID, Stream_ID FROM comments_streams WHERE ID = {$id_komentarza};";
  }

  $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
  $wiersz = $wynik->fetch_assoc();
  $id_wlasciciela = $wiersz["User_ID"];

  if($tryb == "film") {
    $id_filmu = $wiersz["Movie_ID"];
  } else {
    $id_filmu = $wiersz["Stream_ID"];
  }

  if($id_wlasciciela == $id_uzytkownika || $uprawnienia == "Administrator") {
    if($tryb == "film") {
      $zapytanie_SQL = "DELETE FROM comments WHERE ID = {$id_komentarza};";
    } else {
      $zapytanie_SQL = "DELETE FROM comments_streams WHERE ID = {$id_komentarza};";
    }

    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
    if($polaczenie_BD->affected_rows > 0) {

      pokazKomunikat("Komentarz został usunięty");
      if($tryb == "film") {
        przekierowanie("./video.php?id={$id_filmu}");
      } else {
        przekierowanie("./stream.php?id={$id_filmu}");
      }
    } else {

      pokazKomunikat("Nie udało się usunąć komentarza");

      if($tryb == "film") {
        przekierowanie("./video.php?id={$id_filmu}");
      } else {
        przekierowanie("./stream.php?id={$id_filmu}");
      }
    }

  } else {
    pokazKomunikat("Nie jesteś autorem komentarza");
    if($tryb == "film") {
      przekierowanie("./video.php?id={$id_filmu}");
    } else {
      przekierowanie("./stream.php?id={$id_filmu}");
    }
  }
}
?>

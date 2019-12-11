<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $id_filmu = $_GET['id'];
  $tryb = $_GET['tryb'];
  sprawdzZalogowanie("","./login.php?return=delete_video.php?tryb={$tryb}&id={$id_filmu}");

  $polaczenie_BD = polaczDB();
  $id_filmu = $polaczenie_BD->real_escape_string($id_filmu);
  if($tryb == "film") {
    $zapytanie_SQL = "SELECT User_ID, Filename FROM movies WHERE ID = '{$id_filmu}';";
  } else {
    $zapytanie_SQL = "SELECT User_ID, Filename FROM streams WHERE ID = '{$id_filmu}';";
  }

  $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

  if ($wynik->num_rows > 0) {

    while($wiersz = $wynik->fetch_assoc()) {
      $id_wlasciciela = $wiersz['User_ID'];
      $nazwa_pliku = $wiersz['Filename'];
      break;
    }
  }

  if($id_wlasciciela == $id_uzytkownika || $uprawnienia == "Administrator") {

    if($tryb == "film") {
      $zapytanie_SQL = "DELETE movies, comments, likes, tags, categories_videos FROM movies LEFT JOIN comments ON comments.Movie_ID = movies.ID LEFT JOIN likes ON likes.Movie_ID = movies.ID LEFT JOIN tags ON tags.Movie_ID = movies.ID LEFT JOIN categories_videos ON categories_videos.Movie_ID = movies.ID WHERE movies.ID = '{$id_filmu}';";
    } else {
      $zapytanie_SQL = "DELETE streams, comments_streams, likes_streams, categories_videos FROM streams LEFT JOIN comments_streams ON comments_streams.Stream_ID = streams.ID LEFT JOIN likes_streams ON likes_streams.Stream_ID = streams.ID LEFT JOIN categories_videos ON categories_videos.Stream_ID = streams.ID WHERE streams.ID = '{$id_filmu}';";
    }

    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if($polaczenie_BD->affected_rows > 0) {

      if($tryb == "film") {

        unlink("miniatures/".$nazwa_pliku.".jpeg");
        unlink("videos/".$nazwa_pliku.".mp4");
        pokazKomunikat("Film został usunięty");
        przekierowanie("./account.php");

      } else {

        unlink("miniatures_streams/".$nazwa_pliku.".jpeg");
        pokazKomunikat("Transmisja została usunięta");
        przekierowanie("./index.php");
      }
    } else {

      if($tryb == "film") {
        pokazKomunikat("Nie udało się usunąć filmu");
        przekierowanie("./account.php");
      } else {
        pokazKomunikat("Nie udało się usunąć transmisji");
        przekierowanie("./stream.php?id={$id_filmu}");
      }
    }
  } else {
    pokazKomunikat("Nie jesteś właścicielem tego materiału");
    przekierowanie("./index.php");
  }
}
?>

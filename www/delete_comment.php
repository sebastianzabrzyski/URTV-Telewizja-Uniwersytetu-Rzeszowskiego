<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $id_komentarza = $_GET['id'];
  $tryb = $_GET['tryb'];

  sprawdzZalogowanie("","./login.php?return=delete_comment.php?tryb={$tryb}&id={$id_komentarza}");

  $conn = polaczDB();
  $id_komentarza = $conn->real_escape_string($id_komentarza);
  if($tryb == "film") {
    $query = "SELECT User_ID, Movie_ID FROM comments WHERE ID = {$id_komentarza};";
  } else {
    $query = "SELECT User_ID, Stream_ID FROM comments_streams WHERE ID = {$id_komentarza};";
  }

  $result = queryDB($conn,$query);
  $row = $result->fetch_assoc();
  $id_wlasciciela = $row["User_ID"];

  if($tryb == "film") {
    $id_filmu = $row["Movie_ID"];
  } else {
    $id_filmu = $row["Stream_ID"];
  }

  if($id_wlasciciela == $user_id || $privileges == "Administrator") {
    if($tryb == "film") {
      $query = "DELETE FROM comments WHERE ID = {$id_komentarza};";
    } else {
      $query = "DELETE FROM comments_streams WHERE ID = {$id_komentarza};";
    }

    $result = queryDB($conn,$query);
    if($conn->affected_rows > 0) {

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

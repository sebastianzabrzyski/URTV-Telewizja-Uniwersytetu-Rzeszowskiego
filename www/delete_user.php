<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $id_uzytkownika = $_GET['id'];
  sprawdzZalogowanie("","./login.php?return=delete_user.php?id={$id_uzytkownika}");
  if($id_uzytkownika == $user_id || $privileges == "Administrator") {

    if($id_uzytkownika == $user_id ) {
      $link_powrot = "./index.php";
    } else {
      $link_powrot = "./admin.php";
    }

    $conn = polaczDB();
    $id_uzytkownika = $conn->real_escape_string($id_uzytkownika);
    $query = "DELETE users, comments, likes, subscription, comments_streams, likes_streams FROM users LEFT JOIN comments ON comments.User_ID = users.ID LEFT JOIN comments_streams ON comments_streams.User_ID = users.ID LEFT JOIN likes ON likes.User_ID = users.ID LEFT JOIN likes_streams ON likes_streams.User_ID = users.ID LEFT JOIN subscription ON subscription.User_ID = users.ID WHERE users.ID = '{$id_uzytkownika}';";
    $result = queryDB($conn,$query);

    if($conn->affected_rows > 0) {

      //usuwanie filmow i plikow

      $query = "SELECT Filename FROM movies WHERE User_ID = {$id_uzytkownika};";
      $result = queryDB($conn,$query);

      if ($result->num_rows > 0) {
        $nazwy_plikow_tablica = array();
        while($row = $result->fetch_assoc()) {
          $nazwa_pliku = $row["Filename"];
          array_push($nazwy_plikow_tablica, $nazwa_pliku);
        }

        foreach ($nazwy_plikow_tablica as $nazwa_pliku_z_tablicy) {
          unlink("miniatures/".$nazwa_pliku_z_tablicy.".jpeg");
          unlink("videos/".$nazwa_pliku_z_tablicy.".mp4");
        }
      }

      $query = "DELETE movies, tags, categories_videos FROM movies LEFT JOIN tags ON tags.Movie_ID = movies.ID LEFT JOIN categories_videos ON categories_videos.Movie_ID = movies.ID WHERE movies.User_ID = '{$id_uzytkownika}';";
      $result = queryDB($conn,$query);


      $query = "SELECT Filename FROM streams WHERE User_ID = {$id_uzytkownika};";
      $result = queryDB($conn,$query);

      if ($result->num_rows > 0) {
        $nazwy_plikow_streamy_tablica = array();
        while($row = $result->fetch_assoc()) {
          $nazwa_pliku_stream = $row["Filename"];
          array_push($nazwy_plikow_streamy_tablica, $nazwa_pliku_stream);
        }

        foreach ($nazwy_plikow_streamy_tablica as $nazwa_pliku_stream_z_tablicy) {
          unlink("miniatures_streams/".$nazwa_pliku_stream_z_tablicy.".jpeg");
        }
      }

      $query = "DELETE streams, categories_videos FROM streams LEFT JOIN categories_videos ON categories_videos.Stream_ID = streams.ID WHERE streams.User_ID = '{$id_uzytkownika}';";
      $result = queryDB($conn,$query);

      if($id_uzytkownika == $user_id ) {
        if (session_id() == "")
        {
          ini_set("session.cookie_httponly", True);
          session_start();
        }
        setcookie("PHPSESSID", "", time() - 6400);
        unset($_SESSION['username']);
        unset($_SESSION['user_id']);
        unset($_SESSION['privileges']);
        unset($_SESSION['access']);
        session_destroy();
      }
      //usuwanie filmow i plikow
      pokazKomunikat("Użytkownik został usunięty");
      przekierowanie($link_powrot);
    } else {
      pokazKomunikat("Nie udało się usunąć użytkownika");
      przekierowanie($link_powrot);
    }
  } else {
    pokazKomunikat("Nie posiadasz odpowiednich uprawnień");
    przekierowanie($link_powrot);
  }
}
?>

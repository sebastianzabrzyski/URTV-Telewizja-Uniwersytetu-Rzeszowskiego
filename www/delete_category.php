<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $id_kategorii = $_GET['id'];
  $tryb = $_GET['tryb'];
  sprawdzZalogowanie("","./login.php?return=delete_category.php?tryb={$tryb}&id={$id_kategorii}");

  $conn = polaczDB();
  $id_kategorii = $conn->real_escape_string($id_kategorii);
  if($tryb == "kategoria") {
    $query_count = "SELECT count(*) FROM categories_videos WHERE Category_ID = '{$id_kategorii}';";
    $result = queryDB($conn,$query_count);
    $row = $result->fetch_row();
    $liczba_powiazanych= $row[0];
  } else {
    $query_count = "SELECT count(*) FROM categories WHERE Group_ID = '{$id_kategorii}';";
    $result = queryDB($conn,$query_count);
    $row = $result->fetch_row();
    $liczba_powiazanych = $row[0];
  }

  if($liczba_powiazanych > 0) {

    if($tryb == "kategoria") {
      $wiadomosc = "Nie mozna usunąć kategorii, do której przypisane są materiały";
    } else {
      $wiadomosc = "Nie mozna usunąć grupy, do której przypisane są kategorie";
    }

    pokazKomunikat($wiadomosc);
    przekierowanie("./admin.php");

  } else {

    if($privileges == "Administrator") {

    if($tryb == "kategoria") {
      $query = "DELETE FROM categories WHERE ID = '{$id_kategorii}';";
    } else {
      $query = "DELETE FROM categories_groups WHERE ID = '{$id_kategorii}';";
    }
      $result = queryDB($conn,$query);
      if($conn->affected_rows > 0) {

        if($tryb == "kategoria") {
          $wiadomosc = "Kategoria została usunięta";
        } else {
          $wiadomosc = "Grupa została usunięta";
        }
        pokazKomunikat($wiadomosc);
        przekierowanie("./admin.php");
      } else {

        if($tryb == "kategoria") {
          $wiadomosc = "Nie udało się usunąć kategorii";
        } else {
          $wiadomosc = "Nie udało się usunąć grupy";
        }

        pokazKomunikat($wiadomosc);
        przekierowanie("./admin.php");

      }
    } else {
      pokazKomunikat("Nie posiadasz uprawnień administratora");
      przekierowanie("./index.php");
    }
  }

}
?>

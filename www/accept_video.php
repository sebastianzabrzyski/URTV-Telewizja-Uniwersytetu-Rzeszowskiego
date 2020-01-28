<?php
require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $id_filmu = $_GET['id'];
  sprawdzZalogowanie("","./login.php?return=accept_video.php?id={$id_filmu}");

  if($privileges == "Administrator") {
    $conn = polaczDB();
    $id_filmu = $conn->real_escape_string($id_filmu);
    $query = "UPDATE movies SET Verified = 'Tak' WHERE ID = '{$id_filmu}';";
    $result = queryDB($conn,$query);
    if($conn->affected_rows > 0) {
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

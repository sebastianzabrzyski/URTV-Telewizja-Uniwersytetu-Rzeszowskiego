<?php

require_once("functions.php");
ustawSesje();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $kod = $_GET['mail'];
  $conn = polaczDB();
  $kod = $conn->real_escape_string($kod);
  $query = "DELETE FROM newsletter WHERE md5(Email) = '{$kod}';";
  $result = queryDB($conn,$query);

  if($conn->affected_rows > 0) {

    pokazKomunikat("Zostałeś wypisany z newslettera");
    przekierowanie("./index.php");

  } else {

    pokazKomunikat("Nie odnaleziono takiego adresu e-mail w naszej bazie");
    przekierowanie("./index.php");
  }
}
?>

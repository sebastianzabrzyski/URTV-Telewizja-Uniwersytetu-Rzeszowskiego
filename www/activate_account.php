<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $kod = $_GET['id'];
  $conn = polaczDB();
  $kod = $conn->real_escape_string($kod);
  $query = "SELECT *
  FROM unverified_users
  WHERE (Code = '{$kod}');";
  $result = queryDB($conn,$query);

  if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
      $login = $row["Login"];
      $email = $row["Email"];
      $query = "SELECT *
      FROM users
      WHERE (Login = '{$login}')
      OR (Email = '{$email}');";
      $result = queryDB($conn,$query);

      if ($result->num_rows == 0) {

        $imie = $row["Name"];
        $nazwisko = $row["Surname"];
        $password = $row["Password"];
        $data = date("Y-m-d G:i:s");
        $miejsce_uzytkownika = 2048;
        $query = "INSERT INTO users (Login, Password, Name, Surname, Email, Privileges, Space, Date)
        VALUES ('{$login}', '{$password}', '{$imie}','{$nazwisko}', '{$email}', 'Użytkownik', {$miejsce_uzytkownika}, '{$data}')";
        $result = queryDB($conn,$query);
        if($result === TRUE) {
          $query = "DELETE FROM unverified_users WHERE (Code = '{$kod}');";
          $result = queryDB($conn,$query);


          $query = "SELECT * FROM newsletter WHERE Email = '{$email}';";
          $result = queryDB($conn,$query);
          if ($result->num_rows > 0) {
            $id_nowego_uzykownika = $conn->insert_id;
            $query2 = "DELETE FROM newsletter WHERE Email = '{$email}';";
            $result2 = queryDB($conn,$query2);
            $query2 = "INSERT INTO newsletter (User_ID, Author_ID) VALUES ('{$id_nowego_uzykownika}', 0);";
            $result2 = queryDB($conn,$query2);
          }
          pokazKomunikat("Twoje konto zostało aktywowane, możesz się zalogować");
        } else {
          $link = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])."/login.php";
          pokazKomunikat("Aktywacja konta nie powiodła się, spróbuj ponownie później");
        }

      } else {
        $link = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])."/login.php";
        pokazKomunikat("Podany login lub adres e-mail jest już zajęty");

      }

    }
  } else {
    $link = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])."/login.php";
    pokazKomunikat("Kod weryfikacyjny jest nieprawidłowy");
  }
    przekierowanie("./login.php");
}


?>

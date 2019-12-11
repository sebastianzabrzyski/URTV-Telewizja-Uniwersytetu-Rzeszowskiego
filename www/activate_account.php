<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $kod = $_GET['id'];
    $polaczenie_BD = polaczDB();
    $kod = $polaczenie_BD->real_escape_string($kod);
    $zapytanie_SQL = "SELECT *
  FROM unverified_users
  WHERE (Code = '{$kod}');";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if ($wynik->num_rows > 0) {

        while($wiersz = $wynik->fetch_assoc()) {
            $login = $wiersz["Login"];
            $email = $wiersz["Email"];
            $zapytanie_SQL = "SELECT *
      FROM users
      WHERE (Login = '{$login}')
      OR (Email = '{$email}');";
            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

            if ($wynik->num_rows == 0) {

                $imie = $wiersz["Name"];
                $nazwisko = $wiersz["Surname"];
                $haslo = $wiersz["Password"];
                $data = date("Y-m-d G:i:s");
                $miejsce_uzytkownika = 2048;
                $zapytanie_SQL = "INSERT INTO users (Login, Password, Name, Surname, Email, Privileges, Space, Date)
        VALUES ('{$login}', '{$haslo}', '{$imie}','{$nazwisko}', '{$email}', 'Użytkownik', {$miejsce_uzytkownika}, '{$data}')";
                $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                if($wynik === TRUE) {
                    $zapytanie_SQL = "DELETE FROM unverified_users WHERE (Code = '{$kod}');";
                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);


                    $zapytanie_SQL = "SELECT * FROM newsletter WHERE Email = '{$email}';";
                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                    if ($wynik->num_rows > 0) {
                        $id_nowego_uzykownika = $polaczenie_BD->insert_id;
                        $zapytanie_SQL2 = "DELETE FROM newsletter WHERE Email = '{$email}';";
                        $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL2);
                        $zapytanie_SQL2 = "INSERT INTO newsletter (User_ID, Author_ID) VALUES ('{$id_nowego_uzykownika}', 0);";
                        $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL2);
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

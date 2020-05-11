<?php

require_once("functions.php");
sprawdzZalogowanie("","./login.php?return=account.php");

$menu_1 = "Moje konto";
$menu_2 = "Wyloguj się";
$link_1 = "./account.php";
$link_2 = "./logout.php";

$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$conn = polaczDB();
$query = "SELECT Name, Surname, Email FROM users WHERE ID = {$user_id};";
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {

  while($row = $result->fetch_assoc()) {
    $imie = $row["Name"];
    $nazwisko = $row["Surname"];
    $email = $row["Email"];
    break;
  }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'newsletter_form')
{

  $wybor = $_POST['newsletter'];

  if($wybor == "no_any_ns") {
    $user_id = $conn->real_escape_string($user_id);
    $query = "DELETE FROM subscription WHERE User_ID = {$user_id} AND Author_ID = 0;";
    $result = queryDB($conn,$query);
    $query = "INSERT INTO subscription (User_ID, Author_ID) VALUES ({$user_id}, -1)";
    $result = queryDB($conn,$query);
    pokazKomunikat("Ustawienia subskrybcji zostały zapisane");

  } elseif ($wybor == "all_ns") {

    //	$query = "DELETE FROM newsletter WHERE User_ID = {$user_id};";
    //  $result = queryDB($conn,$query);
    $user_id = $conn->real_escape_string($user_id);
    $query = "DELETE FROM subscription WHERE User_ID = {$user_id} AND Author_ID = -1;";
    $result = queryDB($conn,$query);
    $query = "INSERT INTO subscription (User_ID, Author_ID) VALUES ({$user_id}, 0)";
    $result = queryDB($conn,$query);
    pokazKomunikat("Ustawienia subskrybcji zostały zapisane");

  } else {
    $user_id = $conn->real_escape_string($user_id);
    $query = "DELETE FROM subscription WHERE User_ID = {$user_id} AND (Author_ID = 0 OR Author_ID =-1);";
    $result = queryDB($conn,$query);
    //$query = "SELECT ID FROM subscription WHERE User_ID = {$user_id};";
  //  $result = queryDB($conn,$query);

    //if ($result->num_rows == 0) {
  //    $user_id = $conn->real_escape_string($user_id);
  //    $query = "INSERT INTO subscription (User_ID) VALUES ({$user_id})";
  //    $result = queryDB($conn,$query);
  //  }

    pokazKomunikat("Ustawienia subskrybcji zostały zapisane");

  }

  przekierowanie($adres_obecny);

}

$user_id = $conn->real_escape_string($user_id);
$query = "SELECT Author_ID FROM subscription WHERE User_ID = {$user_id};";
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
  $ustawienia_newslettera = "selected_ns";

  while($row = $result->fetch_assoc()) {

    $autor = $row["Author_ID"];
    if($autor == 0) {
      $ustawienia_newslettera = "all_ns";
      break;
    }
    if($autor == -1) {
      $ustawienia_newslettera = "no_any_ns";
      break;
    }
  }
} else {
  $ustawienia_newslettera = "no_any_ns";

}

$id_strony = null;

if(isset($_GET['page'])) {
  $id_strony = $_GET['page'];
}

if(!is_numeric($id_strony) || $id_strony < 1) {
  $id_strony = 1;
}

$wszystkie_wyniki = 0;
$pokazywane_wyniki = 5;
$pominiete_wyniki = ($pokazywane_wyniki * $id_strony) - $pokazywane_wyniki;

$conn = polaczDB();
$user_id = $conn->real_escape_string($user_id);
$query_count = "SELECT count(ID) FROM movies WHERE User_ID = {$user_id};";

$result = queryDB($conn,$query_count);
$row = $result->fetch_row();
$wszystkie_wyniki = $row[0];

$query = "SELECT movies.ID, User_ID, Title, Filename, Views, movies.Date, Describtion FROM movies INNER JOIN users ON movies.User_ID = users.ID WHERE User_ID = {$user_id} ORDER BY movies.Date DESC LIMIT {$pokazywane_wyniki} OFFSET {$pominiete_wyniki};";
$result = queryDB($conn,$query);

$id_filmu = null;

// wyswietlanie listy filmów

if ($result->num_rows > 0) {

  $przeslane_filmy_tablica = array();

  while($row = $result->fetch_assoc()) {

    $id_filmu = $row["ID"];
    $nazwa_pliku = $row["Filename"];
    $adres_db = "./video.php?id=".$id_filmu;
    $tytul_db = $row["Title"];
    $opis_db = $row["Describtion"];
    $wyswietlenia_db = $row["Views"];
    $wyswietlenia_db = $row["Views"];
    $wyswietlenia_db = number_format($wyswietlenia_db, 0, ',', ' ');
    $miniaturka_db = "miniatures/".$nazwa_pliku.".jpeg";
    $data_publikacji_db = $row["Date"];
    $data_publikacji_db = new DateTime($data_publikacji_db);
    $data_publikacji_db = $data_publikacji_db->format('d.m.Y');
    $edytuj = "./edit_video.php?id=".$id_filmu;
    $id_filmu = $conn->real_escape_string($id_filmu);
    $query_komentarze = "SELECT COUNT(ID) FROM comments WHERE Movie_ID = {$id_filmu} ;";
    $result_kom = queryDB($conn,$query_komentarze);
    $row_kom = $result_kom->fetch_row();
    $komentarze = $row_kom[0];

    array_push($przeslane_filmy_tablica,array('tytul' => $tytul_db,'adres' => $adres_db,'opis' => $opis_db,'wyswietlenia' => $wyswietlenia_db,'miniaturka' => $miniaturka_db,'data_publikacji' => $data_publikacji_db,'id' => $id_filmu,'link_edytuj' => $edytuj,'komentarze' => $komentarze));

  }


}

if($privileges != "Administrator" && $privileges != "Uploader") {
  $transmisje_widocznosc = "display: none;";
}

if($privileges != "Administrator") {
  $link_panel_admina = "display: none;";
}

$wykorzystane = 0;
$do_wykorzystania = 0;
$user_id = $conn->real_escape_string($user_id);
$query = "SELECT SUM(Size) AS Wykorzystane FROM movies WHERE User_ID = {$user_id};";
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {


    $wykorzystane = $row["Wykorzystane"];


    break;
  }
}

if($wykorzystane == NULL) $wykorzystane = 0;

$query = "SELECT Space FROM users WHERE ID = {$user_id};";
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

    $do_wykorzystania = $row["Space"];

    break;
  }
}


//paginacja

$ilosc_stron = $wszystkie_wyniki / $pokazywane_wyniki;
$ilosc_stron = ceil($ilosc_stron);

if($id_strony >4) {
  $page_5 = $id_strony;
} else {
  $page_5 = 5;
}

$page_previous = $id_strony - 1;
$page_1 = $page_5 - 4;
$page_2 = $page_5 - 3;
$page_3 = $page_5 - 2;
$page_4 = $page_5 - 1;
$page_5 = $page_5;
$page_6 = $page_5 + 1;
$page_7 = $page_5 + 2;
$page_next = $id_strony + 1;

$styl_aktywny = ' class="aktywne"';
$styl_page_1 = $styl_page_2 = $styl_page_3 = $styl_page_4 = $styl_page_5 = $styl_page_6 = $styl_page_7 = "";

switch ($id_strony) {
  case $page_1:
  $styl_page_1 = $styl_aktywny;
  break;
  case $page_2:
  $styl_page_2 = $styl_aktywny;
  break;
  case $page_3:
  $styl_page_3 = $styl_aktywny;
  break;

  case $page_4:
  $styl_page_4 = $styl_aktywny;
  break;
  case $page_5:
  $styl_page_5 = $styl_aktywny;
  break;
  case $page_6:
  $styl_page_6 = $styl_aktywny;
  break;

  case $page_7:
  $styl_page_7 = $styl_aktywny;
  break;
  default:
  break;
}


?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <meta name="description" content="Telewizja internetowa Uniwersytetu Rzeszowskiego">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="msapplication-config" content="/favicons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">
  <link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
  <link rel="manifest" href="/favicons/site.webmanifest">
  <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="shortcut icon" href="/favicons/favicon.ico">
  <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Moje konto</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-3.3.1.min.js"></script>
  <script>var ustawienia_newslettera = "<?php echo $ustawienia_newslettera;?>";</script>
</head>
<body>
  <header>
    <div class="kontener">
      <div id="naglowek_tytul">
        <div id="logo_strony">
          <a href="./index.php">
            <img src="./images/logo_UR.png">
          </a>
        </div>
        <h1>Telewizja internetowa<br>Uniwersytetu Rzeszowskiego</h1>
      </div>
      <div id="naglowek_nawigacja">
        <nav>
          <ul>
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="about.php">O serwisie</a></li>
            <li><a href="contact.php">Kontakt</a></li>
            <li><a href="<?php echo $link_1; ?>"><?php echo $menu_1; ?></a></li>
            <li><a href="<?php echo $link_2; ?>"><?php echo $menu_2; ?></a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div id="blok_podnaglowka" class="blok_odstep_dolny blok_odstep_gorny">
    <div class="kontener">
      <ul id="sciezka" class="podnaglowek">
        <li><a href="./index.php">Strona główna</a></li>
        <li>Moje konto</li>
      </ul>

      <form id="wyszukiwarka" method="get" action="./search.php">
        <input type="text" name="phrase" class="podnaglowek" placeholder="Wpisz szukany tekst...">
        <input type="hidden" name="page" value="1">
        <select class="podnaglowek" name="mode">
          <option selected value="Tytul">Tytuł</option>
          <option value="Tagi">Tag</option>
          <option value="Autor">Autor</option>
        </select>
        <button type="submit" class="button_1">Szukaj</button>
      </form>
    </div>
  </div>

  <div id="blok_glowny">
    <div class="kontener">

      <div class="naglowek_formularza"><h1>Informacje o koncie <span onclick="potwierdzUsuniecieUzytkownika(<?php echo $user_id;?>);return false;"><a href="#">&nbsp;[Usuń konto]</a></span><span style="<?php echo $link_panel_admina; ?>"><a href="./admin.php">[Panel administratora]</a></span></h1></div>
      <div class="tlo blok_odstep_dolny">
        <div class="blok_formularza formularz_moje_dane blok_padding_gorny blok_padding_dolny">
          <form>
            <div class="pole">
              <input type="text" class="readonly centrowanie" name="login" id="login" readonly value="<?php echo $username; ?>">
              <p>Login:</p>
            </div>
            <div class="pole">
              <input type="text" class="readonly centrowanie" name="name" id="name" readonly value="<?php echo $imie." ".$nazwisko; ?>">
              <p class="desktop">Imię i nazwisko:</p>
              <p class="mobile">Imię i nazw.:</p>
            </div>
            <div class="pole">
              <input type="email" class="readonly centrowanie" name="email" id="email" readonly value="<?php echo $email; ?>">
              <p>Adres e-mail:</p>
            </div>
            <div class="pole ostatnie_pole dwa_przyciski">
              <a href="./change_email.php"><button type="button" class="przycisk_1">Zmień e-mail</button></a>
              <a href="./change_password.php"><button type="button" class="przycisk_2">Zmień hasło</button></a>
            </div>
          </form>
        </div>
      </div>

      <div class="naglowek_formularza"><h1>Ustawienia subskrybcji</h1></div>
      <div class="tlo blok_odstep_dolny">
        <div class="blok_formularza blok_padding_gorny blok_padding_dolny">
          <form id="newsletter_form" name="newsletter_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="form_name" value="newsletter_form">
            <div class="pole">
              <input type="radio" id="no_any_ns" name="newsletter" value="no_any_ns">
              <p class="newsletter desktop">Nie powiadamiaj mnie o nowych materiałach</p>
              <p class="newsletter mobile">Wyłącz powiadomienia</p>
            </div>
            <div class="pole">
              <input type="radio" id="all_ns" name="newsletter" value="all_ns">
              <p class="newsletter desktop">Powiadamiaj mnie o wszystkich materiałach</p>
              <p class="newsletter mobile">Wszystkie materiały</p>
            </div>
            <div class="pole">
              <input type="radio" id="selected_ns" name="newsletter" value="selected_ns">
              <p class="newsletter desktop">Powiadamiaj mnie tylko o materiałach, które subskrybuję</p>
              <p class="newsletter mobile">Tylko subskrybowane materiały</p>
            </div>
            <div class="pole ostatnie_pole">
              <button type="submit" id="przycisk_newsletter">Zapisz ustawienia</button>
            </div>
          </form>
        </div>
      </div>

      <div class="naglowek_formularza"><h1>Przesłane materiały <span id="wykorzystane_miejsce">Zajęte miejsce: <?php echo $wykorzystane; ?>MB z <?php echo $do_wykorzystania; ?>MB</span></h1></div>
      <div id="blok_przeslane_filmy">
        <div class="tlo">
          <div class="blok_panelu blok_padding_gorny">
            <div class="wiersz_przeslane_naglowek clearfix">
              <div class="kolumna_oczekujace kolumna_miniaturka">
                <h1>Miniaturka</h1>
              </div>
              <div class="kolumna_oczekujace">
                <h1>Tytuł i opis</h1>
              </div>
              <div class="kolumna_oczekujace">
                <h1>Wyświetlenia</h1>
              </div>
              <div class="kolumna_oczekujace kolumna_komentarze">
                <h1>Komentarze</h1>
              </div>
              <div class="kolumna_oczekujace">
                <h1>Data przesłania</h1>
              </div>
              <div class="kolumna_oczekujace">
              </div>
            </div>
            <?php
            foreach ($przeslane_filmy_tablica as $wynik) {
              $id = $wynik['id'];
              $adres = $wynik['adres'];
              $tytul = $wynik['tytul'];
              $opis = $wynik['opis'];
              $miniaturka = $wynik['miniaturka'];
              $data_publikacji = $wynik['data_publikacji'];
              $komentarze= $wynik['komentarze'];
              $wyswietlenia = $wynik['wyswietlenia'];
              $link_edytuj = $wynik['link_edytuj'];
              echo '<div class="wiersz_oczekujace clearfix">
              <div class="kolumna_oczekujace">
              <a href="'.$adres.'"><img class="miniaturka_oczekujacego_filmu" src="'.$miniaturka.'"></a>
              </div>
              <div class="kolumna_oczekujace tytul_i_opis">
              <a href="'.$adres.'"><h2>'.$tytul.'</h2></a>
              <p>'.$opis.'</p>
              </div>
              <div class="kolumna_oczekujace">
              <p>'.$wyswietlenia.'</p>
              </div>
              <div class="kolumna_oczekujace kolumna_komentarze">
              <p>'.$komentarze.'</p>
              </div>
              <div class="kolumna_oczekujace">
              <p>'.$data_publikacji.'</p>
              </div>
              <div class="kolumna_oczekujace">
              <img class="wskaznik" src="./images/delete.png" onclick="potwierdzUsuniecieFilmu('.$id.');return false;">
              <a href="'.$link_edytuj.'"><img src="./images/settings2.png"></a>
              </div>
              </div>';
            }
            ?>
            <div class="paginacja paginacja-2">
              <?php
              if($page_previous > 0 && $id_strony <= $ilosc_stron) echo "<a href=\"./account.php?page={$page_previous}\">Poprzednia</a>";
              if($page_1 <= $ilosc_stron) echo "<a {$styl_page_1} href=\"./account.php?page={$page_1}\">{$page_1}</a>";
              if($page_2 <= $ilosc_stron) echo "<a {$styl_page_2} href=\"./account.php?page={$page_2}\">{$page_2}</a>";
              if($page_3 <= $ilosc_stron) echo "<a {$styl_page_3} href=\"./account.php?page={$page_3}\">{$page_3}</a>";
              if($page_4 <= $ilosc_stron) echo "<a {$styl_page_4} href=\"./account.php?page={$page_4}\">{$page_4}</a>";
              if($page_5 <= $ilosc_stron) echo "<a {$styl_page_5} href=\"./account.php?page={$page_5}\">{$page_5}</a>";
              if($page_6 <= $ilosc_stron) echo "<a {$styl_page_6} href=\"./account.php?page={$page_6}\">{$page_6}</a>";
              if($page_7 <= $ilosc_stron) echo "<a {$styl_page_7} href=\"./account.php?page={$page_7}\">{$page_7}</a>";
              if($id_strony < $ilosc_stron) echo "<a href=\"./account.php?page={$page_next}\">Następna</a>";
              ?>
            </div>
            <div class="przeslane_przyciski blok_odstep_dolny clearfix">
              <a href="./add_video.php"><button type="button" class="przycisk_1">Dodaj nowy film</button></a>
              <a href="./add_stream.php"><button style="<?php echo $transmisje_widocznosc; ?>" type="button" class="przycisk_2">Transmisja na żywo</button></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="blok_odstep_gorny">
    <div class="kontener">
      <ul>
        <li>© Uniwersytet Rzeszowski 2018</li>
        <li class="desktop"><a href="http://www.ur.edu.pl/">Strona Uniwersytetu</a></li>
        <li><a href="terms.php">Regulamin serwisu</a></li>
        <li><a href="privacy.php">Polityka prywatności</a></li>
      </ul>
    </div>
  </footer>

<?php
require_once("functions_end.php");
?>
</body>
<script src="./js/account.js"></script>
</html>

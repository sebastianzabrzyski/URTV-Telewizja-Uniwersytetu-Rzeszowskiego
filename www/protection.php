<?php

require_once("functions.php");
sprawdzZalogowanie("","");

if($username == null) {

  $menu_1 = "Rejestracja";
  $menu_2 = "Zaloguj się";
  $link_1 = "./register.php";
  $link_2 = "./login.php";

} else {

  $menu_1 = "Moje konto";
  $menu_2 = "Wyloguj się";
  $link_1 = "./account.php";
  $link_2 = "./logout.php";

}

if(isset($_GET['id']) && isset($_GET['tryb'])) {

  $id_filmu = $_GET['id'];
  $tryb = $_GET['tryb'];

  if($tryb == "film") {
    $tryb_2 = "video";
  } else {
    $tryb_2 = "stream";
  }


}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'password_check')
{

  $haslo = $_POST['password'];
  $id_filmu = $_POST['movie_id'];
  $conn = polaczDB();
  $id_filmu = $conn->real_escape_string($id_filmu);
  if($tryb == "film") {

    $query = "SELECT Password FROM movies WHERE ID = '{$id_filmu}';";
  } else {
    $query = "SELECT Password FROM streams WHERE ID = '{$id_filmu}';";
  }

  $result = queryDB($conn,$query);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      if ( password_verify ($haslo, $row["Password"]) == true) {

        if(isset($_SESSION['access'])) {

          $dostep = $_SESSION['access'];
          array_push($dostep, $id_filmu);
          $_SESSION['access'] = $dostep;
          przekierowanie("./{$tryb_2}.php?id={$id_filmu}");
        } else {
          $_SESSION['access'] = array($id_filmu);
          przekierowanie("./{$tryb_2}.php?id={$id_filmu}");
        }
      } else {
        pokazKomunikat("Wprowadzono nieprawidłowe hasło");
        przekierowanie($adres_obecny);
      }
      break;
    }
  } else {
    pokazKomunikat("Materiał jest niedostępny");
    przekierowanie($adres_obecny);
  }
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
  <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Ochrona materiału</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-3.3.1.min.js"></script>
  <script src="./js/walidacja_ochrona_haslem.js"></script>
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
        <li>Zabezpieczenie</li>
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
      <div class="naglowek_formularza"><h1>Materiał jest chroniony hasłem</h1></div>
      <div class="tlo">
        <div class="blok_formularza formularz_ochrona flex blok_padding_gorny blok_padding_dolny">
          <form id="password_check" name="password_check" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?tryb=<?php echo $tryb; ?>&id=<?php echo $id_filmu; ?>" onsubmit="return Validatepassword_check()">
            <img id="obrazek_ochrona" src="./images/page_lock.png" alt="Ochrona materiału">
            <input type="hidden" name="form_name" value="password_check">
            <input type="hidden" name="movie_id" value="<?php if (isset($_POST['movie_id'])) echo $_POST['movie_id']; elseif (isset($_GET['id'])) echo $_GET['id']; ?>">
            <div class="pole_ochrona">
              <input class="centrowanie" type="password" name="password" id="password" placeholder="Wprowadź hasło">
            </div>
            <div class="pole_ochrona ostatnie_pole">
              <button type="submit" class="button_1">Wejdź</button>
            </div>
          </form>
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
</html>

<?php

require_once("functions.php");
sprawdzZalogowanie("./index.php","");

$menu_1 = "Rejestracja";
$menu_2 = "Zaloguj się";
$link_1 = "./register.php";
$link_2 = "./login.php";
$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if(isset($_GET["return"])) {
  $wroc = $_GET["return"];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'login_form')
{

  $login = strtolower($_POST['login']);
  $password = $_POST['password'];
  $conn = polaczDB();
  $login = $conn->real_escape_string($login);
  $query = "SELECT ID, Privileges, Password
  FROM users
  WHERE (Login = '{$login}');";

  $result = queryDB($conn,$query);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      if ( password_verify ($password, $row["Password"]) == true) {

        session_regenerate_id();
        $_SESSION['username'] = $login;
        $_SESSION['user_id'] = $row["ID"];
        $_SESSION['privileges'] = $row["Privileges"];
        $_SESSION['access'] = array();

        if(isset($_POST["return"])) {
          $wroc = $_POST["return"];
          przekierowanie("./{$wroc}");
        } else {
          przekierowanie("./index.php");
        }
      } else {
        pokazKomunikat("Wprowadzono zły login lub hasło");
      }
      break;
    }
  } else {
    pokazKomunikat("Wprowadzono zły login lub hasło");
  }
  przekierowanie($adres_obecny);

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'reset_password') {

  $login = strtolower($_POST['login_2']);
  $conn = polaczDB();
  $login = $conn->real_escape_string($login);
  $query = "SELECT Email, ID, Login
  FROM users
  WHERE Login = '{$login}';";
  $result = queryDB($conn,$query);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $login_db = $row['Login'];
      $user_id = $row['ID'];
      $email = $row['Email'];
      if($login_db == $login) {

        $kod_weryfikacyjny = md5(uniqid(rand(), true));
        $aktualny_czas = time();

        $query = "INSERT INTO reset_password (User_ID, Code, Time)
        VALUES ({$user_id}, '{$kod_weryfikacyjny}', '{$aktualny_czas}')";

        $result = queryDB($conn,$query);
        if($result === TRUE) {

          $link = "http://".$_SERVER['HTTP_HOST']."/reset_password.php?code=".$kod_weryfikacyjny;
          $tresc = "Witaj, ".$login."!\n\nKliknij poniższy odnośnik, aby zresetować hasło w serwisie tv.ur.edu.pl:\n\n".$link."\n\nLink będzie ważny tylko przez 24 godziny.\n\nPozdrawiamy!";
          $wyslano_email = wyslijEmail($email,"Resetowanie hasła w serwisie UR TV",$tresc);
          pokazKomunikat("Sprawdź skrzynkę pocztową, aby zresetować hasło");

        } else {
          pokazKomunikat("Wystąpił błąd podczas resetowania hasła");
        }
      }
      break;
    }
  } else {

    pokazKomunikat("Podany login nie istnieje");
  }

  przekierowanie($adres_obecny);
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
  <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Logowanie</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-3.3.1.min.js"></script>
  <script src="./js/walidacja_logowanie.js"></script>
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
        <li>Logowanie</li>
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
      <div class="naglowek_formularza"><h1>Logowanie do konta</h1></div>
      <div id="blok_logowanie" class="blok_odstep_dolny">
        <div class="tlo">
          <div class="blok_formularza formularz_logowanie blok_padding_gorny blok_padding_dolny">
            <form id="login_form" name="login_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return Validatelogin_form()">
              <input type="hidden" name="form_name" value="login_form">
              <input type="hidden" name="return" value="<?php echo $wroc; ?>">
              <div class="pole">
                <input type="text" class="centrowanie" name="login" id="login" placeholder="">
                <p>Login:</p>
              </div>
              <div class="pole">
                <input type="password" class="centrowanie" name="password" id="password" placeholder="">
                <p>Hasło:</p>
              </div>
              <div class="pole ostatnie_pole">
                <button type="submit" class="button_1">Zaloguj się</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="naglowek_formularza"><h1>Resetowanie hasła</h1></div>
      <div id="blok_resetowanie_hasla">
        <div class="tlo">
          <div class="blok_formularza formularz_logowanie blok_padding_gorny blok_padding_dolny">
            <form id="reset_password" name="reset_password" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return Validatereset_password()">
              <input type="hidden" name="form_name" value="reset_password">
              <div class="pole">
                <input type="text" class="centrowanie" name="login_2" id="login_2" placeholder="">
                <p>Login:</p>
              </div>
              <div class="pole ostatnie_pole">
                <button type="submit" class="button_1">Zresetuj hasło</button>
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

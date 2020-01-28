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

$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

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
  <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Polityka prywatności</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-3.3.1.min.js"></script>
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
        <li>Polityka prywatności</li>
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
      <div class="naglowek_formularza"><h1>Polityka prywatności</h1></div>
      <div class="tlo">
        <div class="blok_formularza formularz_kontakt blok_padding_gorny blok_padding_dolny">
          <div id="polityka_prywatnosci">
            <p>1. Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z korzystaniem przez nich usług oferowanych poprzez Serwis.</p>
            <p>2. Administratorem danych osobowych zawartych w serwisie jest
              Uniwersytet Rzeszowski z siedzibą przy al. Rejtana w Rzeszowie, NIP: 813-32-38-822, REGON: 691560040.</p>
              <p>3. W trosce o bezpieczeństwo powierzonych nam danych opracowaliśmy wewnętrzne procedury i zalecenia, które mają zapobiec udostępnieniu danych osobom nieupoważnionym. Kontrolujemy ich wykonywanie i stale sprawdzamy ich zgodność z odpowiednimi aktami prawnymi - ustawą o ochronie danych osobowych, ustawą o świadczeniu usług drogą elektroniczną, a także wszelkiego rodzaju aktach wykonawczych i aktach prawa wspólnotowego.</p>
              <p>4. Dane Osobowe przetwarzane są na podstawie zgody wyrażanej przez Użytkownika oraz w przypadkach, w których przepisy prawa upoważniają Administratora do przetwarzania danych osobowych na podstawie przepisów prawa lub w celu realizacji zawartej pomiędzy stronami umowy.</p>
              <p>5. Serwis realizuje funkcje pozyskiwania informacji o użytkownikach i ich zachowaniach w następujący sposób:</br>
                a)	poprzez dobrowolnie wprowadzone w formularzach informacje</br>
                b)	poprzez gromadzenie plików “cookies”</p>
                <p>6. Serwis zbiera informacje dobrowolnie podane przez użytkownika.</p>
                <p>7. Dane podane w formularzu są przetwarzane w celu wynikającym z funkcji konkretnego formularza np. w celu dokonania procesu obsługi kontaktu informacyjnego</p>
                <p>8. Dane osobowe pozostawione w serwisie nie zostaną sprzedane ani udostępnione osobom trzecim, zgodnie z przepisami Ustawy o ochronie danych osobowych.</p>
                <p>9. Do danych zawartych w formularzu przysługuje wgląd osobie fizycznej, która je tam umieściła. Osoba ta ma również praw do modyfikacji i zaprzestania przetwarzania swoich danych w dowolnym momencie.</p>
                <p>10. Zastrzegamy sobie prawo do zmiany w polityce ochrony prywatności serwisu, na które może wpłynąć rozwój technologii internetowej, ewentualne zmiany prawa w zakresie ochrony danych osobowych oraz rozwój naszego serwisu internetowego. O wszelkich zmianach będziemy informować w sposób widoczny i zrozumiały.</p>
                <p>11. W Serwisie mogą pojawiać się linki do innych stron internetowych. Takie strony internetowe działają niezależnie od Serwisu i nie są w żaden sposób nadzorowane przez serwis. Strony te mogą posiadać własne polityki dotyczące prywatności oraz regulaminy, z którymi zalecamy się zapoznać.</p>
                <p>12. W razie wątpliwości co któregokolwiek z zapisów niniejszej polityki prywatności jesteśmy do dyspozycji pod adresem e-mailowym kontakt@tv.ur.edu.pl.</p>

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

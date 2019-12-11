<?php

require_once("functions.php");
sprawdzZalogowanie("./index.php","");

if($nazwa_uzytkownika == null) {

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'reset_password')
{

    if(isset($_POST['code'])) {

        $kod = $_POST['code'];
        $polaczenie_BD = polaczDB();
        $kod = $polaczenie_BD->real_escape_string($kod);
        $zapytanie_SQL = "SELECT ID, User_ID, Code, Time
    FROM reset_password
    WHERE Code = '{$kod}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

        if ($wynik->num_rows > 0) {

            while($wiersz = $wynik->fetch_assoc()) {

                $czas_wyslania = $wiersz['Time'];
                $id_resetu = $wiersz['ID'];
                $id_uzytkownika = $wiersz['User_ID'];
                $kod_db = $wiersz['Code'];
                $aktualny_czas = time();
                $roznica = $aktualny_czas - $czas_wyslania;
                if($roznica > 86400) {
                    $zapytanie_SQL = "DELETE FROM reset_password WHERE ID = '{$id_resetu}';";
                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                    pokazKomunikat("Upłynął czas na zmianę hasła");
                    przekierowanie("./login.php");

                } else {
                    $nowe_haslo = $_POST['password_1'];
                    $nowe_haslo_bcrypt = password_hash($nowe_haslo, PASSWORD_BCRYPT);
                    $id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
                    $zapytanie_SQL = "UPDATE users SET Password = '{$nowe_haslo_bcrypt}' WHERE ID = {$id_uzytkownika};";
                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                    if(mysqli_affected_rows($polaczenie_BD) > 0) {

                        $zapytanie_SQL = "DELETE FROM reset_password WHERE ID = '{$id_resetu}';";
                        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                        pokazKomunikat("Hasło zostało zmienione");
                        przekierowanie("./login.php");
                    } else {
                        pokazKomunikat("Wystąpił błąd podczas zmiany hasła");
                        przekierowanie("./login.php");
                    }
                }
                break;
            }
        } else {
            pokazKomunikat("Wystąpił błąd podczas zmiany hasła");
            przekierowanie("./login.php");
        }
    } else {
        przekierowanie("./index.php");
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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Resetowanie hasła</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/walidacja_resetowanie_hasla.js"></script>
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
                    <li><a href="./login.php">Logowanie</a></li>
                    <li>Resetowanie hasła</li>
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
                <div class="naglowek_formularza"><h1>Resetowanie hasła</h1></div>
                <div class="tlo">
                    <div class="blok_formularza formularz_reset blok_padding_gorny blok_padding_dolny">
                        <form id="reset_password" name="reset_password" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return Validatereset_password()">
                            <input type="hidden" name="form_name" value="reset_password">
                            <input type="hidden" name="code" value="<?php if (isset($_POST['code'])) echo $_POST['code']; elseif (isset($_GET['code'])) echo $_GET['code']; ?>">
                            <div class="pole">
                                <input type="password" class="centrowanie" name="password_1" id="password_1" placeholder="">
                                <p>Nowe hasło:</p>
                            </div>
                            <div class="pole">
                                <input type="password" class="centrowanie" name="password_2" id="password_2" placeholder="">
                                <p>Powtórz hasło:</p>
                            </div>
                            <div class="pole ostatnie_pole">
                                <button type="submit" class="button_1">Zmień hasło</button>
                            </div>
                        </form>
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

<!-- Strona umożliwiająca rejestrację użytkownika w serwisie -->

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'register_form')
{

    $dane_poprawne = 0;
    $email = $_POST['email'];
    $login = $_POST['login'];
    $haslo_1 = $_POST['password_1'];
    $haslo_2 = $_POST['password_2'];
    $imie = $_POST['name'];
    $nazwisko = $_POST['surname'];

    if(sprawdzPoprawnosc($email,"a-zA-Z0-9_\W",5,100) == "Poprawne" &&
       sprawdzPoprawnosc($login,"A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9-_",1,64) == "Poprawne" &&
       sprawdzPoprawnosc($haslo_1,"a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ_ \W",8,0) == "Poprawne" &&
       sprawdzPoprawnosc($imie,"A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f",1,30) == "Poprawne" &&
       sprawdzPoprawnosc($nazwisko,"A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f-",1,100) == "Poprawne") {
        $dane_poprawne++;
    } else {
        pokazKomunikat("Wprowadzono nieprawidłowe dane");
    }

    $polaczenie_BD = polaczDB();
    $login = $polaczenie_BD->real_escape_string($login);
    $email = $polaczenie_BD->real_escape_string($email);
    $zapytanie_SQL = "SELECT *
  FROM users
  WHERE (Login = '{$login}')
  OR (Email = '{$email}');";

    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if ($wynik->num_rows == 0) {
        $dane_poprawne++;
    } else {
        pokazKomunikat("Podany login lub adres e-mail jest już zajęty");

    }

    if($haslo_1 == $haslo_2) {
        $haslo = password_hash($haslo_1, PASSWORD_BCRYPT);
        $dane_poprawne++;
    } else {
        pokazKomunikat("Wprowadzono nieprawidłowe dane");
    }

    if($dane_poprawne == 3) {

        $kod_weryfikacyjny = md5(uniqid(rand(), true));
        $login = $polaczenie_BD->real_escape_string($login);
        $haslo = $polaczenie_BD->real_escape_string($haslo);
        $imie = $polaczenie_BD->real_escape_string($imie);
        $nazwisko = $polaczenie_BD->real_escape_string($nazwisko);
        $email = $polaczenie_BD->real_escape_string($email);

        $zapytanie_SQL = "INSERT INTO unverified_users (Login, Password, Name, Surname, Email, Code)
    VALUES (lower('{$login}'), '{$haslo}', '{$imie}','{$nazwisko}', '{$email}', '{$kod_weryfikacyjny}')";

        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        if($wynik === TRUE) {

            $link = "http://".$_SERVER['HTTP_HOST']."/activate_account.php?id=".$kod_weryfikacyjny;
            $tresc = "Witaj, ".$imie."!\n\nKliknij poniższy odnośnik, aby aktywować konto w serwisie tv.ur.edu.pl:\n\n".$link."\n\nPozdrawiamy!";
            $wyslano_email = wyslijEmail($email,"Weryfikacja konta w serwisie UR TV",$tresc);

            pokazKomunikat("Aby dokończyć rejestrację, kliknij w link wysłany na Twój adres e-mail");
        } else {
            pokazKomunikat("Rejestracja nie powiodła się, spróbuj ponownie później");
        }
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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Zakładanie konta</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/walidacja_rejestracja.js"></script>
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
                    <li>Rejestracja</li>
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
                <div class="naglowek_formularza"><h1>Rejestracja nowego użytkownika</h1></div>
                <div class="tlo">
                    <div class="blok_formularza formularz_rejestracja blok_padding_gorny blok_padding_dolny">
                        <form id="register_form" name="register_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return Validateregister_form()">
                            <input type="hidden" name="form_name" value="register_form">
                            <div class="pole">
                                <input type="text" class="centrowanie" name="name" id="name" placeholder="">
                                <p>Imię:</p>
                            </div>
                            <div class="pole">
                                <input type="text" class="centrowanie" name="surname" id="surname" placeholder="">
                                <p>Nazwisko:</p>
                            </div>
                            <div class="pole">
                                <input type="text" class="centrowanie" name="login" id="login" placeholder="">
                                <p>Login:</p>
                            </div>
                            <div class="pole">
                                <input type="password" class="centrowanie" name="password_1" id="password_1" placeholder="">
                                <p>Hasło:</p>
                            </div>
                            <div class="pole">
                                <input type="password" class="centrowanie" name="password_2" id="password_2" placeholder="">
                                <p>Powtórz hasło:</p>
                            </div>
                            <div class="pole">
                                <input type="email" class="centrowanie" name="email" id="email" placeholder="">
                                <p>Adres e-mail:</p>
                            </div>
                            <div class="pole regulamin">
                                <input type="checkbox" id="terms" name="terms" value="on">
                                <p>Akceptuję <a href="terms.php">regulamin serwisu</a></p>
                            </div>
                            <div class="pole ostatnie_pole">
                                <button type="submit" class="button_1">Załóż konto</button>
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

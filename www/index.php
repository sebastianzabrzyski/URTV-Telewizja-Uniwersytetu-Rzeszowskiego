<?php
//error_reporting(0);
require_once("functions.php");
sprawdzZalogowanie("","");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_name']) && $_POST['form_name'] == 'newsletter_signup')
{

    $email = $_POST['email'];
    $polaczenie_BD = polaczDB();
    $email = $polaczenie_BD->real_escape_string($email);
    $zapytanie_SQL = "SELECT ID FROM newsletter WHERE Email = '{$email}';";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
    if ($wynik->num_rows > 0) {
        pokazKomunikat("Jesteś już dodany do newslettera");
    } else {

        $zapytanie_SQL2 = "SELECT ID FROM users WHERE Email = '{$email}';";
        $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL2);
        if ($wynik2->num_rows == 0) {
            $zapytanie_SQL = "INSERT INTO newsletter (Email)
      VALUES ('{$email}');";

            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
            if($wynik === TRUE) {
                pokazKomunikat("Zostałeś dodany do newslettera");
            } else {
                pokazKomunikat("Wystąpił błąd podczas dodawania do newslettera");
            }
        } else {
            pokazKomunikat("Edytuj ustawienia powiadomień na stronie swojego konta");
        }

    }
    przekierowanie($adres_obecny);
}
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
$id_strony = null;
if(isset($_GET['page'])) {
    $id_strony = $_GET['page'];
}

if(!is_numeric($id_strony) || $id_strony < 1) {
    $id_strony = 1;
}

$wszystkie_wyniki = 0;
$pokazywane_wyniki = 6;
$pominiete_wyniki = ($pokazywane_wyniki * $id_strony) - $pokazywane_wyniki;
$pokazywane_wyniki_tablica = array();
$planowane_transmisje_tablica = array();
$planowane_transmisje_pokazywanie = "display: none";
// STREAMY
$polaczenie_BD = polaczDB();
$streamy_sciezka = "/usr/local/antmedia/webapps/LiveApp/streams";
$zapytanie_SQL = "SELECT streams.ID, User_ID, Title, Views, Author, Planned_date, Filename, streams.Date, Streamkey_active, Streamkey_last, Describtion, streams.Password, Login FROM streams INNER JOIN users ON streams.User_ID = users.ID WHERE Streamkey_active IS NOT NULL ORDER BY streams.Date DESC LIMIT {$pokazywane_wyniki} OFFSET {$pominiete_wyniki};";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {

    while($wiersz = $wynik->fetch_assoc()) {

        $pokazac = false;
        $plik_istnieje = false;
        $active_key = $wiersz["Streamkey_active"];
        $last_key = $wiersz["Streamkey_last"];

        if (file_exists("{$streamy_sciezka}/{$active_key}.m3u8")) {
            $plik_istnieje = true;
        }

        if($active_key == $last_key) {

            if($plik_istnieje === true) {
                $pokazac = true;
            } else {
                $new_key = mt_rand(1000000000, 9999999999);
                $zapytanie_SQL = "UPDATE streams SET Streamkey_last = NULL, Streamkey_active = {$new_key} WHERE Streamkey_active = '{$active_key}';";
                $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
            }

        } else {

            if($plik_istnieje === true) {
                $pokazac = true;
                $zapytanie_SQL = "UPDATE streams SET Streamkey_last = {$active_key} WHERE Streamkey_active = '{$active_key}';";
                $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
            }
        }

        if($pokazac === true ) {
            $wszystkie_wyniki++;
            $id_streamu = $wiersz["ID"];
            $nazwa_pliku = $wiersz["Filename"];
            $adres_db = "./stream.php?id=".$id_streamu;
            $tytul_db = $wiersz["Title"];
            $opis_db = $wiersz["Describtion"];
            $autor = $wiersz["Author"];
            $wyswietlenia_db = $wiersz["Views"];
            $wyswietlenia_db = $wiersz["Views"];
            $wyswietlenia_db = number_format($wyswietlenia_db, 0, ',', ' ');
            $miniaturka_db = "miniatures_streams/".$nazwa_pliku.".jpeg";
            $data_publikacji_db = "Na żywo";
            $przesylajacy_db = $wiersz["Login"];
            $oznaczenie = "images/live.png";

            array_push($pokazywane_wyniki_tablica,array('tytul' => $tytul_db,'miniaturka' => $miniaturka_db,'link' => $adres_db,'opis' => $opis_db,'autor' => $autor,'wyswietlenia' => $wyswietlenia_db,
                                                        'przesylajacy' => $przesylajacy_db, 'data' => $data_publikacji_db, 'oznaczenie' => $oznaczenie));

        } else {

            $planowana_data = $wiersz["Planned_date"];
            if($planowana_data === null) {

            } else {
                $data_obecna =  date('Y.m.d H:i:s');
                $planowana_data = date("Y.m.d H:i:s", strtotime($planowana_data));

                if($planowana_data > $data_obecna) {

                    $id_streamu = $wiersz["ID"];
                    $nazwa_pliku = $wiersz["Filename"];
                    $adres_db = "./stream.php?id=".$id_streamu;
                    $tytul_db = $wiersz["Title"];
                    $autor = $wiersz["Author"];
                    $wyswietlenia_db = $wiersz["Views"];
                    $wyswietlenia_db = $wiersz["Views"];
                    $wyswietlenia_db = number_format($wyswietlenia_db, 0, ',', ' ');
                    $opis_db = $wiersz["Describtion"];
                    $miniaturka_db = "miniatures_streams/".$nazwa_pliku.".jpeg";
                    $przesylajacy_db = $wiersz["Login"];
                    $oznaczenie = "images/live.png";

                    array_push($planowane_transmisje_tablica,array('tytul' => $tytul_db,'miniaturka' => $miniaturka_db,'link' => $adres_db,'opis' => $opis_db,'autor' => $autor,
                                                                   'przesylajacy' => $przesylajacy_db, 'data' => $planowana_data,'wyswietlenia' => $wyswietlenia_db, 'oznaczenie' => $oznaczenie));
                    $planowane_transmisje_pokazywanie = "";
                }
            }
        }
    }
}

// STREAMY


$zapytanie_SQL_count = "SELECT count(ID) FROM movies WHERE Verified = 'Tak';";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
$wiersz = $wynik->fetch_row();
$wszystkie_wyniki = $wiersz[0] + $wszystkie_wyniki;
$zapytanie_SQL = "SELECT movies.ID, User_ID, Title, Views, Author, Filename, movies.Date, Describtion, movies.Password, Login FROM movies INNER JOIN users ON movies.User_ID = users.ID WHERE Verified = 'Tak' ORDER BY movies.Date DESC LIMIT {$pokazywane_wyniki} OFFSET {$pominiete_wyniki};";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
$id_filmu = null;

// wyswietlanie listy filmów
if ($wynik->num_rows > 0) {

    while($wiersz = $wynik->fetch_assoc()) {

        $id_filmu = $wiersz["ID"];
        $nazwa_pliku = $wiersz["Filename"];
        $adres_db = "./video.php?id=".$id_filmu;
        $tytul_db = $wiersz["Title"];
        $opis_db = $wiersz["Describtion"];
        $autor = $wiersz["Author"];
        $haslo_db = $wiersz["Password"];
        $wyswietlenia_db = $wiersz["Views"];
        $wyswietlenia_db = $wiersz["Views"];
        $wyswietlenia_db = number_format($wyswietlenia_db, 0, ',', ' ');
        $miniaturka_db = "miniatures/".$nazwa_pliku.".jpeg";
        $data_publikacji_db = $wiersz["Date"];
        $data_publikacji_db = new DateTime($data_publikacji_db);
        $data_publikacji_db = $data_publikacji_db->format('d.m.Y');
        $przesylajacy_db = $wiersz["Login"];

        if($haslo_db != null) {
            $oznaczenie = "images/lock.png";
        } else {
            $oznaczenie = "images/none.png";
        }
        array_push($pokazywane_wyniki_tablica,array('tytul' => $tytul_db,'miniaturka' => $miniaturka_db,'link' => $adres_db,'opis' => $opis_db,'autor' => $autor,'wyswietlenia' => $wyswietlenia_db,
                                                    'przesylajacy' => $przesylajacy_db, 'data' => $data_publikacji_db, 'oznaczenie' => $oznaczenie));
    }
}

//paginacja

$ilosc_stron = $wszystkie_wyniki / $pokazywane_wyniki;
$ilosc_stron = ceil($ilosc_stron);

if($id_strony >7) {
    $page_8 = $id_strony;
} else {
    $page_8 = 8;
}

$page_previous = $id_strony - 1;
$page_1 = $page_8 - 7;
$page_2 = $page_8 - 6;
$page_3 = $page_8 - 5;
$page_4 = $page_8 - 4;
$page_5 = $page_8 - 3;
$page_6 = $page_8 - 2;
$page_7 = $page_8 - 1;
$page_8 = $page_8;
$page_9 = $page_8 + 1;
$page_10 = $page_8 + 2;
$page_11 = $page_8 + 3;
$page_12 = $page_8 + 4;
$page_next = $id_strony + 1;

$styl_aktywny = ' class="aktywne"';
$styl_page_1 = $styl_page_2 = $styl_page_3 = $styl_page_4 = $styl_page_5 = $styl_page_6 = $styl_page_7 = $styl_page_8 = $styl_page_9 = $styl_page_10 = $styl_page_11 = $styl_page_12 = "";

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
    case $page_8:
        $styl_page_8 = $styl_aktywny;
        break;
    case $page_9:
        $styl_page_9 = $styl_aktywny;
        break;

    case $page_10:
        $styl_page_10 = $styl_aktywny;
        break;
    case $page_11:
        $styl_page_11 = $styl_aktywny;
        break;
    case $page_12:
        $styl_page_12 = $styl_aktywny;
        break;
    default:
        break;
}


// pobieranie kategorii

$grupy_kategorii = array();
$kategorie = array();

$zapytanie_SQL = "SELECT * FROM categories;";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {

    while($wiersz = $wynik->fetch_assoc()) {

        $id = $wiersz["ID"];
        $nazwa = $wiersz["Name"];
        $id_grupy = $wiersz["Group_ID"];
        array_push($kategorie, array($id,$nazwa,$id_grupy));
    }
}

$zapytanie_SQL = "SELECT * FROM categories_groups;";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {

    while($wiersz = $wynik->fetch_assoc()) {

        $id = $wiersz["ID"];
        $nazwa = $wiersz["Name"];
        array_push($grupy_kategorii, array($id,$nazwa));
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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Strona Główna</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/kategorie.js"></script>
        <script src="./js/walidacja_newsletter.js"></script>
    </head>
    <body>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = 'https://connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v3.2';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
        <header>
            <div class="kontener">
                <div id="naglowek_tytul">
                    <div id="logo_strony">
                        <a href="./index.php">
                            <img src="./images/logo_UR.png" alt="obrazek">
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
                    <li>Strona główna</li>
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

                <div id="blok_lewy">


                    <div id="lista_planowanych_transmisji" style="<?php echo $planowane_transmisje_pokazywanie; ?>" class="clearfix">
                        <h1>Planowane transmisje:</h1>
                        <ul>
                            <?php
                            foreach ($planowane_transmisje_tablica as $wynik) {
                                $miniaturka = $wynik['miniaturka'];
                                $link = $wynik['link'];
                                $tytul = $wynik['tytul'];
                                $oznaczenie = $wynik['oznaczenie'];
                                $autor = $wynik['autor'];
                                $wyswietlenia = $wynik['wyswietlenia'];
                                $opis = $wynik['opis'];
                                $przesylajacy = $wynik['przesylajacy'];
                                $data_publikacji = $wynik['data'];
                                echo '<li>
              <a href="#"><img class="miniaturka_filmu" src="'.$miniaturka.'" alt="obrazek"></a>
              <div class="dane_filmu">
              <h2><a href="#">'.$tytul.'</a>
              <img class="oznaczenie_filmu" src="'.$oznaczenie.'" alt="obrazek">
              </h2>
              <p class="opis_filmu">'.$opis.'</p>
              <p class="info_filmu">Autor: '.$autor.'<br>Data transmisji: '.$data_publikacji.'<br></p>
              </div>
              </li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <div id="lista_filmow" class="clearfix">
                        <h1>Ostatnio dodane:</h1>
                        <ul>
                            <?php
                            foreach ($pokazywane_wyniki_tablica as $wynik) {
                                $miniaturka = $wynik['miniaturka'];
                                $link = $wynik['link'];
                                $tytul = $wynik['tytul'];
                                $oznaczenie = $wynik['oznaczenie'];
                                $opis = $wynik['opis'];
                                $autor = $wynik['autor'];
                                $wyswietlenia = $wynik['wyswietlenia'];
                                $przesylajacy = $wynik['przesylajacy'];
                                $data_publikacji = $wynik['data'];
                                echo '<li>
              <a href="'.$link.'"><img class="miniaturka_filmu" src="'.$miniaturka.'" alt="obrazek"></a>
              <div class="dane_filmu">
              <h2><a href="'.$link.'">'.$tytul.'</a>
              <img class="oznaczenie_filmu" src="'.$oznaczenie.'" alt="obrazek">
              </h2>
              <p class="opis_filmu">'.$opis.'</p>
              <p class="info_filmu">Wyświetlenia: '.$wyswietlenia.'<br>Autor: '.$autor.'<br>Opublikowano: '.$data_publikacji.'<br></p>
              </div>
              </li>';
                            }
                            ?>
                        </ul>
                        <div class="paginacja">
                            <?php
                            if($page_previous > 0 && $id_strony <= $ilosc_stron) echo "<a href=\"./index.php?page={$page_previous}\">Poprzednia</a>";
                            if($page_1 <= $ilosc_stron) echo "<a {$styl_page_1} href=\"./index.php?page={$page_1}\">{$page_1}</a>";
                            if($page_2 <= $ilosc_stron) echo "<a {$styl_page_2} href=\"./index.php?page={$page_2}\">{$page_2}</a>";
                            if($page_3 <= $ilosc_stron) echo "<a {$styl_page_3} href=\"./index.php?page={$page_3}\">{$page_3}</a>";
                            if($page_4 <= $ilosc_stron) echo "<a {$styl_page_4} href=\"./index.php?page={$page_4}\">{$page_4}</a>";
                            if($page_5 <= $ilosc_stron) echo "<a {$styl_page_5} href=\"./index.php?page={$page_5}\">{$page_5}</a>";
                            if($page_6 <= $ilosc_stron) echo "<a {$styl_page_6} href=\"./index.php?page={$page_6}\">{$page_6}</a>";
                            if($page_7 <= $ilosc_stron) echo "<a {$styl_page_7} href=\"./index.php?page={$page_7}\">{$page_7}</a>";
                            if($page_8 <= $ilosc_stron) echo "<a {$styl_page_8} href=\"./index.php?page={$page_8}\">{$page_8}</a>";
                            if($page_9 <= $ilosc_stron) echo "<a {$styl_page_9} href=\"./index.php?page={$page_9}\">{$page_9}</a>";
                            if($page_10 <= $ilosc_stron) echo "<a {$styl_page_10} href=\"./index.php?page={$page_10}\">{$page_10}</a>";
                            if($page_11 <= $ilosc_stron) echo "<a {$styl_page_11} href=\"./index.php?page={$page_11}\">{$page_11}</a>";
                            if($page_12 <= $ilosc_stron) echo "<a {$styl_page_12} href=\"./index.php?page={$page_12}\">{$page_12}</a>";
                            if($id_strony < $ilosc_stron) echo "<a href=\"./index.php?page={$page_next}\">Następna</a>";
                            ?>
                        </div>
                    </div>
                </div>

                <div id="blok_prawy">
                    <div id="lista_kategorii">
                        <h1>Wybierz kategorię:</h1>
                        <ul role="menu">

                            <?php

                            foreach($grupy_kategorii as $grupa) {

                                $id_grupy = $grupa[0];
                                $nazwa_grupy = $grupa[1];


                                echo '<li class="folder" aria-haspopup="true"><a>'.$nazwa_grupy.'&nbsp;&#9662;</a>
                  <ul role="menu" style="display:none" aria-expanded="false">';

                                foreach ($kategorie as $kategoria) {
                                    $id_kategorii = $kategoria[0];
                                    $nazwa_kategorii = $kategoria[1];
                                    $id_przypisanej_grupy =$kategoria[2];
                                    if ($id_przypisanej_grupy == $id_grupy) {
                                        echo '<li><a role="menuitem" href="./search.php?category='.$id_kategorii.'">'.$nazwa_kategorii.'</a></li>';
                                    }
                                }

                                echo '</ul>
                  </li>';

                            }

                            foreach ($kategorie as $kategoria) {
                                $id_kategorii = $kategoria[0];
                                $nazwa_kategorii = $kategoria[1];
                                $id_przypisanej_grupy =$kategoria[2];

                                if ($id_przypisanej_grupy == null) {
                                    echo '<li><a role="menuitem" href="./search.php?category='.$id_kategorii.'">'.$nazwa_kategorii.'</a>
                  </li>';
                                }
                            }


                            ?>

                        </ul>
                    </div>
                    <div id="facebook">
                        <h1>Nasz profil na Facebooku:</h1>
                        <div class="fb-page" data-href="https://www.facebook.com/uniRzeszow/" data-tabs="timeline" height="660" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/uniRzeszow/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/uniRzeszow/">Uniwersytet Rzeszowski</a></blockquote></div>
                    </div>
                    <form id="newsletter" class="clearfix" name="newsletter_signup" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return Validatenewsletter_signup()">
                        <input type="text" id="email" name="email" placeholder="Dołącz do newslettera...">
                        <input type="hidden" name="form_name" value="newsletter_signup">
                        <button type="submit" class="button_1">&#10004;</button>
                    </form>
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

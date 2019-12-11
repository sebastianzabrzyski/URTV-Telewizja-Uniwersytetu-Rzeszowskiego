<!-- Strona z panelem administratora -->

<?php

require_once("functions.php");

sprawdzZalogowanie("","./login.php?return=admin.php");

if($uprawnienia != "Administrator") {
    pokazKomunikat("Nie posiadasz wymaganych uprawnień");
    przekierowanie("./index.php");
}


$menu_1 = "Moje konto";
$menu_2 = "Wyloguj się";
$link_1 = "./account.php";
$link_2 = "./logout.php";
$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$polaczenie_BD = polaczDB();
$id_strony = null;

if(isset($_GET['page'])) {
    $id_strony = $_GET['page'];
}

if(!is_numeric($id_strony) || $id_strony < 1) {
    $id_strony = 1;
}

//$wszystkie_wyniki = 0;
//$pokazywane_wyniki = 5;
//$pominiete_wyniki = ($pokazywane_wyniki * $id_strony) - $pokazywane_wyniki;
$polaczenie_BD = polaczDB();
$id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
$zapytanie_SQL_count = "SELECT count(ID) FROM users WHERE ID NOT LIKE {$id_uzytkownika};";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
$wiersz = $wynik->fetch_row();
$wszystkie_wyniki = $wiersz[0];
$zapytanie_SQL = "SELECT ID, Login, Privileges, Space, Date FROM users WHERE ID NOT LIKE {$id_uzytkownika} ORDER BY Date DESC;";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
$id_filmu = null;
// wyswietlanie listy filmów
if ($wynik->num_rows > 0) {

    $uzytkownicy_tablica = array();

    while($wiersz = $wynik->fetch_assoc()) {

        $id_uzytkownika = $wiersz["ID"];
        $login = $wiersz["Login"];
        $uprawnienia = $wiersz["Privileges"];
        $przyznane_miejsce = $wiersz["Space"];
        $data_rejestracji = $wiersz["Date"];
        $data_rejestracji = new DateTime($data_rejestracji);
        $data_rejestracji = $data_rejestracji->format('d.m.Y H:i');
        $usun = "./delete_user.php?id=".$id_uzytkownika;
        $edytuj = "./edit_user.php?id=".$id_uzytkownika;
        $id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
        $zapytanie_SQL_filmy = "SELECT COUNT(ID) FROM movies WHERE User_ID = {$id_uzytkownika} ;";
        $wynik_fil = wykonajSQL($polaczenie_BD,$zapytanie_SQL_filmy);
        $wiersz_fil = $wynik_fil->fetch_row();
        $filmy = $wiersz_fil[0];
        //wyk
        $zapytanie_SQL_wykorzystane = "SELECT SUM(Size) AS Wykorzystane FROM movies WHERE User_ID = {$id_uzytkownika};";
        $wynik_wykorzystane = wykonajSQL($polaczenie_BD,$zapytanie_SQL_wykorzystane);

        if ($wynik_wykorzystane->num_rows > 0) {
            while($wiersz = $wynik_wykorzystane->fetch_assoc()) {
                $wykorzystane_miejsce = $wiersz["Wykorzystane"];
                break;
            }
        }
        if (!$wykorzystane_miejsce > 0) $wykorzystane_miejsce = "0";
        $wykorzystane = $wykorzystane_miejsce." MB z ".$przyznane_miejsce." MB";
        array_push($uzytkownicy_tablica,array('login' => $login,'uprawnienia' => $uprawnienia,'data_rejestracji' => $data_rejestracji,'id' => $id_uzytkownika,'link_usun' => $usun,'link_edytuj' => $edytuj,'filmy' => $filmy,'wykorzystane_miejsce' => $wykorzystane));
    }
}

// oczekujące filmy

$id_strony_ocz = null;

if(isset($_GET['page_awaiting'])) {
    $id_strony_ocz = $_GET['page_awaiting'];
}



if(!is_numeric($id_strony_ocz) || $id_strony_ocz < 1) {
    $id_strony_ocz = 1;
}

//$wszystkie_wyniki_ocz = 0;
//$pokazywane_wyniki_ocz = 5;
//$pominiete_wyniki_ocz = ($pokazywane_wyniki_ocz * $id_strony_ocz) - $pokazywane_wyniki_ocz;


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


$zapytanie_SQL_count = "SELECT count(ID) FROM movies WHERE Verified = 'Nie';";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
$wiersz = $wynik->fetch_row();
$wszystkie_wyniki_ocz = $wiersz[0];
$zapytanie_SQL = "SELECT movies.ID, User_ID, Title, Filename, Views, movies.Date, users.Login, Describtion FROM movies INNER JOIN users ON movies.User_ID = users.ID WHERE Verified = 'Nie' ORDER BY movies.Date DESC;";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
$id_filmu = null;

// wyswietlanie listy filmów

if ($wynik->num_rows > 0) {

    $oczekujace_filmy_tablica = array();

    while($wiersz = $wynik->fetch_assoc()) {
        $id_filmu = $wiersz["ID"];
        $nazwa_pliku = $wiersz["Filename"];
        $adres_db = "./video.php?id=".$id_filmu;
        $tytul_db = $wiersz["Title"];
        $opis_db = $wiersz["Describtion"];
        $miniaturka_db = "miniatures/".$nazwa_pliku.".jpeg";
        $data_publikacji_db = $wiersz["Date"];
        $data_publikacji_db = new DateTime($data_publikacji_db);
        $data_publikacji_db = $data_publikacji_db->format('d.m.Y H:i');

        $zapytanie_SQL2= "SELECT Category_ID FROM categories_videos WHERE Movie_ID = '{$id_filmu}';";
        $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL2);
        $kategorie_filmu_ids = array();
        if ($wynik2->num_rows > 0) {
            while($wiersz2 = $wynik2->fetch_assoc()) {
                array_push($kategorie_filmu_ids,$wiersz2["Category_ID"]);
            }
        }
        $kategorie_filmu_nazwy = array();

        foreach($kategorie as $kategoria) {
            $id_kategorii = $kategoria[0];
            if (in_array($id_kategorii, $kategorie_filmu_ids)) {
                array_push($kategorie_filmu_nazwy, $kategoria[1]);
            }
        }

        $kategorie_filmu_nazwy_string = implode(", ", $kategorie_filmu_nazwy);


        array_push($oczekujace_filmy_tablica,array('id' => $id_filmu,'adres' => $adres_db,'tytul' => $tytul_db,'opis' => $opis_db,'miniaturka' => $miniaturka_db,'data_publikacji' => $data_publikacji_db,'przesylajacy' => $wiersz["Login"],'kategorie' => $kategorie_filmu_nazwy_string));
    }
}
// oczekujące filmy
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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Panel administratora</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/admin.js"></script>
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
                    <li><a href="./account.php">Moje konto</a></li>
                    <li>Panel administratora</li>
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

                <div class="naglowek_formularza"><h1>Oczekujące filmy</h1></div>
                <div id="blok_oczekujace_filmy" class="blok_odstep_dolny">
                    <div class="tlo">
                        <div class="blok_panelu blok_padding_gorny">
                            <div class="wiersz_oczekujace_naglowek clearfix">
                                <div class="kolumna_oczekujace">
                                    <h1>Miniaturka</h1>
                                </div>
                                <div class="kolumna_oczekujace">
                                    <h1>Tytuł i opis</h1>
                                </div>
                                <div class="kolumna_oczekujace kolumna_przesylajacy">
                                    <h1>Przesłane przez</h1>
                                </div>
                                <div class="kolumna_oczekujace kolumna_kategoria">
                                    <h1>Kategoria</h1>
                                </div>
                                <div class="kolumna_oczekujace kolumna_data">
                                    <h1>Data przesłania</h1>
                                </div>
                                <div class="kolumna_oczekujace ">
                                </div>
                            </div>
                            <?php
                            foreach ($oczekujace_filmy_tablica as $wynik) {
                                $id = $wynik['id'];
                                $adres = $wynik['adres'];
                                $tytul = $wynik['tytul'];
                                $kategorie_filmu = $wynik['kategorie'];
                                $opis = $wynik['opis'];
                                $miniaturka = $wynik['miniaturka'];
                                $data_publikacji = $wynik['data_publikacji'];
                                $przesylajacy = $wynik['przesylajacy'];
                                echo '<div class="wiersz_oczekujace clearfix">
              <div class="kolumna_oczekujace">
              <a href="'.$adres.'"><img class="miniaturka_oczekujacego_filmu" src="'.$miniaturka.'"></a>
              </div>
              <div class="kolumna_oczekujace tytul_i_opis">
              <a href="'.$adres.'"><h2>'.$tytul.'</h2></a>
              <p>'.$opis.'</p>
              </div>
              <div class="kolumna_oczekujace kolumna_przesylajacy">
              <p>'.$przesylajacy.'</p>
              </div>
              <div class="kolumna_oczekujace kolumna_kategoria">
              <p>'.$kategorie_filmu.'</p>
              </div>
              <div class="kolumna_oczekujace kolumna_data">
              <p>'.$data_publikacji.'</p>
              </div>
              <div class="kolumna_oczekujace kolumna_przyciski flex">
              <img class="wskaznik" src="./images/delete.png" onclick="potwierdzUsuniecieFilmu('.$id.');return false;">
              <img class="wskaznik" src="./images/checkmark-24.png" onclick="potwierdzAkceptacjeFilmu('.$id.');return false;">
              </div>
              </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>








                <div class="naglowek_formularza"><h1>Kategorie</h1></div>
                <div id="blok_kategorie" class="blok_odstep_dolny">
                    <div class="tlo">
                        <div class="blok_panelu blok_padding_gorny">
                            <div class="wiersz_kategorie_naglowek clearfix">
                                <div class="kolumna_uzytkownicy">
                                    <h1>Nazwa</h1>
                                </div>
                                <div class="kolumna_uzytkownicy">
                                    <h1>Typ</h1>
                                </div>
                                <div class="kolumna_uzytkownicy kolumna_przeslane">
                                    <h1>Liczba kategorii</h1>
                                </div>
                                <div class="kolumna_uzytkownicy desktop">
                                    <h1>Należy do</h1>
                                </div>
                                <div class="kolumna_uzytkownicy desktop">
                                </div>
                                <div class="kolumna_uzytkownicy">
                                </div>
                            </div>
                            <?php

                            foreach($grupy_kategorii as $grupa) {

                                $id_grupy = $grupa[0];
                                $nazwa_grupy = $grupa[1];
                                $zapytanie_SQL_count = "SELECT count(ID) FROM categories WHERE Group_ID = '{$id_grupy}';";
                                $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
                                $wiersz = $wynik->fetch_row();
                                $liczba_kategorii = $wiersz[0];
                                $tryb_usuniecie = "potwierdzUsuniecieGrupy";
                                $link_edytuj = "./edit_group.php?id={$id}";

                                echo '<div class="wiersz_uzytkownicy clearfix">
              <div class="kolumna_uzytkownicy">
              <p>'.$nazwa_grupy.'</p>
              </div>
              <div class="kolumna_uzytkownicy">
              <p>Grupa</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przeslane">
              <p>'.$liczba_kategorii.'</p>
              </div>
              <div class="kolumna_uzytkownicy desktop">
              <p>---</p>
              </div>
              <div class="kolumna_uzytkownicy desktop">
              <p>&nbsp;</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przyciski flex">
              <a href="'.$link_edytuj.'"><img src="./images/settings2.png"></a>
              <img src="./images/delete.png" onclick="'.$tryb_usuniecie.'('.$id_grupy.');return false;">
              </div>
              </div>';

                            }

                            foreach($kategorie as $kategoria) {

                                $id_kategorii = $kategoria[0];
                                $nazwa_kategorii= $kategoria[1];
                                $id_przypisanej_grupy= $kategoria[2];

                                if($id_przypisanej_grupy != null) {
                                    foreach($grupy_kategorii as $grupa) {
                                        if($grupa[0] == $id_przypisanej_grupy) {
                                            $nazwa_przypisanej_grupy = $grupa[1];
                                            break;
                                        }
                                    }
                                } else {
                                    $nazwa_przypisanej_grupy = "---";

                                }

                                $tryb_usuniecie = "potwierdzUsuniecieKategorii";
                                $link_edytuj = "./edit_category.php?id={$id_kategorii}";
                                echo '<div class="wiersz_uzytkownicy clearfix">
              <div class="kolumna_uzytkownicy">
              <p>'.$nazwa_kategorii.'</p>
              </div>
              <div class="kolumna_uzytkownicy">
              <p>Kategoria</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przeslane">
              <p>---</p>
              </div>
              <div class="kolumna_uzytkownicy desktop">
              <p>'.$nazwa_przypisanej_grupy.'</p>
              </div>
              <div class="kolumna_uzytkownicy desktop">
              <p>&nbsp;</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przyciski flex">
              <a href="'.$link_edytuj.'"><img src="./images/settings2.png"></a>
              <img src="./images/delete.png" onclick="'.$tryb_usuniecie.'('.$id_kategorii.');return false;">
              </div>
              </div>';

                            }

                            ?>
                            <div class="przeslane_przyciski blok_odstep_dolny clearfix">
                                <a href="./add_category.php"><button type="button" class="przycisk_1">Dodaj kategorię</button></a>
                                <a href="./add_group.php"><button style="<?php echo $transmisje_widocznosc; ?>" type="button" class="przycisk_2">Dodaj grupę kategorii</button></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="naglowek_formularza"><h1>Administracja użytkownikami</h1></div>
                <div id="blok_uzytkownicy">
                    <div class="tlo">
                        <div class="blok_panelu blok_padding_gorny">
                            <div class="wiersz_uzytkownicy_naglowek clearfix">
                                <div class="kolumna_uzytkownicy">
                                    <h1>Użytkownik</h1>
                                </div>
                                <div class="kolumna_uzytkownicy">
                                    <h1>Uprawnienia</h1>
                                </div>
                                <div class="kolumna_uzytkownicy kolumna_przeslane">
                                    <h1>Przesłane filmy</h1>
                                </div>
                                <div class="kolumna_uzytkownicy">
                                    <h1>Zajęte miejsce</h1>
                                </div>
                                <div class="kolumna_uzytkownicy">
                                    <h1>Data rejestracji</h1>
                                </div>
                                <div class="kolumna_uzytkownicy">
                                </div>
                            </div>
                            <?php
                            foreach ($uzytkownicy_tablica as $wynik) {
                                $uzytkownik = $wynik['login'];
                                $uprawnienia = $wynik['uprawnienia'];
                                $data_rejestracji = $wynik['data_rejestracji'];
                                $id = $wynik['id'];
                                $link_usun = $wynik['link_usun'];
                                $link_edytuj = $wynik['link_edytuj'];
                                $filmy = $wynik['filmy'];
                                $wykorzystane_miejsce = $wynik['wykorzystane_miejsce'];
                                echo '<div class="wiersz_uzytkownicy clearfix">
              <div class="kolumna_uzytkownicy">
              <p>'.$uzytkownik.'</p>
              </div>
              <div class="kolumna_uzytkownicy">
              <p>'.$uprawnienia.'</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przeslane">
              <p>'.$filmy.'</p>
              </div>
              <div class="kolumna_uzytkownicy">
              <p>'.$wykorzystane_miejsce.'</p>
              </div>
              <div class="kolumna_uzytkownicy">
              <p>'.$data_rejestracji.'</p>
              </div>
              <div class="kolumna_uzytkownicy kolumna_przyciski flex">
              <a href="'.$link_edytuj.'"><img src="./images/settings2.png"></a>
              <img src="./images/delete.png" onclick="potwierdzUsuniecieUzytkownika('.$id.');return false;">
              </div>
              </div>';
                            }
                            ?>
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

<!-- Strona pokazująca wyniki wyszukiwania materiałów wideo -->

<?php
//error_reporting(0);

require_once("functions.php");
sprawdzZalogowanie("","");

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
$id_strony = $fraza = $tryb = "";
$sortowanie = "Date DESC";
$data_filter = "Dowolne";
$kategoria = "Dowolne";
$haslo_filter = "Dowolne";
$where = "";
$polaczenie_BD = polaczDB();

if(isset($_GET['page'])) {
    $id_strony = $_GET['page'];
}

if(!is_numeric($id_strony) || $id_strony < 1) {
    $id_strony = 1;
}

if(isset($_GET['phrase'])) {
    $fraza = $_GET["phrase"];
}

if(isset($_GET['mode'])) {
    $tryb = $_GET["mode"];
}

if(isset($_GET['category'])) {
    $kategoria = $_GET["category"];
}

if(isset($_GET['password'])) {
    $haslo_filter = $_GET["password"];
}

if(isset($_GET['date'])) {
    $data_filter = $_GET["date"];
}

if(isset($_GET['sort'])) {
    $sortowanie = $_GET["sort"];
}

if($tryb != "Tytuł" && $tryb != "Autor" && $tryb != "Tagi") {
    $tryb = "Tytuł";
}

if($tryb == "Tytuł") {
    $where = "WHERE lower(Title) LIKE lower('%{$fraza}%')";
}

if($tryb == "Autor") {

    $fraza = $polaczenie_BD->real_escape_string($fraza);
    //$where = "WHERE User_ID IN (SELECT ID from users WHERE lower(Login) LIKE lower('%{$fraza}%'))";
    $where = "WHERE lower(Author) LIKE lower('%{$fraza}%')";

}

if($tryb == "Tagi") {

    $tagi = "";
    $tagi_tablica = array();
    $pieces = explode(",", $fraza);

    foreach ($pieces as $value) {
        $value = trim($value);
        $value = $polaczenie_BD->real_escape_string($value);
        $value = "'".$value."'";

        array_push($tagi_tablica, $value);
    }

    $tagi = implode(", ", $tagi_tablica);
    $where = "WHERE movies.ID IN (SELECT Movie_ID FROM tags WHERE Name IN ({$tagi}))";

}

if($kategoria != "" && $kategoria != "Dowolne") {

    $where = $where." AND movies.ID IN (SELECT Movie_ID FROM categories_videos WHERE Category_ID = '{$kategoria}')";
}

if($haslo_filter != "" && $haslo_filter != "Dowolne") {

    if($haslo_filter == "Tak") {
        $where = $where." AND movies.Password IS NOT NULL";
    } else {
        $where = $where." AND movies.Password IS NULL";
    }
}

if($data_filter != "" && $data_filter != "Dowolne") {

    if($data_filter == "Dzisiaj") {
        $data = date('Y-m-d 00:00:00');
    }

    if($data_filter == "Ostatni tydzień") {
        $data = date('Y-m-d H:i:s', strtotime('-7 day', time()));
    }

    if($data_filter == "Ostatni miesiąc") {
        $data = date('Y-m-d H:i:s', strtotime('-1 month', time()));
    }

    if($data_filter == "Ostatni rok") {

        $data = date('Y-m-d H:i:s', strtotime('-1 year', time()));
    }

    $where = $where." AND movies.Date > '{$data}'";
}

$wszystkie_wyniki = 0;
$pokazywane_wyniki = 6;
$pominiete_wyniki = ($pokazywane_wyniki * $id_strony) - $pokazywane_wyniki;
$pokazywane_wyniki_tablica = array();

$where = $where." AND Verified = 'Tak'";
$zapytanie_SQL_count = "SELECT count(ID) FROM movies {$where};";

$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
$wiersz = $wynik->fetch_row();
$wszystkie_wyniki = $wiersz[0];
$sortowanie = $polaczenie_BD->real_escape_string($sortowanie);
$zapytanie_SQL = "SELECT movies.ID, User_ID, Title, Filename, Author, Views, movies.Date, Describtion, movies.Password, Login, COALESCE(polubienia.Suma, 0) AS Likes
          FROM movies
          INNER JOIN users ON movies.User_ID = users.ID
          LEFT JOIN (SELECT Movie_ID,
          Lubie-Nie_lubie AS Suma FROM (SELECT Movie_ID, SUM(CASE WHEN Type = 'Like' THEN 1 ELSE 0 END) AS Lubie,
          SUM(CASE WHEN Type = 'Unlike' THEN 1 ELSE 0 END) AS Nie_lubie
          FROM likes GROUP BY Movie_ID) AS polubienia_temp)
          AS polubienia ON movies.ID = polubienia.Movie_ID {$where} ORDER BY {$sortowanie} LIMIT {$pokazywane_wyniki} OFFSET {$pominiete_wyniki};";

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
        $haslo_db = $wiersz["Password"];
        $autor = $wiersz["Author"];
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
        array_push($pokazywane_wyniki_tablica,array('tytul' => $tytul_db,'miniaturka' => $miniaturka_db,'link' => $adres_db,'opis' => $opis_db,'wyswietlenia' => $wyswietlenia_db,
                                                    'przesylajacy' => $przesylajacy_db, 'data' => $data_publikacji_db, 'oznaczenie' => $oznaczenie, 'autor' => $autor));
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
// pobieranie lista_kategorii
$kategorie_tablica = array();
$zapytanie_SQL = "SELECT ID, Name
  FROM categories;";

$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {
    // output data of each row
    while($wiersz = $wynik->fetch_assoc()) {
        $nazwa = $wiersz["Name"];
        $id = $wiersz["ID"];
        array_push($kategorie_tablica,array('nazwa' => $nazwa,'id' => $id));
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="description" content="Telewizja internetowa Uniwersytetu Rzeszowskiego">
        <meta name="keywords" content="Uniwersytet Rzeszowski, Telewizja internetowa, Studia">
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Wyniki wyszukiwania</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/walidacja_filtrowanie.js"></script>
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
                    <li>Wyniki wyszukiwania</li>
                </ul>

                <form id="wyszukiwarka" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                    <div id="lista_filmow">
                        <h1>Wyniki wyszukiwania: <?php echo $wszystkie_wyniki; ?></h1>
                        <ul>
                            <?php
                            foreach ($pokazywane_wyniki_tablica as $wynik) {
                                $miniaturka = $wynik['miniaturka'];
                                $link = $wynik['link'];
                                $tytul = $wynik['tytul'];
                                $oznaczenie = $wynik['oznaczenie'];
                                $autor = $wynik['autor'];
                                $opis = $wynik['opis'];
                                $wyswietlenia = $wynik['wyswietlenia'];
                                $przesylajacy = $wynik['przesylajacy'];
                                $data_publikacji = $wynik['data'];

                                echo '<li>
                <a href="'.$link.'"><img class="miniaturka_filmu" src="'.$miniaturka.'"></a>
                <div class="dane_filmu">
                <h2><a href="'.$link.'">'.$tytul.'</a>
                <img class="oznaczenie_filmu" src="'.$oznaczenie.'">
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

                            $parametry = "?phrase={$fraza}&mode={$tryb}&category={$kategoria}&password={$haslo_filter}&date={$data_filter}&sort={$sortowanie}";

                            if($page_previous > 0 && $id_strony <= $ilosc_stron) echo "<a href=\"./search.php".$parametry."&page={$page_previous}\">Poprzednia</a>";
                            if($page_1 <= $ilosc_stron) echo "<a {$styl_page_1} href=\"./search.php".$parametry."&page={$page_1}\">{$page_1}</a>";
                            if($page_2 <= $ilosc_stron) echo "<a {$styl_page_2} href=\"./search.php".$parametry."&page={$page_2}\">{$page_2}</a>";
                            if($page_3 <= $ilosc_stron) echo "<a {$styl_page_3} href=\"./search.php".$parametry."&page={$page_3}\">{$page_3}</a>";
                            if($page_4 <= $ilosc_stron) echo "<a {$styl_page_4} href=\"./search.php".$parametry."&page={$page_4}\">{$page_4}</a>";
                            if($page_5 <= $ilosc_stron) echo "<a {$styl_page_5} href=\"./search.php".$parametry."&page={$page_5}\">{$page_5}</a>";
                            if($page_6 <= $ilosc_stron) echo "<a {$styl_page_6} href=\"./search.php".$parametry."&page={$page_6}\">{$page_6}</a>";
                            if($page_7 <= $ilosc_stron) echo "<a {$styl_page_7} href=\"./search.php".$parametry."&page={$page_7}\">{$page_7}</a>";
                            if($page_8 <= $ilosc_stron) echo "<a {$styl_page_8} href=\"./search.php".$parametry."&page={$page_8}\">{$page_8}</a>";
                            if($page_9 <= $ilosc_stron) echo "<a {$styl_page_9} href=\"./search.php".$parametry."&page={$page_9}\">{$page_9}</a>";
                            if($page_10 <= $ilosc_stron) echo "<a {$styl_page_10} href=\"./search.php".$parametry."&page={$page_10}\">{$page_10}</a>";
                            if($page_11 <= $ilosc_stron) echo "<a {$styl_page_11} href=\"./search.php".$parametry."&page={$page_11}\">{$page_11}</a>";
                            if($page_12 <= $ilosc_stron) echo "<a {$styl_page_12} href=\"./search.php".$parametry."&page={$page_12}\">{$page_12}</a>";
                            if($id_strony < $ilosc_stron) echo "<a href=\"./search.php".$parametry."&page={$page_next}\">Następna</a>";
                            ?>
                        </div>
                    </div>
                </div>

                <div id="blok_prawy">
                    <div id="panel_filtrowania">
                        <h1>Filtrowanie wyników:</h1>
                        <form id="filter_form" name="filter_form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return return Validatefilter_form()">
                            <input type="hidden" name="mode" value="<?php if(isset($_GET["mode"])) echo $_GET["mode"]; ?>">
                            <input type="hidden" name="phrase" value="<?php if(isset($_GET["phrase"])) echo $_GET["phrase"]; ?>">
                            <h3>Sortuj wg:</h3>
                            <select name="sort" id="sort">
                                <option selected value="Date DESC">Data przesłania ▼</option>
                                <option value="Date ASC">Data przesłania ▲</option>
                                <option value="Title DESC">Tytuł ▼</option>
                                <option value="Title ASC">Tytuł ▲</option>
                                <option value="Views DESC">Wyświetlenia ▼</option>
                                <option value="Views ASC">Wyświetlenia ▲</option>
                                <option value="Likes DESC">Ocena ▼</option>
                                <option value="Likes ASC">Ocena ▲</option>
                            </select>
                            <h3>Kategoria:</h3>
                            <select name="category" id="category">
                                <option selected value="Dowolne">Dowolne</option>
                                <?php
                                foreach ($kategorie_tablica as $wynik) {
                                    $nazwa = $wynik['nazwa'];
                                    $id = $wynik['id'];
                                    echo '<option value="'.$id.'">'.$nazwa.'</option>';
                                }
                                ?>
                            </select>
                            <h3>Data przesłania:</h3>
                            <select name="date" id="date">
                                <option selected value="Dowolne">Dowolne</option>
                                <option value="Dzisiaj">Dzisiaj</option>
                                <option value="Ostatni tydzień">Ostatni tydzień</option>
                                <option value="Ostatni miesiąc">Ostatni miesiąc</option>
                                <option value="Ostatni rok">Ostatni rok</option>
                            </select>
                            <h3>Chronione hasłem:</h3>
                            <select name="password" id="password">
                                <option selected value="Dowolne">Dowolne</option>
                                <option value="Tak">Tak</option>
                                <option value="Nie">Nie</option>
                            </select>
                            <button type="submit" class="button_1">Zastosuj</button>
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
        <script>

            $( document ).ready(function() {
                var $sortowanie = '<?php echo $sortowanie; ?>';
                var $data = '<?php echo $data_filter; ?>';
                var $kategoria = '<?php echo $kategoria; ?>';
                var $haslo = '<?php echo $haslo_filter; ?>';

                $("#sort").val($sortowanie);
                $("#date").val($data);
                $("#category").val($kategoria);
                $("#password").val($haslo);
            });

        </script>
        <?php
        require_once("functions_end.php");
        ?>
    </body>
</html>

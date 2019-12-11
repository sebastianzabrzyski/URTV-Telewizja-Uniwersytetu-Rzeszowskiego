<!-- Strona z edycją danych materiału wideo -->

<?php

require_once("functions.php");
$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if(isset($_GET['id'])) {

    $id_filmu = $_GET['id'];
    sprawdzZalogowanie("","./login.php?return=edit_video.php?id={$id_filmu}");
    /*
  if($uprawnienia != "Administrator" && $uprawnienia != "Uploader") {
  przekierowanie("./index.php?message=1");
}

*/
    $polaczenie_BD = polaczDB();
    $id_filmu = $polaczenie_BD->real_escape_string($id_filmu);
    $zapytanie_SQL = "SELECT User_ID FROM movies WHERE ID = '{$id_filmu}';";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
    $wiersz = $wynik->fetch_row();
    $id_wlasciciela = $wiersz[0];

    if($id_wlasciciela == $id_uzytkownika) {

        $zapytanie_SQL = "SELECT movies.User_ID, Title, Views, Author, Filename, movies.Date, Describtion, movies.Password, Login FROM movies INNER JOIN users ON users.ID = movies.User_ID WHERE movies.ID = {$id_filmu};";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        $tytul = $opis = $wyswietlenia = $ocena_up = $ocena_down = $data_publikacji = $przesylajacy = $nazwa_pliku = null;
        $tagi_tablica = array();

        if ($wynik->num_rows > 0) {
            // output data of each row
            while($wiersz = $wynik->fetch_assoc()) {

                $nazwa_pliku = $wiersz["Filename"];
                $haslo = $wiersz["Password"];
                if($haslo != "") $haslo = "************";
                $tytul = $wiersz["Title"];
                $opis = $wiersz["Describtion"];
                $autor = $wiersz["Author"];
                break;
            }

            $zapytanie_SQL = "SELECT Name FROM tags WHERE Movie_ID = {$id_filmu};";
            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

            if ($wynik->num_rows > 0) {
                // output data of each row
                while($wiersz = $wynik->fetch_assoc()) {
                    $tag = $wiersz["Name"];
                    array_push($tagi_tablica, $tag);
                }
            }

            $tagi = implode(",", $tagi_tablica);
            $menu_1 = "Moje konto";
            $menu_2 = "Wyloguj się";
            $link_1 = "./account.php";
            $link_2 = "./logout.php";

        } else {
            przekierowanie("./index.php");
        }

    } else {
        pokazKomunikat("Nie jesteś właścicielem filmu");
        przekierowanie("./index.php");
    }
} else {
    przekierowanie("./index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'update_video') {

    $id_filmu = $_POST['movie_id'];
    $tytul = $_POST['title'];
    $tagi_nowe = $_POST['tags'];
    $kategorie = $_POST['category'];
    $autor = $_POST['author'];
    $haslo = $_POST['password'];
    $aktualizacja_hasla = "";
    if($haslo == null || $haslo == "") {
        $haslo = "NULL";
        $aktualizacja_hasla = "Password = NULL,";
    } else if ($haslo == "************") {

        $aktualizacja_hasla = "";
    } else {

        $haslo_bcrypt = password_hash($haslo, PASSWORD_BCRYPT);
        $aktualizacja_hasla = "Password = '{$haslo_bcrypt}',";
    }

    $opis = $_POST['describtion'];
    $polaczenie_BD = polaczDB();

    //dodawanie filmu do bazy

    $tytul = $polaczenie_BD->real_escape_string($tytul);
    $autor = $polaczenie_BD->real_escape_string($autor);
    $opis = $polaczenie_BD->real_escape_string(nl2br($opis));
    $zapytanie_SQL = "UPDATE movies SET
  Title = '{$tytul}',
  Author = '{$autor}',
  {$aktualizacja_hasla}
  Describtion = '{$opis}'
  WHERE ID = {$id_filmu}";

    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if($wynik === TRUE) {

        $zapytanie_SQL = "DELETE FROM categories_videos WHERE Movie_ID = '{$id_filmu}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

        foreach($kategorie as $kategoria) {
            $kategoria = $polaczenie_BD->real_escape_string($kategoria);
            $zapytanie_SQL = "INSERT INTO categories_videos (Stream_ID, Movie_ID, Category_ID)
      VALUES (NULL, {$id_filmu}, '{$kategoria}')";
            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        }


        //dodawanie miniaturki
        $plik_docelowy = "miniatures/".$nazwa_pliku.".jpeg";

        try {

            if($_FILES['miniature']['size'] > 0) {

                $format = strtolower(pathinfo(basename($_FILES["miniature"]["name"]),PATHINFO_EXTENSION));

                if($format == "png") {
                    imagejpeg(imagecreatefrompng($_FILES['miniature']['tmp_name']), $plik_docelowy, 100);
                }

                if($format == "gif") {
                    imagejpeg(imagecreatefromgif($_FILES['miniature']['tmp_name']), $plik_docelowy, 100);
                }

                if($format == "jpg" || $format == "jpeg") {
                    move_uploaded_file($_FILES["miniature"]["tmp_name"], $plik_docelowy);
                }
            }
        } catch (Exception $e) {
        }
        //dodawanie miniaturki
        //dodawanie tagów do bazy
        if ($tagi_nowe == "") {

            $zapytanie_SQL = "DELETE FROM tags WHERE Movie_ID = '{$id_filmu}';";

            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        } else {

            $tagi_tablica_nowa = explode(",", $tagi_nowe);
            foreach ($tagi_tablica_nowa as $tag_nowy) {

                if (!in_array($tag_nowy, $tagi_tablica)) {
                    $tag_nowy = $polaczenie_BD->real_escape_string($tag_nowy);
                    $zapytanie_SQL = "INSERT INTO tags (Name, Movie_ID)
          VALUES ('{$tag_nowy}', {$id_filmu})";

                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                }
            }

            foreach ($tagi_tablica as $tag_stary) {

                if (!in_array($tag_stary, $tagi_tablica_nowa)) {

                    $zapytanie_SQL = "DELETE FROM tags WHERE Name = '{$tag_stary}';";
                    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
                }
            }
        }
        //dodawanie tagów do bazy
        pokazKomunikat("Dane filmu zostały zaktualizowane");
        przekierowanie("./account.php");
    } else {
        pokazKomunikat("Wystąpił błąd podczas aktualizowania danych");
    }
    //dodawanie filmu do bazy
    //przekierowanie($adres_obecny);
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


$kategorie_filmu_ids = array();
$zapytanie_SQL = "SELECT Category_ID FROM categories_videos WHERE Movie_ID = '{$id_filmu}';";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {
    // output data of each row
    while($wiersz = $wynik->fetch_assoc()) {

        array_push($kategorie_filmu_ids,$wiersz["Category_ID"]);

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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Edycja wideo</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="./js/walidacja_edycja_wideo.js"></script>
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
                    <li>Edycja wideo</li>
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
                <div class="naglowek_formularza"><h1>Dodawanie wideo</h1></div>
                <div class="tlo">
                    <div class="blok_formularza formularz_edycja_wideo blok_padding_gorny blok_padding_dolny">
                        <form id="update_video" name="update_video" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id_filmu; ?>" onsubmit="return Validateupdate_video()">
                            <input type="hidden" name="form_name" value="update_video">
                            <input type="hidden" name="movie_id" value="<?php echo $id_filmu; ?>">
                            <div class="pole">
                                <input type="file" accept=".gif,.jpeg,.jpg,.png" name="miniature" id="miniature" placeholder="[opcjonalne]">
                                <p>Miniaturka:</p>
                            </div>
                            <div class="pole">
                                <input type="text" class="centrowanie" name="title" id="title" value="<?php echo $tytul; ?>" placeholder="">
                                <p>Tytuł:</p>
                            </div>
                            <div class="pole">
                                <input type="text" class="centrowanie" name="tags" id="tags" value="<?php echo $tagi; ?>" placeholder="[opcjonalne, oddzielone przecinkami]">
                                <p>Tagi:</p>
                            </div>
                            <div class="pole">
                                <input type="text" class="centrowanie" name="author" id="author" value="<?php echo $autor; ?>">
                                <p>Autor:</p>
                            </div>
                            <div class="pole">
                                <select name="category[]" class="centrowanie" multiple id="category">

                                    <?php
                                    foreach ($kategorie_tablica as $wynik) {
                                        $nazwa = $wynik['nazwa'];
                                        $id = $wynik['id'];

                                        if (in_array($wynik['id'], $kategorie_filmu_ids)) {
                                            echo '<option selected value="'.$id.'">'.$nazwa.'</option>';
                                        } else {
                                            echo '<option value="'.$id.'">'.$nazwa.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <p>Kategorie:</p>
                            </div>
                            <div class="pole">
                                <input type="password" class="centrowanie" name="password" id="password" value="<?php echo $haslo; ?>" placeholder="[opcjonalne]">
                                <p>Hasło:</p>
                            </div>
                            <div class="pole">
                                <textarea name="describtion" id="describtion" rows=10><?php echo $opis; ?></textarea>
                                <p>Opis:</p>
                            </div>
                            <div class="pole ostatnie_pole">
                                <button type="submit" class="button_1">Zatwierdź zmiany</button>
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

        <script>
            $( document ).ready(function() {
                var $uprawnienia = '<?php echo $uprawnienia_stare; ?>';
                $("#privileges").val($uprawnienia);
            });
        </script>
        <?php
        require_once("functions_end.php");
        ?>
    </body>
</html>

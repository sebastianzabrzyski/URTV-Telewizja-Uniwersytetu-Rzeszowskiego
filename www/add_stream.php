<?php

require_once("functions.php");
sprawdzZalogowanie("","./login.php?return=add_stream.php");

if($uprawnienia != "Administrator" && $uprawnienia != "Uploader") {
    pokazKomunikat("Nie posiadasz odpowiednich uprawnień");
    przekierowanie("./index.php");
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


$adres = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$polaczenie_BD = polaczDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'update_stream')
{

    $data = date("Y-m-d G:i:s");
    $tytul = $_POST['title'];
    $kategorie = $_POST['category'];
    $haslo = $_POST['password'];
    $dzien_planowania = $_POST['date'];
    $godzina_planowania = $_POST['time'];
    $autor = $_POST['author'];
    $data_planowania = $dzien_planowania." ".$godzina_planowania;
    $data_planowania = date("Y-m-d H:i:s", strtotime($data_planowania));
    if($haslo == null || $haslo == "") {
        $haslo = "NULL";
    } else {

        $haslo_bcrypt = password_hash($haslo, PASSWORD_BCRYPT);
        $haslo = "'{$haslo_bcrypt}'";
    }
    $opis = $_POST['describtion'];
    $uploadOk = 1;

    //dodawanie filmu do bazy

    $new_key = mt_rand(1000000000, 9999999999);
    $nazwa_pliku = bin2hex(random_bytes(16));
    $tytul = $polaczenie_BD->real_escape_string($tytul);
    $opis = $polaczenie_BD->real_escape_string(nl2br($opis));
    $data_planowania = $polaczenie_BD->real_escape_string($data_planowania);
    $autor = $polaczenie_BD->real_escape_string($autor);

    $zapytanie_SQL = "INSERT INTO streams (User_ID, Title, Views, Date, Describtion, Password, Streamkey_last, Streamkey_active, Filename, Planned_date, Author)
  VALUES ('{$id_uzytkownika}', '{$tytul}',0, '{$data}','{$opis}',{$haslo}, NULL, '{$new_key}', '{$nazwa_pliku}', '{$data_planowania}', '{$autor}') ON DUPLICATE KEY UPDATE
  Title='{$tytul}', Date='{$data}', Author='{$autor}', Describtion='{$opis}', Password={$haslo}, Planned_date='{$data_planowania}', Views = 0;";

    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
    if($wynik === TRUE) {

        try {
            $id_streamu = $polaczenie_BD->insert_id;
        } catch (Exception $e) {

        }

        $zapytanie_SQL = "DELETE FROM categories_videos WHERE Stream_ID = '{$id_streamu}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

        foreach($kategorie as $kategoria) {
            $kategoria = $polaczenie_BD->real_escape_string($kategoria);
            $zapytanie_SQL = "INSERT INTO categories_videos (Movie_ID, Stream_ID, Category_ID)
      VALUES (NULL, {$id_streamu}, '{$kategoria}')";
            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
        }

        pokazKomunikat("Dane transmisji zostały zapisane");
        $id_streamu = $polaczenie_BD->insert_id;

        $zapytanie_SQL = "DELETE comments_streams, likes_streams FROM comments_streams LEFT JOIN likes_streams ON comments_streams.Stream_ID = likes_streams.Stream_ID WHERE comments_streams.Stream_ID = '{$id_streamu}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

        //dodawanie miniaturki

        $plik_docelowy = "miniatures_streams/".$nazwa_pliku.".jpeg";

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

        if (!file_exists($plik_docelowy)) {


            copy("images/no_miniature.jpeg", $plik_docelowy);


        }

        //dodawanie miniaturki

    } else {
        pokazKomunikat("Wystąpił błąd podczas zapisywania danych");
    }

    //dodawanie filmu do bazy

    przekierowanie($adres_obecny);

}

//abs

$tytul = $opis = $streamkey_db = null;

$dzien_pokazywany = date('d.m.Y');
$godzina_pokazywana = date('H:i');

$zapytanie_SQL = "SELECT Name, Surname FROM users WHERE ID = {$id_uzytkownika};";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {
    // output data of each row
    while($wiersz = $wynik->fetch_assoc()) {
        $imie = $wiersz["Name"];
        $nazwisko = $wiersz["Surname"];
        $autor = $imie." ".$nazwisko;
        break;
    }
}

try {
    $id_uzytkownika = $polaczenie_BD->real_escape_string($id_uzytkownika);
    $zapytanie_SQL = "SELECT ID FROM streams WHERE User_ID = {$id_uzytkownika};";
    $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

    if ($wynik->num_rows > 0) {
        // output data of each row
        while($wiersz = $wynik->fetch_assoc()) {

            $id_streamu = $wiersz["ID"];
            break;
        }

        $zapytanie_SQL = "SELECT streams.User_ID, Title, streams.Date, Planned_date, Author, Describtion, Streamkey_active, Streamkey_last, streams.Password FROM streams INNER JOIN users ON users.ID = streams.User_ID WHERE streams.ID = {$id_streamu};";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

        if ($wynik->num_rows > 0) {
            // output data of each row
            while($wiersz = $wynik->fetch_assoc()) {

                $haslo = $wiersz["Password"];
                if($haslo != "") $haslo = "************";
                $tytul = $wiersz["Title"];
                $opis = $wiersz["Describtion"];
                $autor = $wiersz["Author"];
                $streamkey_db = $wiersz["Streamkey_active"];
                $streamkey_last_db = $wiersz["Streamkey_last"];
                $data_planowania = $wiersz["Planned_date"];
                if($data_planowania != null) {
                    $dzien_pokazywany = date('d.m.Y', strtotime($data_planowania));
                    $godzina_pokazywana = date('H:i', strtotime($data_planowania));
                }

                $streamy_sciezka = "/usr/local/antmedia/webapps/LiveApp/streams";

                if($streamkey_db == $streamkey_last_db) {


                    if (!file_exists("{$streamy_sciezka}/{$streamkey_db}.m3u8")) {

                        $new_key = mt_rand(1000000000, 9999999999);
                        $zapytanie_SQL = "UPDATE streams SET Streamkey_active = {$new_key}, Streamkey_last = NULL WHERE Streamkey_active = {$streamkey_db};";

                        $wynik2 = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

                        if($wynik2 === true) {
                            $streamkey_db = $new_key;
                        }
                    }
                }
                break;
            }
        }
    }

    //abs

} catch (Exception $e) {
}

// pobieranie lista_kategorii

$kategorie_tablica = array();
$zapytanie_SQL = "SELECT ID, Name
FROM categories;";

$polaczenie_BD = polaczDB();

$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {
    // output data of each row
    while($wiersz = $wynik->fetch_assoc()) {
        $nazwa = $wiersz["Name"];
        $id = $wiersz["ID"];

        array_push($kategorie_tablica,array('nazwa' => $nazwa,'id' => $id));

    }
}


$kategorie_streamu_ids = array();
$zapytanie_SQL = "SELECT Category_ID FROM categories_videos WHERE Stream_ID = '{$id_streamu}';";
$wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);

if ($wynik->num_rows > 0) {
    // output data of each row
    while($wiersz = $wynik->fetch_assoc()) {

        array_push($kategorie_streamu_ids,$wiersz["Category_ID"]);

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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Dodawanie transmisji</title>
        <link rel="stylesheet" href="./css/style.css">
        <link href="./css/jquery-ui.min.css" rel="stylesheet">
        <script src="./js/jquery-3.3.1.min.js"></script>
        <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
        <script src="./js/webrtc_adaptor.js"></script>
        <script src="./js/walidacja_dodawanie_transmisji.js"></script>
        <script src="/js/jquery-ui.min.js"></script>
        <script src="/js/jquery.ui.datepicker-pl.js"></script>
        <script src="/js/kalendarz.js"></script>
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
                    <li>Dodawanie transmisji</li>
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
                <div class="naglowek_formularza"><h1>Ustawienia transmisji</h1></div>
                <div id="blok_ustawienia_transmisji" class="blok_odstep_dolny">
                    <div class="tlo">
                        <div class="blok_formularza formularz_dodawanie_transmisji blok_padding_gorny blok_padding_dolny">
                            <form id="update_stream" name="update_stream" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return Validateupdate_stream()">
                                <input type="hidden" name="form_name" value="update_stream">
                                <div class="pole">
                                    <input type="file" accept=".gif,.jpeg,.jpg,.png" name="miniature" id="miniature" placeholder="[opcjonalne]">
                                    <p>Miniaturka:</p>
                                </div>
                                <div class="pole">
                                    <input type="text" class="centrowanie" name="title" id="title" value="<?php echo $tytul; ?>" placeholder="">
                                    <p>Tytuł:</p>
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

                                            if (in_array($wynik['id'], $kategorie_streamu_ids)) {
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
                                <div class="pole">
                                    <input id="DatePicker1_input" name="DatePicker1" type="text" value="">
                                    <div id="DatePicker1">
                                    </div>
                                    <p>Zaplanuj:</p>
                                </div>
                                <div class="pole planowanie">
                                    <select name="time" class="centrowanie" id="time">
                                        <option selected value="<?php echo $godzina_pokazywana; ?>"><?php echo $godzina_pokazywana; ?></option>
                                        <option value="07:00">07:00</option>
                                        <option value="07:15">07:15</option>
                                        <option value="07:30">07:30</option>
                                        <option value="07:45">07:45</option>
                                        <option value="08:00">08:00</option>
                                        <option value="08:15">08:15</option>
                                        <option value="08:30">08:30</option>
                                        <option value="08:45">08:45</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:15">09:15</option>
                                        <option value="09:30">09:30</option>
                                        <option value="09:45">09:45</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:15">10:15</option>
                                        <option value="10:30">10:30</option>
                                        <option value="10:45">10:45</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:15">11:15</option>
                                        <option value="11:30">11:30</option>
                                        <option value="11:45">11:45</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:15">12:15</option>
                                        <option value="12:30">12:30</option>
                                        <option value="12:45">13:45</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:15">13:15</option>
                                        <option value="13:30">13:30</option>
                                        <option value="13:45">13:45</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:15">14:15</option>
                                        <option value="14:30">14:30</option>
                                        <option value="14:45">14:45</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:15">15:15</option>
                                        <option value="15:30">15:30</option>
                                        <option value="15:45">15:45</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:15">16:15</option>
                                        <option value="16:30">16:30</option>
                                        <option value="16:45">16:45</option>
                                        <option value="17:00">17:00</option>
                                        <option value="17:15">17:15</option>
                                        <option value="17:30">17:30</option>
                                        <option value="17:45">17:45</option>
                                        <option value="18:00">18:00</option>
                                        <option value="18:15">18:15</option>
                                        <option value="18:30">18:30</option>
                                        <option value="18:45">18:45</option>
                                        <option value="19:00">19:00</option>
                                        <option value="19:15">19:15</option>
                                        <option value="19:30">19:30</option>
                                        <option value="19:45">19:45</option>
                                        <option value="20:00">20:00</option>
                                    </select>
                                    <input type="text" class="centrowanie readonly" name="date" id="date" value="<?php echo $dzien_pokazywany; ?>" readonly>
                                </div>

                                <div class="pole ostatnie_pole">
                                    <button type="submit" class="button_1">Zapisz</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="naglowek_formularza"><h1>Transmisja przez RTMP</h1></div>
                <div id="blok_transmisja_rtmp">
                    <div class="tlo">
                        <div class="blok_formularza formularz_rtmp blok_padding_gorny blok_padding_dolny">
                            <div class="pole">
                                <input type="text" class="readonly centrowanie" id="rtmp_address" value="rtmp://<?php echo $adres_serwera; ?>/LiveApp/" placeholder="" readonly>
                                <p class="desktop">Adres serwera:</p>
                                <p class="mobile">Adres:</p>
                            </div>
                            <div class="pole">
                                <img src="images/refresh.png" id="obrazek_odswiezenia_klucza" alt="odśwież klucz" onclick="odswiezKlucz();return false;">
                                <input type="text" class="readonly centrowanie" id="rtmp_key" value="<?php echo $streamkey_db; ?>" placeholder="" readonly>
                                <p class="desktop">Nazwa transmisji:</p>
                                <p class="mobile">Nazwa:</p>
                            </div>
                            <div class="pole ostatnie_pole">
                                <a href="https://www.xsplit.com/broadcaster" target="_blank"><button type="submit" class="button_1">Pobierz XSplit Broadcaster</button></a>
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

        <script src="./js/dodawanie_transmisji.js"></script>
        <?php
        require_once("functions_end.php");
        ?>
    </body>
</html>

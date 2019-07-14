<?php

require_once("functions.php");
ignore_user_abort(true);
sprawdzZalogowanie("","./login.php?return=add_video.php");
set_time_limit(0);
/*
if($privileges != "Administrator" && $privileges != "Uploader") {
przekierowanie("./index.php?message=1");
}
*/

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


$wykorzystane = 0;
$do_wykorzystania = 0;
$conn = polaczDB();
$user_id = $conn->real_escape_string($user_id);
$query = "SELECT SUM(Size) AS Wykorzystane FROM movies WHERE User_ID = {$user_id};";
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $wykorzystane = $row["Wykorzystane"];
    break;
  }
}
$user_id = $conn->real_escape_string($user_id);
$query = "SELECT Space, Name, Surname FROM users WHERE ID = {$user_id};";
$result = queryDB($conn,$query);


if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $do_wykorzystania = $row["Space"];
    $pozostalo_miejsca = $do_wykorzystania - $wykorzystane;
    $imie = $row["Name"];
    $nazwisko = $row["Surname"];
    $imie_nazwisko = $imie." ".$nazwisko;
    break;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'upload_video')
{

  $data = date("Y-m-d G:i:s");
  $tytul = $_POST['title'];
  $tagi = $_POST['tags'];
  $autor = $_POST['author'];
  $kategorie = $_POST['category'];
  $haslo = $_POST['password'];
  if($haslo == null || $haslo == "") {
    $haslo = "NULL";
  } else {

    $haslo_bcrypt = password_hash($haslo, PASSWORD_BCRYPT);
    $haslo = "'{$haslo_bcrypt}'";
  }

  $opis = $_POST['describtion'];
  $rozmiar_mb = number_format($_FILES["movie"]["size"]  / 1048576, 2);
  $rozmiar_miniaturki_mb = number_format($_FILES["miniature"]["size"]  / 1048576, 2);
  if($rozmiar_mb < 1) $rozmiar_mb = 1;
  if($rozmiar_miniaturki_mb < 1) $rozmiar_miniaturki_mb = 1;
  $uploadOk = 1;

  $format = strtolower(pathinfo(basename($_FILES["movie"]["name"]),PATHINFO_EXTENSION));

  if(  $format != "mp4" &&   $format != "3gp" && $format != "avi" &&
  $format != "flv" &&  $format != "mov" && $format != "mpeg" &&
  $format != "mpg" &&  $format != "rmvb" && $format != "wmv" ) {

    pokazKomunikat("Wysłano plik w nieprawidłowym formacie");
    $uploadOk = 0;
  } else {

    if ($rozmiar_mb > $pozostalo_miejsca) {
      pokazKomunikat("Rozmiar filmu przekracza dostępne miejsce na dysku");
      $uploadOk = 0;
    } elseif ($rozmiar_miniaturki_mb > 5) {
      pokazKomunikat("Rozmiar miniaturki nie może przekraczać 5MB");
      $uploadOk = 0;
    } else {

      if ($uploadOk == 0) {
        pokazKomunikat("Wystąpił błąd podczas przesyłania filmu");
      } else {

        //dodawanie filmu do bazy


        if($privileges == "Użytkownik") {
          $verified = "Nie";
        } else {
          $verified = "Tak";
        }
        $nazwa_pliku = bin2hex(random_bytes(16));
        $target_file = "videos/".$nazwa_pliku.".mp4";
        $source_file = $_FILES["movie"]["tmp_name"];
        $przekonwertowane = 0;

        //konwersja filmu

        $media_info = exec("/var/www/html/ffmpeg/ffprobe -show_streams {$source_file}",$output,$result);
        $streams_info = implode(" ",$output);
        $dobry_kodek = 0;
        if (strpos($streams_info, 'codec_name=h264') === false) {
        } else {
          $dobry_kodek = 1;
        }

        if($format != "mp4" || $dobry_kodek == 0) {

          try {
            require_once("ffmpeg/FFmpeg.php");
            $key = 'max_muxing_queue_size';
            $value = '9999';
            //$key2 = 's';
            //$value2 = '872x490';
            $FFmpeg = new FFmpeg($ffmpeg_path);
            $FFmpeg->input( "{$source_file}" )->set($key,$value)->output( "{$target_file}" );
            $FFmpeg->ready();
            $komenda = $FFmpeg->command;
          } catch (Exception $e) {
          }

          if (!file_exists($target_file)) {
            $uploadOk = 0;
          } else {
            $przekonwertowane = 1;
          }
        }

        //konwersja filmu

        if ($przekonwertowane == 1 || ($uploadOk == 1 && move_uploaded_file($source_file, $target_file))) {

          $tytul = $conn->real_escape_string($tytul);

          $autor = $conn->real_escape_string($autor);
          $opis = $conn->real_escape_string(nl2br($opis));

          $query = "INSERT INTO movies (User_ID, Title, Views, Size, Date, Describtion, Password, Verified, Filename, Author)
          VALUES ('{$user_id}', '{$tytul}',0, '{$rozmiar_mb}', '{$data}','{$opis}',{$haslo}, '{$verified}', '{$nazwa_pliku}','{$autor}')";

          $result = queryDB($conn,$query);
          if($result === TRUE) {
            $id_filmu = $conn->insert_id;
            if($privileges == "Użytkownik") {
              pokazKomunikat("Film oczekuje na zatwierdzenie przez administratora");
            } else {
              pokazKomunikat("Film został dodany");
            }

            foreach($kategorie as $kategoria) {

              $kategoria = $conn->real_escape_string($kategoria);
              $query = "INSERT INTO categories_videos (Stream_ID, Movie_ID, Category_ID)
              VALUES (NULL, {$id_filmu}, '{$kategoria}')";
              $result = queryDB($conn,$query);
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

            if (!file_exists($plik_docelowy)) {

              try {

                require_once("ffmpeg/FFmpeg.php");

                $FFmpeg = new FFmpeg($ffmpeg_path);
                $size = '872x491';
                $start = 1;
                $frames = 1;
                $FFmpeg->input( "{$target_file}" )->thumb( $size , $start, $frames )->output( "{$plik_docelowy}" )->ready();

              } catch (Exception $e) {
              }

              if (!file_exists($plik_docelowy)) {
                copy("images/no_miniature.jpeg", $plik_docelowy);
              }
            }

            //dodawanie miniaturki
            //dodawanie tagów do bazy

            if ($tagi != "") {

              $tagi_tablica = explode(",", $tagi);

              foreach ($tagi_tablica as $tag) {
                $tag = trim($tag);
                $tag = $conn->real_escape_string($tag);
                $query = "INSERT INTO tags (Name, Movie_ID)
                VALUES ('{$tag}', {$id_filmu})";
                $result = queryDB($conn,$query);
                if($result === TRUE) {

                } else {
                }
              }
            }
            //dodawanie tagów do bazy

            //powiadomienie newsletter

            try {

              $temat = "W serwisie UR TV pojawił się nowy film";
              $adres = "http://$_SERVER[HTTP_HOST]/video.php?id={$id_filmu}";

              // subskrybenci
              $user_id = $conn->real_escape_string($user_id);
              $query = "SELECT DISTINCT User_ID FROM subscription WHERE (Author_ID = {$user_id} OR Author_ID = 0) AND NOT Author_ID = -1;";
              $result = queryDB($conn,$query);
              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                  $id_subskrybenta = $row["User_ID"];

                  $query2 = "SELECT DISTINCT User_ID FROM subscription WHERE User_ID = {$id_subskrybenta} AND Author_ID = -1;";
                  $result2 = queryDB($conn,$query2);
                  if ($result2->num_rows > 0) {
                    continue;
                  }

                    $rezygnacja = "edytuj ustawienia newslettera na stronie swojego konta.";
                    $query2 = "SELECT Email FROM users WHERE ID = {$id_subskrybenta};";
                    $result2 = queryDB($conn,$query2);
                    if ($result2->num_rows > 0) {
                      while($row2 = $result2->fetch_assoc()) {
                        $email = $row2["Email"];
                        break;
                      }
                    }

                  $wiadomosc = "Witaj,\n\nw serwisie TV UR pojawił się nowy film pod tytułem \"{$tytul}\", przesłany przez użytkownika {$username}.\n\nMożesz go obejrzeć na stronie: {$adres}\n\nJeżeli nie chcesz otrzymywać kolejnych powiadomień, {$rezygnacja}\n\nPozdrawiamy!";
                  wyslijEmail($email,$temat,$wiadomosc,"noreply@urtv.tk","TV UR");
                }
              }

              // newsletter
              $query = "SELECT DISTINCT Email FROM newsletter;";
              $result = queryDB($conn,$query);
              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {

                  $email = $row["Email"];
                  $email_md5 = md5($email);
                  $rezygnacja_link = "http://$_SERVER[HTTP_HOST]/unsubscribe.php?mail={$email_md5}";
                  $rezygnacja = "kliknij w link: ".$rezygnacja_link;
                  $wiadomosc = "Witaj, \n\nW serwisie TV UR pojawił się nowy film pod tytułem \"{$tytul}\", przesłany przez użytkownika {$username}.\n\nMożesz go obejrzeć na stronie: {$adres}\n\nJeżeli nie chcesz otrzymywać kolejnych powiadomień, {$rezygnacja}\n\nPozdrawiamy!";
                  wyslijEmail($email,$temat,$wiadomosc,"noreply@urtv.tk","UR TV");
                }
              }


            } catch (Exception $e) {
            }

            //powiadomienie newsletter

          } else {
            pokazKomunikat("Wystąpił błąd podczas dodawania filmu do bazy");
            if (file_exists($target_file)) {
              unlink($target_file);
            }
          }
        } else {
          pokazKomunikat("Wystąpił błąd podczas dodawania filmu");
        }
        //dodawanie filmu do bazy
      }
    }
  }
  przekierowanie($adres_obecny);
}

// pobieranie lista_kategorii

$kategorie_tablica = array();
$query = "SELECT ID, Name
FROM categories;";

$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $nazwa = $row["Name"];
    $id = $row["ID"];
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
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="msapplication-config" content="/favicons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">
  <link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
  <link rel="manifest" href="/favicons/site.webmanifest">
  <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="shortcut icon" href="/favicons/favicon.ico">
  <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Dodawanie wideo</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-3.3.1.min.js"></script>
  <script src="./js/walidacja_dodawanie_wideo.js"></script>
  <script>
  var do_wykorzystania = "<?php echo $pozostalo_miejsca; ?>";
  </script>
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
        <li>Dodawanie wideo</li>
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
        <div class="blok_formularza formularz_dodawanie_wideo blok_padding_gorny blok_padding_dolny">
          <form id="upload_video" name="upload_video" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return Validateupload_video()">
            <input type="hidden" name="form_name" value="upload_video">
            <h3 class="flex">Prosimy o przesłanie filmu w formacie MP4 z kodekiem H.264 (nie wymaga konwersji)</h3>
            <div class="pole">
              <input type="file" accept=".3gp,.avi,.flv,.mov,.mp4,.mpeg,.mpg,.rmvb,.wmv" name="movie" id="movie">
              <p>Plik wideo:</p>
            </div>
            <div class="pole">
              <input type="file" accept=".gif,.jpeg,.jpg,.png" name="miniature" id="miniature" placeholder="[opcjonalne]">
              <p>Miniaturka:</p>
            </div>
            <div class="pole">
              <input type="text" class="centrowanie" name="title" id="title" placeholder="">
              <p>Tytuł:</p>
            </div>
            <div class="pole">
              <input type="text" class="centrowanie" name="tags" id="tags" placeholder="[opcjonalne, oddzielone przecinkami]">
              <p>Tagi:</p>
            </div>
            <div class="pole">
              <input type="text" class="centrowanie" name="author" id="author" value="<?php echo $imie_nazwisko; ?>">
              <p>Autor:</p>
            </div>
            <div class="pole">
              <select class="centrowanie" multiple name="category[]" id="category">
                <?php
                foreach ($kategorie_tablica as $wynik) {
                  $nazwa = $wynik['nazwa'];
                  $id = $wynik['id'];
                  echo '<option value="'.$id.'">'.$nazwa.'</option>';
                }
                ?>
              </select>
              <p>Kategorie:</p>
            </div>
            <div class="pole">
              <input type="password" class="centrowanie" name="password" id="password" placeholder="[opcjonalne]">
              <p>Hasło:</p>
            </div>
            <div class="pole">
              <textarea name="describtion" id="describtion" rows=10></textarea>
              <p>Opis:</p>
            </div>
            <div class="pole ostatnie_pole">
              <button type="submit" class="button_1">Dodaj wideo</button>
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

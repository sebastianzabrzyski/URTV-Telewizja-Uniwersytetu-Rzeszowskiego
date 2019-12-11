<!-- Skrypt z często używanymi funkcjami -->

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//error_reporting(0);
date_default_timezone_set('Europe/Warsaw');
$aktualna_data = date("d-m-Y - h:i:s");
$adres_serwera = $_SERVER['HTTP_HOST'];
$id_uzytkownika = null;
$nazwa_uzytkownika = null;
$uprawnienia = null;

$adres_obecny = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$srodowisko = "Linux";

if($srodowisko == "Windows") {
    $ffmpeg_sciezka = 	"ffmpeg_windows\bin\mpeg.exe";
} elseif($srodowisko == "Linux") {
    $ffmpeg_sciezka = 	"/var/www/html/ffmpeg/ffmpeg";
}

function pokazKomunikat($tresc_komunikatu) {
    $_SESSION["message"] = $tresc_komunikatu;
}

function polaczDB() {

    $servername = "localhost";
    $nazwa_bazy = "urtv";
    $nazwa_uzytkownika = "urtv";
    $haslo = "haslo_do_urtv#Q";

    $polaczenie_BD = new mysqli($servername, $nazwa_uzytkownika, $haslo, $nazwa_bazy);
    mysqli_set_charset($polaczenie_BD, "utf8");

    if ($polaczenie_BD->connect_error) {
        exit("Błąd połączenia z bazą danych");
    } else {
        return $polaczenie_BD;
    }
}

function rozlaczDB($polaczenie_BD) {
    $polaczenie_BD->close();
}

function wykonajSQL($polaczenie_BD,$sql) {

    if (strpos($sql, 'INSERT') === 0) {

        if ($polaczenie_BD->query($sql) === TRUE) {
            return TRUE;
        } else {
            return $polaczenie_BD->error;
        }

    } else if (strpos($sql, 'SELECT') === 0) {

        $wynik = $polaczenie_BD->query($sql);
        return $wynik;

    } else if (strpos($sql, 'DELETE') === 0 || strpos($sql, 'UPDATE') === 0) {

        if ($polaczenie_BD->query($sql) === TRUE) {
            return TRUE;
        } else {
            return $polaczenie_BD->error;
        }
    } else {
        return "Bład";
    }
}

function sprawdzPoprawnosc($tekst, $znaki = "", $min_dlugosc = "", $max_dlugosc = "") {

    $punkty = 0;
    $cel = 0;

    if($znaki != "") $cel = $cel + 1;
    if($min_dlugosc != "") $cel = $cel + 1;
    if($max_dlugosc != "") $cel = $cel + 1;
    if($znaki != "") {
        if(preg_match('/^['.$znaki.']*$/', $tekst)) {
            $punkty = $punkty + 1;
        }
    }

    if($min_dlugosc != "") {

        $dlugosc = strlen($tekst);

        if($dlugosc >= $min_dlugosc) {
            $punkty = $punkty + 1;
        }

    }

    if($max_dlugosc != "") {

        $dlugosc = strlen($tekst);

        if($max_dlugosc == 0) $max_dlugosc = $dlugosc;

        if($dlugosc <= $max_dlugosc) {

            $punkty = $punkty + 1;
        }

    }

    if($punkty == $cel) {

        return "Poprawne";
    } else {
        return "Niepoprawne";
    }
}

function komunikat($tresc) {
    echo '<script>
	alert("'.$tresc.'");
	</script>';
}


function wyslijEmail($odbiorca,$temat,$tresc,$odpowiedz_do_adres = "kontakt@urtv.tk", $odpowiedz_do_nazwa = "UR TV") {

    try{

        date_default_timezone_set('Etc/UTC');
        require_once("phpmailer/PHPMailer.php");
        require_once("phpmailer/SMTP.php");
        require_once("phpmailer/Exception.php");
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAutoTLS = false;
        //$mail->Debugoutput = 'html';
        $mail->Host = "mail.urtv.tk";
        $mail->Port = 587;
        //$mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        //$mail->isHTML(true);
        $mail->Username = "kontakt@urtv.tk";
        $mail->Password = "adminpass";
        $mail->setFrom('kontakt@tv.ur.edu.pl', 'Telewizja Internetowa UR');
        $mail->addReplyTo($odpowiedz_do_adres, $odpowiedz_do_nazwa);
        $mail->addAddress($odbiorca, $odbiorca);
        $mail->Subject = $temat;
        $mail->Body = $tresc;
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
            $blad = $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    } catch (Exception $blad_maila) {
        $blad_emaila_tresc = $blad_maila->getMessage();
        return false;
    }
}

function przekierowanie($adres) {
    header('Location: '.$adres);
    exit;
}

function ustawSesje() {

    if (session_id() == "")
    {
        ini_set("session.cookie_httponly", True);
        session_start();
    }
}

function sprawdzZalogowanie($zalogowany = "",$nie_zalogowany = "") {

    global $nazwa_uzytkownika, $id_uzytkownika, $uprawnienia;
    ustawSesje();

    if (isset($_SESSION['username']))
    {

        $nazwa_uzytkownika = $_SESSION['username'];
        $id_uzytkownika = $_SESSION['user_id'];
        $uprawnienia = $_SESSION['privileges'];

        if($zalogowany != "") {

            przekierowanie($zalogowany);
        }
    } else {

        if($nie_zalogowany != "") {
            przekierowanie($nie_zalogowany);
        }
    }
}
?>

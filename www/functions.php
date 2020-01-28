<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//error_reporting(0);
date_default_timezone_set('Europe/Warsaw');
$aktualna_data = date("d-m-Y - h:i:s");
$adres_serwera = $_SERVER['HTTP_HOST'];
$user_id = null;
$username = null;
$privileges = null;

$adres_obecny = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$srodowisko = "Linux";

if($srodowisko == "Windows") {
	$ffmpeg_path = 	"ffmpeg_windows\bin\mpeg.exe";
} elseif($srodowisko == "Linux") {
	$ffmpeg_path = 	"/var/www/html/ffmpeg/ffmpeg";
}

function pokazKomunikat($tresc_komunikatu) {
	$_SESSION["message"] = $tresc_komunikatu;
}

function polaczDB() {

	$servername = "localhost";
	$dbname = "urtv";
	$username = "urtv";
	$password = "haslo_do_urtv#Q";

	$conn = new mysqli($servername, $username, $password, $dbname);
	mysqli_set_charset($conn, "utf8");

	if ($conn->connect_error) {
		exit("Błąd połączenia z bazą danych");
	} else {
		return $conn;
	}
}

function rozlaczDB($conn) {
	$conn->close();
}

function queryDB($conn,$sql) {

	if (strpos($sql, 'INSERT') === 0) {

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return $conn->error;
		}

	} else if (strpos($sql, 'SELECT') === 0) {

		$result = $conn->query($sql);
		return $result;

	} else if (strpos($sql, 'DELETE') === 0 || strpos($sql, 'UPDATE') === 0) {

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return $conn->error;
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
	} catch (Exception $mailerror) {
		$mail_err_mess = $mailerror->getMessage();
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

	global $username, $user_id, $privileges;
	ustawSesje();

	if (isset($_SESSION['username']))
	{

		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		$privileges = $_SESSION['privileges'];

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

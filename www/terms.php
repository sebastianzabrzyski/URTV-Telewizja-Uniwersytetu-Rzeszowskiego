<?php

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
        <title>Telewizja internetowa Uniwersytetu Rzeszowskiego - Regulamin</title>
        <link rel="stylesheet" href="./css/style.css">
        <script src="./js/jquery-3.3.1.min.js"></script>
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
                    <li>Regulamin</li>
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
                <div class="naglowek_formularza"><h1>Regulamin serwisu</h1></div>
                <div class="tlo">
                    <div class="blok_formularza formularz_kontakt blok_padding_gorny blok_padding_dolny">
                        <div id="o_serwisie">
                            <p>

                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis nulla orci. Sed lectus mauris, euismod sit amet suscipit nec, interdum sed justo. Ut vulputate luctus libero, nec euismod magna tempor sit amet. Vivamus non dolor in leo efficitur convallis. Suspendisse eleifend nisl eget velit lobortis consectetur. Sed eu mi ac tortor cursus tincidunt. Sed mollis mollis rhoncus. Etiam tincidunt ipsum vitae erat volutpat tincidunt. Nam tincidunt dictum nulla. Nulla odio justo, tristique vel fermentum eu, bibendum vel est. Vivamus a mauris eros. Nunc vitae semper quam, ac gravida leo. Praesent sagittis, tellus ut condimentum dictum, elit dolor commodo nisl, sed sagittis sem sapien et ante. Sed sed ultricies ligula. Maecenas orci erat, interdum ut placerat a, vestibulum id velit. Morbi sed orci dictum, aliquet risus vitae, luctus turpis.</br></br>

                    Nulla blandit porttitor ante ut fringilla. Maecenas sed tellus ultricies odio euismod vestibulum. Integer posuere metus iaculis leo maximus, et venenatis libero vestibulum. Quisque semper dignissim feugiat. Maecenas non felis semper, auctor justo non, condimentum sapien. Maecenas ac dui sem. Nulla est quam, fermentum pellentesque feugiat ac, fringilla sit amet ante. Etiam tempor fringilla auctor. Mauris fringilla efficitur mi, vel volutpat mauris placerat at. Curabitur auctor dolor non turpis semper, at dictum magna placerat. Morbi aliquet augue mauris, a ultricies lorem volutpat a. Vestibulum vitae ex quam. Fusce eu aliquam dui. Donec malesuada commodo felis consequat pharetra.</br></br>

            Cras id pulvinar nisi. Duis quis neque a quam rhoncus blandit. Aenean pharetra dui eros, id tempus orci consequat id. Donec quis nulla erat. Maecenas pellentesque nunc ac mauris elementum, sed viverra mi luctus. Curabitur semper sem nulla, convallis malesuada nisi accumsan eget. Phasellus urna diam, dictum ac magna at, tristique fermentum nisi. Quisque at sollicitudin erat. Fusce hendrerit, risus in posuere posuere, lorem tortor varius eros, id rutrum neque est id leo. Integer vulputate nulla in neque cursus ornare.</br></br>

    Praesent justo elit, imperdiet non tellus quis, maximus commodo enim. Proin eu auctor lectus, et sollicitudin arcu. Aliquam ultrices, nisl ullamcorper mattis posuere, tortor felis fermentum diam, a varius mi eros non ante. Curabitur sit amet tristique velit, nec accumsan orci. Morbi pellentesque sapien eu ipsum aliquam semper. Morbi mattis dictum fringilla. Vivamus euismod convallis consectetur. In in eros at ligula ultrices gravida. Etiam rutrum auctor molestie. Suspendisse nec turpis ut lectus pretium varius. Aenean auctor ultricies lacus quis viverra. Donec id nulla a tortor varius consequat quis a neque. Suspendisse potenti. Fusce consectetur, massa non rhoncus commodo, justo arcu ultricies sapien, ac scelerisque arcu lorem sed magna.</br></br>

Nam nec mauris et est ullamcorper accumsan. Praesent turpis sem, finibus at laoreet in, scelerisque quis nibh. Fusce eu lacinia diam. Mauris aliquet nisl vitae urna molestie, id vehicula massa tristique. Donec eget convallis massa, eu cursus sem. Nunc nunc ipsum, accumsan eget euismod nec, fringilla eget tellus. Curabitur molestie aliquet nulla, et ultrices dolor mattis eu. Praesent laoreet massa in nisl finibus, in elementum tellus semper. </p>
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

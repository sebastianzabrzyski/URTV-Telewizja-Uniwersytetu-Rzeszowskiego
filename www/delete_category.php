<!-- Skrypt usuwający kategorię materiałów filmowych -->

<?php

require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id_kategorii = $_GET['id'];
    $tryb = $_GET['tryb'];
    sprawdzZalogowanie("","./login.php?return=delete_category.php?tryb={$tryb}&id={$id_kategorii}");

    $polaczenie_BD = polaczDB();
    $id_kategorii = $polaczenie_BD->real_escape_string($id_kategorii);
    if($tryb == "kategoria") {
        $zapytanie_SQL_count = "SELECT count(*) FROM categories_videos WHERE Category_ID = '{$id_kategorii}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
        $wiersz = $wynik->fetch_row();
        $liczba_powiazanych= $wiersz[0];
    } else {
        $zapytanie_SQL_count = "SELECT count(*) FROM categories WHERE Group_ID = '{$id_kategorii}';";
        $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL_count);
        $wiersz = $wynik->fetch_row();
        $liczba_powiazanych = $wiersz[0];
    }

    if($liczba_powiazanych > 0) {

        if($tryb == "kategoria") {
            $wiadomosc = "Nie mozna usunąć kategorii, do której przypisane są materiały";
        } else {
            $wiadomosc = "Nie mozna usunąć grupy, do której przypisane są kategorie";
        }

        pokazKomunikat($wiadomosc);
        przekierowanie("./admin.php");

    } else {

        if($uprawnienia == "Administrator") {

            if($tryb == "kategoria") {
                $zapytanie_SQL = "DELETE FROM categories WHERE ID = '{$id_kategorii}';";
            } else {
                $zapytanie_SQL = "DELETE FROM categories_groups WHERE ID = '{$id_kategorii}';";
            }
            $wynik = wykonajSQL($polaczenie_BD,$zapytanie_SQL);
            if($polaczenie_BD->affected_rows > 0) {

                if($tryb == "kategoria") {
                    $wiadomosc = "Kategoria została usunięta";
                } else {
                    $wiadomosc = "Grupa została usunięta";
                }
                pokazKomunikat($wiadomosc);
                przekierowanie("./admin.php");
            } else {

                if($tryb == "kategoria") {
                    $wiadomosc = "Nie udało się usunąć kategorii";
                } else {
                    $wiadomosc = "Nie udało się usunąć grupy";
                }

                pokazKomunikat($wiadomosc);
                przekierowanie("./admin.php");

            }
        } else {
            pokazKomunikat("Nie posiadasz uprawnień administratora");
            przekierowanie("./index.php");
        }
    }

}
?>

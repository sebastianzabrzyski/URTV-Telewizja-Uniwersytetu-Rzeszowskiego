<!-- Skrypt z funkcjami wywoływanymi po zakończeniu wczytywania strony -->

<?php
if(isset($_SESSION['message']))  {
    komunikat($_SESSION['message']);
    unset($_SESSION['message']);
}
?>

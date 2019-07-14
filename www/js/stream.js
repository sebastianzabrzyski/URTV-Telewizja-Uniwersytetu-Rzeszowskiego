function wyczyscKomentarz() {

document.getElementById("pole_komentarza").value = "";
document.getElementById("pole_komentarza").focus();
}

function wyslijEmail() {

var adresStrony = document.URL;
var tytulFilmu = '<?php echo $tytul; ?>';
window.open('mailto:?subject='+tytulFilmu+'&body='+adresStrony);
}

function ustawPoleKomentarza(zalogowany) {

if(zalogowany == false) {
document.getElementById("pole_komentarza").placeholder = "Musisz się zalogować, aby dodać komentarz";
document.getElementById("pole_komentarza").disabled = true;
}


}

function potwierdzUsuniecieKomentarza(id)
{

var result = confirm("Na pewno chcesz usunąć ten komentarz?");
if (result) {
  window.location.href = "./delete_comment.php?tryb=stream&id=" + id;
  
}
}

function potwierdzUsuniecieFilmu(id)
{

var result = confirm("Na pewno chcesz usunąć tą transmisję?");
if (result) {
  window.location.href = "./delete_video.php?tryb=stream&id=" + id;
  
}
}
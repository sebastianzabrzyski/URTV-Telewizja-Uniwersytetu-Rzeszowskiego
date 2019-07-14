function potwierdzUsuniecieUzytkownika(id)
{
  var result = confirm("Na pewno chcesz usunąć tego użytkownika?");
  if (result) {
    window.location.href = "./delete_user.php?id=" + id;
  }
}


function potwierdzUsuniecieFilmu(id)
{
  var result = confirm("Na pewno chcesz usunąć ten film?");
  if (result) {
    window.location.href = "./delete_video.php?tryb=film&id=" + id;
  }
}


function potwierdzAkceptacjeFilmu(id)
{
  var result = confirm("Na pewno chcesz zaakceptować ten film?");
  if (result) {
    window.location.href = "./accept_video.php?id=" + id;
  }
}


function potwierdzUsuniecieKategorii(id)
{
  var result = confirm("Na pewno chcesz usunąć tą kategorię?");
  if (result) {
    window.location.href = "./delete_category.php?tryb=kategoria" + "&id=" + id;
  }
}

function potwierdzUsuniecieGrupy(id)
{
  var result = confirm("Na pewno chcesz usunąć tą grupę?");
  if (result) {
    window.location.href = "./delete_category.php?tryb=grupa" + "&id=" + id;
  }
}

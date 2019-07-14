  function potwierdzUsuniecieUzytkownika(id)
  {
    var result = confirm("Na pewno chcesz usunąć swoje konto?");
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

  if(ustawienia_newslettera == "no_any_ns") {
    document.getElementById("no_any_ns").checked = true;
  } else if (ustawienia_newslettera == "selected_ns") {
    document.getElementById("selected_ns").checked = true;
  } else {
    document.getElementById("all_ns").checked = true;
  }

function Validateadd_category()
{
   var regexp;
   var kategoria = document.getElementById('kategoria');
   if (!(kategoria.disabled || kategoria.style.display === 'none' || kategoria.style.visibility === 'hidden'))
   {
      if (kategoria.value == "")
      {
         alert("Nieprawidłowa nazwa kategorii");
         kategoria.focus();
         return false;
      }
      if (kategoria.value.length < 1)
      {
         alert("Nieprawidłowa nazwa kategorii");
         kategoria.focus();
         return false;
      }
      if (kategoria.value.length > 100)
      {
         alert("Zbyt długa nazwa kategorii");
         kategoria.focus();
         return false;
      }
   }
   var grupa = document.getElementById('grupa');
   if (!(grupa.disabled ||
         grupa.style.display === 'none' ||
         grupa.style.visibility === 'hidden'))
   {
      if (grupa.selectedIndex < 0)
      {
         alert("Niepoprawna grupa");
         grupa.focus();
         return false;
      }
   }
   return true;
}

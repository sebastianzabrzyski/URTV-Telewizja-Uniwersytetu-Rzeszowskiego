function Validatecomment_form()
{
   var regexp;
   var komentarz = document.getElementById('pole_komentarza');
   if (!(komentarz.disabled || komentarz.style.display === 'none' || komentarz.style.visibility === 'hidden'))
   {
      if (komentarz.value == "")
      {
         alert("Niepoprawna treść komentarza");
         komentarz.focus();
         return false;
      }
      if (komentarz.value.length < 1)
      {
         alert("Niepoprawna treść komentarza");
         komentarz.focus();
         return false;
      }
      if (komentarz.value.length > 1200)
      {
         alert("Niepoprawna treść komentarza");
         komentarz.focus();
         return false;
      }
   }
   return true;
}
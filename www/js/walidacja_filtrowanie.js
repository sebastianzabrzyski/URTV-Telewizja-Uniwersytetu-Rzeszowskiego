function Validatefilter_form()
{
   var regexp;
   var password = document.getElementById('password');
   if (!(password.disabled ||
         password.style.display === 'none' ||
         password.style.visibility === 'hidden'))
   {
      if (password.selectedIndex < 0)
      {
         alert("Niepoprawna wartość");
         password.focus();
         return false;
      }
   }
   var date = document.getElementById('date');
   if (!(date.disabled ||
         date.style.display === 'none' ||
         date.style.visibility === 'hidden'))
   {
      if (date.selectedIndex < 0)
      {
         alert("Niepoprawna data");
         date.focus();
         return false;
      }
   }
   var category = document.getElementById('category');
   if (!(category.disabled ||
         category.style.display === 'none' ||
         category.style.visibility === 'hidden'))
   {
      if (category.selectedIndex < 0)
      {
         alert("Niepoprawna kategoria");
         category.focus();
         return false;
      }
   }
   var sort = document.getElementById('sort');
   if (!(sort.disabled ||
         sort.style.display === 'none' ||
         sort.style.visibility === 'hidden'))
   {
      if (sort.selectedIndex < 0)
      {
         alert("Proszę wybrać jedną z opcji sortowania");
         sort.focus();
         return false;
      }
   }
   return true;
}

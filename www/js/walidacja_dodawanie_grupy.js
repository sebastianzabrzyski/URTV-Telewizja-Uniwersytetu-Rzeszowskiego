function Validateadd_group()
{
   var regexp;
   var grupa = document.getElementById('grupa_2');
   if (!(grupa.disabled || grupa.style.display === 'none' || grupa.style.visibility === 'hidden'))
   {
      if (grupa.value == "")
      {
         alert("Nieprawidłowa nazwa grupy");
         grupa.focus();
         return false;
      }
      if (grupa.value.length < 1)
      {
         alert("Nieprawidłowa nazwa grupy");
         grupa.focus();
         return false;
      }
      if (grupa.value.length > 100)
      {
         alert("Zbyt długa nazwa grupy");
         grupa.focus();
         return false;
      }
   }
   return true;
}

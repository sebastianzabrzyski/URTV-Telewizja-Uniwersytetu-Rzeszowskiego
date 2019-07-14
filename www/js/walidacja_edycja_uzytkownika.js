function Validateupdate_user()
{
   var regexp;
   var space = document.getElementById('space');
   if (!(space.disabled || space.style.display === 'none' || space.style.visibility === 'hidden'))
   {
      regexp = /^[-+]?\d*\.?\d*$/;
      if (!regexp.test(space.value))
      {
         alert("Nieprawidłowa ilość miejsca");
         space.focus();
         return false;
      }
      if (space.value == "")
      {
         alert("Nieprawidłowa ilość miejsca");
         space.focus();
         return false;
      }
      if (space.value.length < 1)
      {
         alert("Nieprawidłowa ilość miejsca");
         space.focus();
         return false;
      }
      if (space.value.length > 6)
      {
         alert("Nieprawidłowa ilość miejsca");
         space.focus();
         return false;
      }
      if (space.value != "" && !(space.value >= 0 && space.value <= 999999))
      {
         alert("Nieprawidłowa ilość miejsca");
         space.focus();
         return false;
      }
   }
   var privileges = document.getElementById('privileges');
   if (!(privileges.disabled ||
         privileges.style.display === 'none' ||
         privileges.style.visibility === 'hidden'))
   {
      if (privileges.selectedIndex < 0)
      {
         alert("Niepoprawne uprawnienia");
         privileges.focus();
         return false;
      }
   }
   var login = document.getElementById('login');
   if (!(login.disabled || login.style.display === 'none' || login.style.visibility === 'hidden'))
   {
      if (login.value == "")
      {
         alert("Nieprawidłowy tytuł filmu");
         login.focus();
         return false;
      }
      if (login.value.length < 1)
      {
         alert("Nieprawidłowy tytuł filmu");
         login.focus();
         return false;
      }
      if (login.value.length > 100)
      {
         alert("Nieprawidłowy tytuł filmu");
         login.focus();
         return false;
      }
   }
   return true;
}

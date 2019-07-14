function Validatepassword_check()
{
   var regexp;
   var password = document.getElementById('password');
   if (!(password.disabled || password.style.display === 'none' || password.style.visibility === 'hidden'))
   {
      if (password.value == "")
      {
         alert("Nieprawidłowe hasło");
         password.focus();
         return false;
      }
      if (password.value.length < 1)
      {
       alert("Nieprawidłowe hasło");
         password.focus();
         return false;
      }
   }
   return true;
}

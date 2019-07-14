function Validatechange_password()
{
   var regexp;
   var password_1 = document.getElementById('password_1');
   if (!(password_1.disabled || password_1.style.display === 'none' || password_1.style.visibility === 'hidden'))
   {
      if (password_1.value == "")
      {
         alert("Podano nieprawidłowe hasło");
         password_1.focus();
         return false;
      }
      if (password_1.value.length < 8)
      {
       alert("Podano nieprawidłowe hasło");
         password_1.focus();
         return false;
      }
   }
   var password_2 = document.getElementById('password_2');
   if (!(password_2.disabled || password_2.style.display === 'none' || password_2.style.visibility === 'hidden'))
   {
      if (password_2.value == "")
      {
         alert("Podano nieprawidłowe hasło");
         password_2.focus();
         return false;
      }
      if (password_2.value.length < 8)
      {
           alert("Podano nieprawidłowe hasło");
         password_2.focus();
         return false;
      }
      if (password_2.value != document.getElementById('Text38').value)
      {
         alert("Hasła nie zgadzają się");
         password_2.focus();
         return false;
      }
   }
   var old_password = document.getElementById('old_password');
   if (!(old_password.disabled || old_password.style.display === 'none' || old_password.style.visibility === 'hidden'))
   {
      if (old_password.value == "")
      {
         alert("Podano nieprawidłowe hasło");
         old_password.focus();
         return false;
      }
      if (old_password.value.length < 8)
      {
         alert("Podano nieprawidłowe hasło");
         old_password.focus();
         return false;
      }
   }
   return true;
}

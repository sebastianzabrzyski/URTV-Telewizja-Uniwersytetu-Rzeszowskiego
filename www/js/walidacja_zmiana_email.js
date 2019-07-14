function Validatechange_email()
{
   var regexp;
   var email = document.getElementById('email');
   if (!(email.disabled || email.style.display === 'none' || email.style.visibility === 'hidden'))
   {
      regexp = /^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i;
      if (!regexp.test(email.value))
      {
         alert("Podano nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
      if (email.value == "")
      {
           alert("Podano nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
      if (email.value.length < 1)
      {
               alert("Podano nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
      if (email.value.length > 100)
      {
               alert("Podano nieprawidłowy adres e-mail");
         email.focus();
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

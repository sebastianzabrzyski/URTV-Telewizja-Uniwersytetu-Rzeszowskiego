function Validatenewsletter_signup()
{
   var regexp;
   var email = document.getElementById('email');
   if (!(email.disabled || email.style.display === 'none' || email.style.visibility === 'hidden'))
   {
      regexp = /^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i;
      if (!regexp.test(email.value))
      {
         alert("Nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
      if (email.value == "")
      {
   alert("Nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
      if (email.value.length < 1)
      {
     alert("Nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
   }
   return true;
}

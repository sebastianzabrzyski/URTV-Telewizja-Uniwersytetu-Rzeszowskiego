function Validatereset_password()
{
   var regexp;
   var login_2 = document.getElementById('login_2');
   if (!(login_2.disabled || login_2.style.display === 'none' || login_2.style.visibility === 'hidden'))
   {
      regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9-_]*$/;
      if (!regexp.test(login_2.value))
      {
         alert("Podano nieprawidłowy login");
         login_2.focus();
         return false;
      }
      if (login_2.value == "")
      {
         alert("Podano nieprawidłowy login");
         login_2.focus();
         return false;
      }
      if (login_2.value.length < 1)
      {
         alert("Podano nieprawidłowy login");
         login_2.focus();
         return false;
      }
      if (login_2.value.length > 64)
      {
         alert("Podano nieprawidłowy login");
         login_2.focus();
         return false;
      }
   }
   return true;
}

function Validatelogin_form()
{
   var regexp;
   var password = document.getElementById('password');
   if (!(password.disabled || password.style.display === 'none' || password.style.visibility === 'hidden'))
   {
      if (password.value == "")
      {
         alert("Podano nieprawidłowe hasło");
         password.focus();
         return false;
      }
      if (password.value.length < 1)
      {
         alert("Podano nieprawidłowe hasło");
         password.focus();
         return false;
      }
   }
   var login = document.getElementById('login');
   if (!(login.disabled || login.style.display === 'none' || login.style.visibility === 'hidden'))
   {
      regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9-_]*$/;
      if (!regexp.test(login.value))
      {
         alert("Podano nieprawidłowy login");
         login.focus();
         return false;
      }
      if (login.value == "")
      {
         alert("Podano nieprawidłowy login");
         login.focus();
         return false;
      }
      if (login.value.length < 1)
      {
         alert("Podano nieprawidłowy login");
         login.focus();
         return false;
      }
      if (login.value.length > 64)
      {
         alert("Podano nieprawidłowy login");
         login.focus();
         return false;
      }
   }
   return true;
}

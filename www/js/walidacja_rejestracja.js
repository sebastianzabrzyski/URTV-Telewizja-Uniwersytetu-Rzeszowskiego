function Validateregister_form()
{
   var regexp;
   var terms = document.getElementById('terms');
   if (!(terms.disabled ||
         terms.style.display === 'none' ||
         terms.style.visibility === 'hidden'))
   {
      if (terms.checked != true)
      {
         alert("Proszę zaakceptować regulamin serwisu");
         return false;
      }
   }
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
      if (email.value.length < 5)
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
   var password_2 = document.getElementById('password_2');
   if (!(password_2.disabled || password_2.style.display === 'none' || password_2.style.visibility === 'hidden'))
   {
      if (password_2.value == "")
      {
         alert("Podano nieprawidłowe hasło (min. 8 znaków)");
         password_2.focus();
         return false;
      }
      if (password_2.value.length < 8)
      {
         alert("Podano nieprawidłowe hasło (min. 8 znaków)");
         password_2.focus();
         return false;
      }
      if (password_2.value != document.getElementById('password_1').value)
      {
         alert("Hasła nie zgadzają się");
         password_2.focus();
         return false;
      }
   }
   var password_1 = document.getElementById('password_1');
   if (!(password_1.disabled || password_1.style.display === 'none' || password_1.style.visibility === 'hidden'))
   {
      if (password_1.value == "")
      {
         alert("Podano nieprawidłowe hasło (min. 8 znaków)");
         password_1.focus();
         return false;
      }
      if (password_1.value.length < 8)
      {
         alert("Podano nieprawidłowe hasło (min. 8 znaków)");
         password_1.focus();
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
   var surname = document.getElementById('surname');
   if (!(surname.disabled || surname.style.display === 'none' || surname.style.visibility === 'hidden'))
   {
regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f-ĄąĆćĘęŁłŃńÓóŚśŹźŻż]*$/;
      if (!regexp.test(surname.value))
      {
         alert("Podano nieprawidłowe nazwisko");
         surname.focus();
         return false;
      }
      if (surname.value == "")
      {
         alert("Podano nieprawidłowe nazwisko");
         surname.focus();
         return false;
      }
      if (surname.value.length < 1)
      {
         alert("Podano nieprawidłowe nazwisko");
         surname.focus();
         return false;
      }
      if (surname.value.length > 100)
      {
         alert("Podano nieprawidłowe nazwisko");
         surname.focus();
         return false;
      }
   }
   var name = document.getElementById('name');
   if (!(name.disabled || name.style.display === 'none' || name.style.visibility === 'hidden'))
   {
      regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\fĄąĆćĘęŁłŃńÓóŚśŹźŻż]*$/;
      if (!regexp.test(name.value))
      {
         alert("Podano nieprawidłowe imię");
         name.focus();
         return false;
      }
      if (name.value == "")
      {
         alert("Podano nieprawidłowe imię");
         name.focus();
         return false;
      }
      if (name.value.length < 1)
      {
         alert("Podano nieprawidłowe imię");
         name.focus();
         return false;
      }
      if (name.value.length > 30)
      {
         alert("Podano nieprawidłowe imię");
         name.focus();
         return false;
      }
   }
   return true;
}

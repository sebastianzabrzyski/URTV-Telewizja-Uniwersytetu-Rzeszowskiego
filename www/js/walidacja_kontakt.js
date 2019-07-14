function Validatecontact_form()
{
   var regexp;
   var topic = document.getElementById('topic');
   if (!(topic.disabled || topic.style.display === 'none' || topic.style.visibility === 'hidden'))
   {
      if (topic.value == "")
      {
         alert("Nieprawidłowy temat");
         topic.focus();
         return false;
      }
      if (topic.value.length < 1)
      {
         alert("Nieprawidłowy temat");
         topic.focus();
         return false;
      }
      if (topic.value.length > 100)
      {
         alert("Nieprawidłowy temat");
         topic.focus();
         return false;
      }
   }
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
      if (email.value.length > 100)
      {
         alert("Nieprawidłowy adres e-mail");
         email.focus();
         return false;
      }
   }
   var name = document.getElementById('name');
   if (!(name.disabled || name.style.display === 'none' || name.style.visibility === 'hidden'))
   {
regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f-ĄąĆćĘęŁłŃńÓóŚśŹźŻż]*$/;
      if (!regexp.test(name.value))
      {
         alert("Nieprawidłowe imię i nazwisko");
         name.focus();
         return false;
      }
      if (name.value == "")
      {
         alert("Nieprawidłowe imię i nazwisko");
         name.focus();
         return false;
      }
      if (name.value.length < 1)
      {
         alert("Nieprawidłowe imię i nazwisko");
         name.focus();
         return false;
      }
      if (name.value.length > 100)
      {
           alert("Nieprawidłowe imię i nazwisko");
         name.focus();
         return false;
      }
   }
   var message = document.getElementById('message');
   if (!(message.disabled || message.style.display === 'none' || message.style.visibility === 'hidden'))
   {
      if (message.value == "")
      {
         alert("Niepoprawna treść wiadomości");
         message.focus();
         return false;
      }
      if (message.value.length < 1)
      {
         alert("Niepoprawna treść wiadomości");
         message.focus();
         return false;
      }
      if (message.value.length > 6000)
      {
       alert("Niepoprawna treść wiadomości");
         message.focus();
         return false;
      }
   }
   return true;
}

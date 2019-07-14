function Validateupdate_stream()
{
   var regexp;
   var title = document.getElementById('title');
   if (!(title.disabled || title.style.display === 'none' || title.style.visibility === 'hidden'))
   {
      if (title.value == "")
      {
         alert("Nieprawidłowy tytuł transmisji");
         title.focus();
         return false;
      }
      if (title.value.length < 1)
      {
         alert("Nieprawidłowy tytuł transmisji");
         title.focus();
         return false;
      }
      if (title.value.length > 100)
      {
         alert("Nieprawidłowy tytuł transmisji");
         title.focus();
         return false;
      }
   }
   var autor = document.getElementById('author');
   if (!(autor.disabled || autor.style.display === 'none' || autor.style.visibility === 'hidden'))
   {
      if (autor.value == "")
      {
         alert("Nieprawidłowy autor");
         autor.focus();
         return false;
      }
      if (autor.value.length < 1)
      {
         alert("Nieprawidłowy autor");
         autor.focus();
         return false;
      }
      if (autor.value.length > 100)
      {
           alert("Nieprawidłowy autor");
         autor.focus();
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
   var describtion = document.getElementById('describtion');
   if (!(describtion.disabled || describtion.style.display === 'none' || describtion.style.visibility === 'hidden'))
   {
      if (describtion.value == "")
      {
         alert("Niepoprawny opis");
         describtion.focus();
         return false;
      }
      if (describtion.value.length < 1)
      {
         alert("Niepoprawny opis");
         describtion.focus();
         return false;
      }
   }
   var file1 = document.getElementById('file1');
   var file1_file = document.getElementById('file1-file');
   if (!(file1.disabled ||
         file1.style.display === 'none' ||
         file1.style.visibility === 'hidden'))
   {
      var ext = file1_file.value.substr(file1_file.value.lastIndexOf('.'));
      if ((ext.toLowerCase() != ".gif") &&
          (ext.toLowerCase() != ".jpeg") &&
          (ext.toLowerCase() != ".jpg") &&
          (ext.toLowerCase() != ".png") &&
          (ext != ""))
      {
         alert("Niepoprawna miniaturka");
         return false;
      }
   }
   return true;
}

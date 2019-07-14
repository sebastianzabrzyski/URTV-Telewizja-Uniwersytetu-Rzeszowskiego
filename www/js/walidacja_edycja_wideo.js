function Validateupdate_video()
{
   var regexp;
   var title = document.getElementById('title');
   if (!(title.disabled || title.style.display === 'none' || title.style.visibility === 'hidden'))
   {
      if (title.value == "")
      {
         alert("Nieprawidłowy tytuł filmu");
         title.focus();
         return false;
      }
      if (title.value.length < 1)
      {
         alert("Nieprawidłowy tytuł filmu");
         title.focus();
         return false;
      }
      if (title.value.length > 100)
      {
         alert("Nieprawidłowy tytuł filmu");
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
   var file1_file = document.getElementById('miniature');
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
      var rozmiar_miniaturki = document.getElementById('miniature').files[0].size;
      rozmiar_miniaturki = (rozmiar_miniaturki / 1048576);
      if(rozmiar_miniaturki < 1) {
        rozmiar_miniaturki = 1;
      }
      if(rozmiar_miniaturki > 5) {
        alert("Maksymalny rozmiar miniaturki wynosi 5MB");
        return false;
      }
   return true;
}

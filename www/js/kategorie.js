$(document).ready(function()
{
   $(".folder a").click(function()
   {
      var $popup = $(this).parent().find('ul');
      if ($popup.is(':hidden'))
      {
         $("#kategorie > ul > li > ul").slideUp();
         $popup.slideDown();
         $popup.attr('aria-expanded', 'true');
      }
      else
      {
         $popup.slideUp();
         $popup.attr('aria-expanded', 'false');
      }
   });
});
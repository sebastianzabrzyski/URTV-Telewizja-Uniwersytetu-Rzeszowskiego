function odswiezKlucz(x) {
var name='<?php echo $user_id; ?>';
var streamkey = '';
 if(name)
 {
  $.ajax({
  type: 'post',
  url: 'get_streamkey.php',
  data: {
   id:name
  },
  success: function (response) {
 streamkey = response;
$('#rtmp_key').val(streamkey);
if(streamkey == '') {
	alert('Nie udało się pobrać klucza transmisji');
}
  }
  });
 }
 else
 {
alert('Nie udało się pobrać klucza transmisji');
 }
}

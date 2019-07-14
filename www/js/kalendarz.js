  $(document).ready(function()
  {
    $("#DatePicker1").datepicker(
      {
        dateFormat: 'd M, y',
        changeMonth: false,
        changeYear: false,
        showButtonPanel: false,
        showAnim: ''
      });
      $("#DatePicker1").datepicker("setDate", "");
      $("#DatePicker1").datepicker("option", $.datepicker.regional['pl']);
      $("#DatePicker1").change(function()
      {
        $('#DatePicker1_input').attr('value',$(this).val());
 	$('#date').attr('value',$(this).val());

      });
    });
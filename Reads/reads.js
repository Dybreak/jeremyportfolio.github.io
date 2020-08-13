
$(document).ready(function() 
{
  $("#alert").fadeTo(3000, 500).slideUp(500, function()
  {
    $("#alert").slideUp(500);
  });
  //////LOG LIST TABLE
  $('#validate_id').prop('disabled', true);//disable validate button

  var tableLog = $('#log_list_id').DataTable();
  $('#log_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {   
      ////REMOVE HIGHLIGHT
      $('#validate_id').prop('disabled', true);//disable validate button
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      tableLog.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrLog = [];
      
      $.each($("#log_list_id tr.selected"),function()
      { //get each tr which has selected class
          dataArrLog.push($(this).find('td').eq(0).text()); //find its first td and push the value
          // dataArrLog.push($(this).find('td:first').text()); //You can use this too
      });
      $('#validate_id').prop('disabled', false);
      document.getElementById("log_id").value = dataArrLog;
      ////
    }
  });
});
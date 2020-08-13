$(document).ready(function() 
{
  $("#alert").fadeTo(3000, 500).slideUp(500, function()
  {
    $("#alert").slideUp(500);
  });
  //////LOG LIST TABLE

  var tableLog = $('#log_history_list_id').DataTable();
  $('#log_history_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {   
      ////REMOVE HIGHLIGHT
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      tableLog.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrHistory = [];
      
      $.each($("#log_history_list_id tr:first.selected"),function()
      { //get each tr which has selected class
          dataArrHistory.push($(this).find('td').eq(0).text()); //find its first td and push the value
          // dataArrHistory.push($(this).find('td:first').text()); //You can use this too
      });

      document.getElementById("log_id").value = dataArrHistory;
      ////
    }
  });
});
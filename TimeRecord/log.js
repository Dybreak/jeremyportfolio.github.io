
$(document).ready(function() 
{
  $("#alert").fadeTo(5500, 1500).slideUp(1500, function(){
    $("#alert").slideUp(1500);
  });

  $('#confirm_id').prop('disabled', true);//disable confirm button
  //$('#confirm_id').val(''); //Change input value
  $( "#faculty_id" ).focusout(function() 
  {
    $("#faculty_id").focus(); //if textbox focus is out, focus again
  });
  $('#log_list_id').DataTable({
    "bFilter": false,
    "bInfo" : false,
    "lengthChange": false,
    "pageLength": 8,
    "order": [[ 0, "desc" ]]
  });
  // $('#log_list_id').DataTable().page('last').draw('page');
  

  //////ACCOUNT LIST TABLE
  var tableSubject = $('#subject_list_id').DataTable();
  $('#subject_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {
      ////REMOVE HIGHLIGHT
      $('#confirm_id').prop('disabled', true);
      document.getElementById("choose_subject_id").value = "";
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      
      tableSubject.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 
      ////READING FIRST COLUMN OF THE TABLE
      var dataArrSubject = [];
      $.each($("#subject_list_id tr.selected"),function()
      { //get each tr which has selected class
          dataArrSubject.push($(this).find('td').eq(0).text()); //find its first td and push the value
          // dataArrSubject.push($(this).find('th:first').text()); //You can use this too
      });
      $('#confirm_id').prop('disabled', false);
      document.getElementById("choose_subject_id").value = dataArrSubject;
      ////
    }
  }); 
});

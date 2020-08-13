// var tableAccountList = document.getElementById('table-account-list-js');
// var optionModal = document.getElementById("manage-modal-js");
// var optionClose = document.getElementById("manage-modal-close")[0];

// optionClose.onclick = function() 
// {
//     optionModal.style.display = "none";
// }
// window.onclick = function(event) 
// {
//     if (event.target == optionModal) 
//     {  
//         optionModal.style.display = "none";
//     }
// }
// for(var i = 1; i< tableAccountList.rows.length;i++)
// {
//     tableAccountList.rows[i].onclick = function()
//     {
//         optionModal.style.display = "block";
//         document.getElementById("id").value = this.cells[0].innerHTML;
//         //window.location = 'manageprofile.php?id='+ document.getElementById("id").value;
//     }
// }
// function changeLog() 
// {
//   var element = document.getElementById("navbar_dropdown_Log");
//   element.classList.toggle("btn-lg");
//   element = document.getElementById("navbar_dropdown_manage");
//   element.classList.toggle("btn-sm");

//   element = document.getElementById("navbar_dropdown_request");
//   element.classList.toggle("btn-sm");
// }


$(document).ready(function() 
{ 
  $("#alert").fadeTo(3000, 500).slideUp(500, function()
  {
    $("#alert").slideUp(500);
  });
  
  $( "#rfid_id" ).focusout(function() 
  {
    $("#rfid_id").focus(); //if textbox focus is out, focus again
  });

  $('#manage_account_id').prop('disabled', true);//disable confirm button

  //////SCHEDULE LIST TABLE
  $('#schedule_list_id').DataTable({
    "order": [[ 2, "asc" ]]
  });
  var tableSchedule = $('#schedule_list_id').DataTable();
  $('#schedule_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {
      ////REMOVE HIGHLIGHT
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      tableSchedule.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrSchedule = [];
      $.each($("#schedule_list_id tr.selected"),function()
      { //get each tr which has selected class
          dataArrSchedule.push($(this).find('td').eq(0).text()); //find its first td and push the value
          // dataArrSchedule.push($(this).find('th:second').text()); //You can use this too
      });
      document.getElementById("schedule_id").value = dataArrSchedule;
      editSchedule('schedule_id');
      ////
    }
  });
  //////ACCOUNT LIST TABLE
  $('#account_list_id').DataTable({
    "order": [[ 4, "asc" ]]
  });
  var tableAccount = $('#account_list_id').DataTable();
  $('#account_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {
      ////REMOVE HIGHLIGHT
      $('#manage_account_id').prop('disabled', true);//disable confirm button
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      
      tableAccount.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrAccount = [];
      $.each($("#account_list_id tr.selected"),function()
      { //get each tr which has selected class
          dataArrAccount.push($(this).find('td').eq(0).text()); //find its first td and push the value
          // dataArrAccount.push($(this).find('th:first').text()); //You can use this too
      });
      $('#manage_account_id').prop('disabled', false);
      document.getElementById("id").value = dataArrAccount;
      ////
    }
  });
  
  //////REQUEST LIST TABLE
  $('#request_list_id').DataTable
  ({
    "order": [[ 1, "asc" ]]
  });
  var tableRequest = $('#request_list_id').DataTable();
  $('#request_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {   
      ////REMOVE HIGHLIGHT
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      tableRequest.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrRequest = [];
      
      $.each($("#request_list_id tr.selected"),function()
      { 
        //get each tr which has selected class
        dataArrRequest.push($(this).find('td').eq(0).text()); //find its first td and push the value
        //dataArrRequest.push($(this).find('th:first').text()); //You can use this too
        $(this).removeClass('selected'); 
      });    
      document.getElementById("request_id").value = dataArrRequest;
      optionRequest('request_id');
      ////
    }
  });


  //////LOG RECORD LIST TABLE
  // $('#log_record_id').DataTable({
  //   "order": [[ 6, "asc" ]]
  // });
  var tableLog = $('#log_record_id').DataTable();
  $('#log_record_id tbody').on( 'click', 'tr', function() 
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
      var dataArrLog = [];
      
      $.each($("#log_record_id tr.selected"),function()
      { //get each tr which has selected class
          //dataArr.push($(this).find('td').eq(0).text()); //find its first td and push the value
          dataArrLog.push($(this).find('td:first').text()); //You can use this too
      });
      document.getElementById("idrequest").value = dataArrLog;
      ////
    }
  });

  //////SUBJECT LIST TABLE
  //$("#exampleModal").modal('show');
  var tableSubject = $('#subject_list_id').DataTable();
  $('#subject_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {
      ////REMOVE HIGHLIGHT
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
      { 
        //get each tr which has selected class
        dataArrSubject.push($(this).find('td').eq(0).text()); //find its first td and push the value
        //dataArrSubject.push($(this).find('td:first').text()); //You can use this too
        $(this).removeClass('selected');   
      });
      document.getElementById("edit_subject_id").value = dataArrSubject;
      editSubject('edit_subject_id');
      ////
    }
  });

  //////ROOM LIST TABLE
  //$("#exampleModal").modal('show');
  var tableRoom = $('#room_list_id').DataTable();
  $('#room_list_id tbody').on( 'click', 'tr', function() 
  {
    if ( $(this).hasClass('selected') ) 
    {
      ////REMOVE HIGHLIGHT
      $(this).removeClass('selected');    
    }
    else 
    {    
      ////HIGHLIGHT SELECTED ROW
      tableRoom.$('tr.selected').removeClass('selected');
      $(this).addClass('selected'); 

      ////READING FIRST COLUMN OF THE TABLE
      var dataArrRoom = [];
      $.each($("#room_list_id tr.selected"),function()
      { 
        //get each tr which has selected class
        dataArrRoom.push($(this).find('td').eq(0).text()); //find its first td and push the value
        //dataArrRoom.push($(this).find('th:first').text()); //You can use this too
        $(this).removeClass('selected');   
      });
      document.getElementById("edit_room_id").value = dataArrRoom;
      editRoom('edit_room_id');
      ////
    }
  });
  // $('#button').click(function ()
  // {
  //   var dataArr = [];
  //   $.each($("#example tr.selected"),function()
  //   { //get each tr which has selected class
  //       //dataArr.push($(this).find('td').eq(0).text()); //find its first td and push the value
  //       dataArr.push($(this).find('td:first').text()); //You can use this too
  //   });
  //   alert(dataArr);
  // }); 
});

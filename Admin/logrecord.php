<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);

  $status="Active";
  $ID;  

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Admin - Log Record</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarAdmin.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="admin.css" type="text/css"> 
  </head>
  <body>
    <div class="container-fluid mt-3 w-100">
      <div class="container-fluid w-100">
        <a class="h2" href="logrecord.php"><span class="fas fa-calendar-check"></span> Log</a>
        <div class="card">
          <div class="card-header navbar navbar-expand-sm">
            <ul class="navbar-nav">
              <li class="nav-item">
                <button class="btn btn-dark btn-sm btn-block" type="button" onclick="jQuery('#log_record_id').print()">
                  <span class="fas fa-print mr-1"></span>Print Preview
                </button>
              </li>              
            </ul>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-3">
              </div>
              <div class="form-group col-3 bg-white" style="border-bottom:2px solid #085C2E;border-top:9px solid #085C2E;">
                <label for="min">Start Date:</label>
                <input id="min" name="min" class="form-control">
              </div>
              <div class="form-group col-3 bg-white" style="border-bottom:2px solid #085C2E;border-top:9px solid #085C2E;">
                <label for="max">End Date:</label>
                <input id="max" name="max" class="form-control">
              </div>
              <div class="col-3">
              </div>
            </div>     
            <div class="row">
              <div class="col">
                <div class="table-responsive">
                  <table id="log_record_id" class="table table-sm table-hover text-center">     
                    <thead>
                      <tr>
                        <th colspan="10" class="border border-0" style="background-color: #a2a194">
                          <div class="bg-light row text-center d-none d-print-block">
                            <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                            <p class="h5">FACULTY ATTENDANCE RECORD</p>
                            <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                            <p></p>
                            <p id="range" class="h6"></p>
                          </div>
                        </th>
                      </tr>
                      <tr>  
                        <td></td>
                        <th scope="col">Faculty Name</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Schedule</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">End Time</th>
                        <th scope="col">Total Hours</th>
                        <th scope="col">Log Date</th>
                        <th scope="col">Validated By</th>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>         
                      <?PHP
                        $timecurrent = Date("H:i:s");

                        if($connected == true)
                        {
                          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database); 
                          if ($db_found) 
                          { 
                            $SQL = $db_found->prepare('SELECT * FROM tbl_time_record');
                            
                            $SQL->execute();
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                 
                                $timeRemaining = strtotime($timecurrent) - strtotime($db_field['endTime']);  
                                print("<tr data-toggle='modal' data-target='#option-modal'>");
                                print("<td>".$db_field['record_ID']."</td>");
                                print("<td>".$db_field['facultyName']."</td>");
                                print("<td>".$db_field['subjectDescription']."</td>");

                                $SQL = $db_found->prepare('SELECT * FROM tbl_schedule WHERE RFID = ? AND schedule_ID = ?');
                                $SQL->bind_param('si', $db_field['RFID'], $db_field['schedule_ID']);
                                $SQL->execute();
                                $result1 = $SQL->get_result();
                                if ($result1->num_rows > 0) 
                                {
                                  while ( $db_field1 = $result1->fetch_assoc() ) 
                                  {   
                                    $schedule = date("h:i:s A", strtotime($db_field1['startTime']))." - ".date("h:i:s A",
                                                                                           strtotime($db_field1['endTime']));

                                    $endtime = $db_field1['endTime'];  
                                  }
                                }
                                if($db_field['endTime'] == NULL)
                                {
                                  $timeRemaining = strtotime($timecurrent) - strtotime($endtime);

                                  //////UPDATE LOGSTATUS IF NOT COMPLETE
                                  $void = "VOID";
                                  $SQL = $db_found->prepare("UPDATE tbl_time_record SET endTime=?, totalHours=? WHERE RFID = ?
                                                                                                        AND schedule_ID = ?");
                                  $SQL->bind_param('sssi', $void, $void , $db_field['RFID'], $db_field['schedule_ID']);
                                  $SQL->execute();
                                  //////END
                                }
                                print("<td>".$schedule."</td>");
                                print("<td>".$db_field['startTime']."</td>");
                                print("<td>".$db_field['endTime']."</td>");
                                print("<td>".$db_field['totalHours']."</td>");
                                print("<td>".$db_field['logDate']."</td>");
                                print("<td>".$db_field['verifiedBy']."</td>");   
                                print("<td></td></tr>"); 
                                $timeRemaining = 0;
                              }
                            }
                            else
                            {
                              $message = "No records found.";
                              $messageType = 3;
                              MessageDisplay($message, $messageType);
                            }
                            $SQL->close();
                            $db_found->close();
                          }
                          else
                          {
                            $message = "Database not found.";
                            $messageType = 3;
                            MessageDisplay($message, $messageType);
                          }
                        }  
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>
    <!-- LOCAL TOOLS -->
    <script src="admin.js"></script>
    
  </body>
</html>
<script>
$(document).ready(function()
{
  $('#min').datepicker
  ({
    uiLibrary: 'bootstrap4',
    iconsLibrary: 'fontawesome',
    // minDate: today,
    dateFormat: 'MM/DD/YYYY',
    maxDate: function () {
        return $('#max').val();
    }
  });
  $('#max').datepicker
  ({
      uiLibrary: 'bootstrap4',
      iconsLibrary: 'fontawesome',
      dateFormat: 'MM/DD/YYYY',
      minDate: function () {
          return $('#min').val();
      }
  });
  $.fn.dataTableExt.afnFiltering.push
  (
    function( settings, data, dataIndex ) 
    {
      var min  = $('#min').val()
      var max  = $('#max').val()
      var createdAt = data[7] || 0; // Our date column in the table
      //createdAt=createdAt.split(" ");
      var startDate   = moment(min, "MM/DD/YYYY");
      var endDate     = moment(max, "MM/DD/YYYY");
      var diffDate = moment(createdAt, "MM/DD/YYYY");
      //console.log(startDate);
      if 
      (
        (min == "" || max == "") ||
        (diffDate.isBetween(startDate, endDate, null, '[]'))
      ) 
      {  
        return true;
      }
      return false;
    }
  );
  var table = $('#log_record_id').DataTable();

    // Event listener to the two range filtering inputs to redraw on input
    $('#min, #max').change(function() {
      table.draw();
      var daterangemin = document.getElementById('min');
      var daterangemax = document.getElementById('max');

      var date = new Date(daterangemin.value);  // 2009-11-10
      daterangemin = date.toLocaleString('default', { month: 'short', day: '2-digit', year: 'numeric'});
      date = new Date(daterangemax.value);  // 2009-11-10
      daterangemax = date.toLocaleString('default', { month: 'short', day: '2-digit', year: 'numeric'});

      var range = daterangemin + " - " + daterangemax;
      document.getElementById("range").value = range;

      document.getElementById("range").innerHTML = range;
      document.getElementById("range").textContent = range;
    });
});
</script>
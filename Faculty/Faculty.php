<?PHP
  include '../restrictFaculty.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);

  $fname = $_SESSION['firstName'];
  $lname = $_SESSION['lastName'];
  $mname = $_SESSION['middleName'];

  $schedule = "";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Faculty - Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarFaculty.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="faculty.css" type="text/css"> 
  </head>
  <body>
    <div class="container mt-3">
      <a class="h2" href="faculty.php"><span class="fas fa-book"></span> Log History</a>

      <div class="card">
        <div class="card-header navbar navbar-expand-sm navbar-light" style="background-color: transparent;">
          <ul class="navbar-nav">
            <li class="nav-item">
              <button class="btn btn-dark btn-sm" type="button" onclick="jQuery('#log_history_list_id').print()">
                <span class="fas fa-print mr-1"></span>Print Preview
              </button>
            </li>              
          </ul>
        </div>
        <div class="card-body">
          <div class="row">
              <div class="col">
                <div class="table-responsive">
                <table id="log_history_list_id" class="table table-sm table-hover text-center">  
                  <thead>
                    <tr>  
                      <th colspan="9" class="border border-0" style="background-color: #a2a194">
                        <div class="bg-light row text-center d-none d-print-block">
                          <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                          <p class="h5"><?PHP print $fname." ".$lname." - Log" ?></p>
                          <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                        </div>
                      </th>
                    </tr>
                    <tr>
                      <td></td>
                      <th scope="col">Subject</th>
                      <th scope="col">Schedule</th>
                      <th scope="col">Room</th>
                      <th scope="col">Log Type</th>
                      <th scope="col">Log Time</th>
                      <th scope="col">Log Date</th>
                      <th scope="col">Verified?</th>
                      <td></td>
                    </tr>
                  </thead>

                  <tbody>         
                    <?PHP
                      if($connected == true)
                      {
                        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);  
                        if ($db_found) 
                        { 
                          $SQL = $db_found->prepare('SELECT * FROM tbl_faculty_log WHERE RFID = ?');
                          $SQL->bind_param('s', $_SESSION['RFID']);
                          $SQL->execute();
                          $result = $SQL->get_result();
                  
                          if ($result->num_rows > 0) 
                          {
                            while ( $db_field = $result->fetch_assoc() ) 
                            {            
                              ?>
                              <tr>
                              <td> <?PHP print $db_field['log_ID'] ?></td>
                              <td> <?PHP print $db_field['subjectIn'] ?></td>
                              <?PHP
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
                                }
                              }
                              ?>
                              <td> <?PHP print $schedule ?></td>
                              <td> <?PHP print $db_field['room'] ?></td>
                              <td> <?PHP print $db_field['logType'] ?></td>
                              <td> <?PHP print date("h:i:s A", strtotime($db_field['logTime'])) ?></td>
                              <td> <?PHP print $db_field['logDate'] ?></td>
                              <?PHP
                                if($db_field['isVerified'] == 0)
                                {
                                  ?><td>Not verified</td><?PHP
                                }
                                else
                                {
                                  ?><td>Verified</td><?PHP
                                }
                              ?>
                              <td></td></tr>
                              <?PHP
                              
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
    <!-- LOCAL TOOLS -->
    <script src="faculty.js"></script>
  </body>
</html>
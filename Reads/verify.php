<?PHP
  include '../restrictREADS.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);

  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      $logID = $_POST['ID'];
      if (isset($_POST['accept']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);  
        //CONFIRM LOG
        if ($db_found) 
        {
          $SQL = $db_found->prepare('SELECT * FROM tbl_faculty_log WHERE log_ID = ?');
          $SQL->bind_param('i', $logID);
          $SQL->execute();
          $result = $SQL->get_result();
          if ($result->num_rows > 0) 
          {
            while ( $db_field = $result->fetch_assoc() ) 
            {   
              $logStatus = 1;
              //////STORE TEMPORARILY
              $saccountid = $db_field['RFID'];
              $sscheduleid = $db_field['schedule_ID'];
              $sfname = $db_field['facultyName'];
              $sdescription = $db_field['subjectIn'];

              // $SQL = $db_found->prepare('SELECT * FROM tbl_schedule WHERE RFID = ? AND schedule_ID = ?');
              // $SQL->bind_param('si', $db_field['RFID'], $db_field['schedule_ID']);
              // $SQL->execute();
              // $result1 = $SQL->get_result();
              // if ($result1->num_rows > 0) 
              // {
              //   while ( $db_field1 = $result1->fetch_assoc() ) 
              //   {   
              //     $sstartTime = $db_field['startTime'];
              //     $sendTime = $db_field['endTime'];
              //   }
              // }
              $slogtime = date("h:i:s A", strtotime($db_field['logTime']));
              $slogtype = $db_field['logType'];
              $slogdate = $db_field['logDate'];
              //////END

              if($db_field['logType'] == "Time-In")
              {  
                //////INSERT TO TIME RECORD AFTER TIME IN
                $SQL = $db_found->prepare("INSERT INTO tbl_time_record (RFID, schedule_ID, log_ID, facultyName, subjectDescription,
                                                                                                    startTime,logDate, verifiedBy) 
                                                                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $SQL->bind_param('siisssss', $saccountid, $sscheduleid, $logID, $sfname, $sdescription, $slogtime, $slogdate,
                                                                                                       $_SESSION['firstName']);      
                $SQL->execute();
                //////END
                //////UPDATING LOGSTATUS IF TI  ME IN IS DONE
                $SQL = $db_found->prepare("UPDATE tbl_faculty_log SET isVerified=? WHERE log_ID=?");
                $SQL->bind_param('ii', $logStatus, $logID);
                $SQL->execute();
                //////END 
              }
              else if($db_field['logType'] == "Time-Out")
              {
                $SQL = $db_found->prepare('SELECT * FROM tbl_time_record WHERE RFID = ? AND schedule_ID = ? AND log_ID=? AND endTime IS NULL');

                $SQL->bind_param('sii', $saccountid, $sscheduleid,$db_field['pairLog']);
                $SQL->execute();

                $result1 = $SQL->get_result();
                if ($result1->num_rows > 0) 
                {          
                  while ( $db_field1 = $result1->fetch_assoc() ) 
                  {          
                    $verify = $db_field1['verifiedBy']."/".$_SESSION['firstName'];
                    ////// ABSOLUTE VALUE OF TIME DIFFERENCE IN SECONDS
                    $totalHours = abs(strtotime($db_field1['startTime']) - strtotime($slogtime));
                    //////END

                    //////TOTAL HOURS CONVERTED
                    $totalHours = gmdate("H:i:s", $totalHours);
                    //////END
                    //////UPDATE ROW IF TIME OUT IS DONE
                    $SQL = $db_found->prepare("UPDATE tbl_time_record SET endTime=?, totalHours=?, verifiedBy=? WHERE RFID=?
                                                          AND schedule_ID=? AND log_ID=? AND endTime IS NULL");
                    $SQL->bind_param('sssiii',  $slogtime, $totalHours, $verify, $saccountid, $sscheduleid, $db_field['pairLog']);
                    $SQL->execute();
                    //////END
                    //////UPDATING LOGSTATUS IF TI  ME IN IS DONE
                    $SQL = $db_found->prepare("UPDATE tbl_faculty_log SET isVerified=? WHERE log_ID=?");
                    $SQL->bind_param('ii',  $logStatus, $logID);
                    $SQL->execute();
                    //////END        
                  }
                }
                else
                {
                  $message = "No match found! Verify <code class='h5'>Time-in</code> first.";
                  $messageType = 3;
                  MessageDisplay($message, $messageType);
                }
              }  
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
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>READS - Verify</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarREADS.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="reads.css" type="text/css"> 
  </head>
  <body>
    <div class="container">
      <a class="h2" href="verify.php" style="color: #212121"><span class="fas fa-users"></span> Log Lists</a>

      <div class="card">
        <div class="card-header navbar navbar-expand-sm navbar-light" style="background-color: transparent;">   
          <ul class="navbar-nav ml-auto">
            <form method ="POST" enctype="multipart/form-data" ACTION ="verify.php"> 
            <li class="nav-item">
              <input type="text" class="form-control" id="log_id" name ='ID' readonly hidden>
              <input type="submit" class="btn btn-lg" id="validate_id" name="accept" value="Validate" style="background-color: #F0C529">
            </li>
            </form> 
          </ul>
          
        </div>
        <div class="card-body">
          <div class="row">
              <div class="col">
                <div class="table-responsive">
                <table id="log_list_id" class="table table-sm table-hover text-center"> 
                  <thead>
                    <tr>  
                      <td></td>
                      <th scope="col">Faculty Name</th>
                      <th scope="col">Subject</th>
                      <th scope="col">Schedule</th>
                      <th scope="col">Log Type</th>
                      <th scope="col">Log Time</th>
                      <th scope="col">Log Date</th>
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
                          $SQL = $db_found->prepare('SELECT * FROM tbl_faculty_log');
                          
                          $SQL->execute();
                          $result = $SQL->get_result();
                  
                          if ($result->num_rows > 0) 
                          {
                            while ( $db_field = $result->fetch_assoc() ) 
                            {            
                              if($db_field['isVerified'] == 0)
                              {
                                print("<tr data-toggle='modal' data-target='#verify-modal'>");
                                print("<td>".$db_field['log_ID']."</td>");
                                
                                print("<td>".$db_field['facultyName']."</td>");
                                print("<td>".$db_field['subjectIn']."</td>");

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
                                print("<td>".$schedule."</td>");
                                print("<td>".$db_field['logType']."</td>");
                                print("<td>".date("h:i:s A", strtotime($db_field['logTime']))."</td>");
                                print("<td>".$db_field['logDate']."</td>");

                                print("<td>".$db_field['schedule_ID']."</td></tr>");  
                              }                 
                            }
                          }
                          $SQL->close();
                          $db_found->close();
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
    <script src="reads.js"></script>
  </body>
</html>
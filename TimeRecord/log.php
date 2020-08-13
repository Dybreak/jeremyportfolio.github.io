
<?PHP
  require '../../../../configure.php'; //connect to data base
  session_start();
  //////FACULTY INFO
  $facultyName = "";
  $facultyImage = file_get_contents("../Images/defaultphoto.png");
  $facultyImage = base64_encode($facultyImage);
  $facultyStatus="";
  $RFIDCODE=""; //RFID value
  //////END
  //////SUBJECT INFO
  $subjectCoursedescription = "";
  //////END
  $whatDay = date("D");
  // $whatDay = "Sat";
  $dateToday = date('m/d/Y');
  $checkOngoingClass = 0;

  $schedule = "";
  $waitingClass = [];
  $countWaitingclass = 0;

  $currentDate = date('m/d/Y');  //  month/day/year

  if($connected == true)
  {
    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
    if ($db_found) 
    {
      $SQL = $db_found->prepare('SELECT * FROM tbl_date');
      $SQL->execute();
      $result = $SQL->get_result();
      if ($result->num_rows > 0) 
      {
        while ( $db_field = $result->fetch_assoc() ) 
        {
          if($currentDate != $db_field['storeDate'])
          {
            //////UPDATING DATE TO CURRENT
            $SQL = $db_found->prepare("UPDATE tbl_date SET storeDate=?");
            $SQL->bind_param('s',  $currentDate);
            $SQL->execute();
            //////END     
            $logStatus = 0;
            $messageStatus = "Waiting";
            //////RESET LOGSTATUS
            $SQL1 = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=?");
            $SQL1->bind_param('is',  $logStatus, $messageStatus);
            $SQL1->execute();
            //////END  
          }
        }
      }
      $timecurrent = Date("H:i:s");
      
      $SQL = $db_found->prepare('SELECT * FROM tbl_schedule');
      $SQL->execute();
      $result = $SQL->get_result();
      if ($result->num_rows > 0) 
      {
        while ( $db_field = $result->fetch_assoc() ) 
        {
          $timeRemaining = strtotime($timecurrent) - strtotime($db_field['endTime']);
          
          //////UPDATE LOGSTATUS IF NOT COMPLETE
          $tempLogStatus = $db_field['logStatus'];
          $logStatus = 3;
          $messageStatus = "Not complete"; 

          $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE endTime=? AND $timeRemaining >= 1200 
                                                                            AND $tempLogStatus = 1 AND daySchedule LIKE '%$whatDay%'");
          $SQL->bind_param('iss', $logStatus, $messageStatus ,$db_field['endTime']);
          $SQL->execute();
          //////END

          //////UPDATE LOGSTATUS IF ABSENT
          $logStatus = 4;
          $messageStatus = "Absent"; 
          
          $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE endTime=? AND $timeRemaining >= 1200 
                                                                            AND $tempLogStatus = 0 AND daySchedule LIKE '%$whatDay%'");
          $SQL->bind_param('iss',  $logStatus, $messageStatus ,$db_field['endTime']);
          $SQL->execute();
          //////END     
        }
      }
      $SQL->close();
      $db_found->close();
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      if(isset($_POST['proceed']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          $logType = "Time-In";
          $logStatus = 1; 
          $messageStatus = "On-Going";       
          //////INSERTING DATA TO TBL_FACULTY_LOG
          $SQL = $db_found->prepare("INSERT INTO tbl_faculty_log (RFID, schedule_ID, logTime, facultyName, subjectIn,
                                                    room, logType, logDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

          $SQL->bind_param('sissssss', $_SESSION['RFIDCODE'], $_SESSION['scheduleID'], $_SESSION['timeLog'], $_SESSION['facultyName'],
           $_SESSION['subjectCoursedescription'], $_SESSION['scheduleRoom'], $logType, $dateToday);
          $SQL->execute();
          //////END
          //////UPDATING LOGSTATUS IF TIME IN IS DONE
          $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE schedule_ID=?");
          $SQL->bind_param('isi',  $logStatus, $messageStatus, $_SESSION['scheduleID']);
          $SQL->execute();
          //////END
          $SQL = $db_found->prepare("SELECT * FROM tbl_schedule WHERE RFID = ? AND logStatus = 0
                                                      AND startTime < ? AND daySchedule LIKE '%$whatDay%'");
          //////UPDATE LOGSTATUS IF ABSENT
          $logStatus = 4;
          $messageStatus = "Absent"; 
          
          $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE RFID = ? AND logStatus = 0
                                                                        AND startTime < ? AND daySchedule LIKE '%$whatDay%'");
          $SQL->bind_param('isss',  $logStatus, $messageStatus, $_SESSION['RFIDCODE'], $_SESSION['endTime']);
          $SQL->execute();
          //////END    
        }
      }
      if(isset($_POST['rfid']) && isset($_POST['choose_subject']))
      {
        $timeLog = strtotime(date('H:i:s')); //convert current time to timestamp
        $ID = $_POST['rfid']; //value of RFID code scanned
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          //////SEARCHING FOR SCHEDULE LOGSTATUS IF MORE THAN 1
          $SQL = $db_found->prepare('SELECT * FROM tbl_schedule WHERE RFID = ?');
          $SQL->bind_param('i', $_POST['rfid']);
          $SQL->execute();
          //////END
          $result = $SQL->get_result();
          if ($result->num_rows > 0) 
          {
            while ( $db_field = $result->fetch_assoc()) 
            {        
              if($db_field['logStatus'] == 1)
              {
                ++$checkOngoingClass;
              }
            }
          }
          //////   
          //////SEARCHING ACCOUNT
          $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE RFID = ?');
          $SQL->bind_param('s', $ID);
          $SQL->execute();
          //////END
          $result = $SQL->get_result();
          if ($result->num_rows > 0) 
          {    
            while ( $db_field = $result->fetch_assoc() ) 
            {
              //////STORE TEMPORARILY THE ACCOUNT DATA
              $fFirstname = $db_field['firstName'];
              $fMiddlename = $db_field['middleName'];
              $fLastname = $db_field['lastName'];  
              $facultyStatus = $db_field['accountStatus'];      
              $facultyImage = $db_field['accountImage'];
              //////END
            }
          }
          //////SEARCHING FOR SCHEDULE
          $SQL = $db_found->prepare('SELECT * FROM tbl_schedule WHERE schedule_ID = ?');
          $SQL->bind_param('i', $_POST['choose_subject']);
          $SQL->execute();
          //////END
          $result = $SQL->get_result();
          if ($result->num_rows > 0) 
          {
            while ( $db_field = $result->fetch_assoc()) 
            {        
              if (strpos($db_field['daySchedule'], $whatDay) !== false) 
              {
                $RFIDCODE = $ID;           

                $sstartTime = $db_field['startTime'];
                $sendTime = $db_field['endTime'];
                $scheduleLogstatus = $db_field['logStatus'];
                $scheduleID = $db_field['schedule_ID'];
                $subjectID = $db_field['subject_ID'];
    
                //////TO BE DISPLAYED
                $scheduleRoom = $db_field['room'];

                if(empty($fMiddlename))
                {
                  $facultyName = $fLastname.", ".$fFirstname; //faculty full name
                }
                else
                {
                  $facultyName = $fLastname.", ".$fFirstname." ".$fMiddlename[0]."."; //faculty full name
                }    
                //////END
                //////GETTING SUBJECT DATA
                $SQL = $db_found->prepare('SELECT * FROM tbl_subject WHERE subject_ID = ?');
                $SQL->bind_param('i', $subjectID);
                $SQL->execute();
                $result = $SQL->get_result();
                if ($result->num_rows > 0) 
                {
                  while ( $db_field1 = $result->fetch_assoc() ) 
                  {           
                    //////STORE SUBJECT DATA FOR LATER USE
                    $subjectCoursedescription = $db_field1['description'];
                  }
                }
                // $timeLog = date("h:i:s A", $timeLog); //convert timestamp to hours minutes
                $timeLog = date("H:i:s", $timeLog); //convert timestamp to hours minutes
                
                //////TIME IN 
                //////RFID IS NOT YET TIME IN
                if($scheduleLogstatus == 0 && $checkOngoingClass == 0)
                {
                  $timeRemaining = strtotime($timecurrent) - strtotime($db_field['endTime']);
                  if($timeRemaining < 1200)
                  {     
                    $SQL = $db_found->prepare("SELECT * FROM tbl_schedule WHERE RFID = ? AND logStatus = 0
                                                      AND startTime < ? AND daySchedule LIKE '%$whatDay%'");
                    $SQL->bind_param('ss', $RFIDCODE, $sstartTime);
                    $SQL->execute();
                    //////END
                    $result2 = $SQL->get_result();
                    if ($result2->num_rows > 0) 
                    {
                      while ( $db_field2 = $result2->fetch_assoc()) 
                      {        
                        $SQL = $db_found->prepare('SELECT * FROM tbl_subject WHERE subject_ID = ?');
                        $SQL->bind_param('i', $db_field2['subject_ID']);
                        $SQL->execute();
                        //////END
                        $result3 = $SQL->get_result();
                        if ($result3->num_rows > 0) 
                        {
                          while ( $db_field3 = $result3->fetch_assoc()) 
                          {        
                            $waitingClass[$countWaitingclass] = date("h:i:s A", strtotime($db_field2['startTime']))." - ".
                            date("h:i:s A", strtotime($db_field2['endTime']))." | <b>".$db_field3['description']."</b>";
                          }
                        }
                        ++$countWaitingclass;
                        
                      }
                    }
                    
                    if($countWaitingclass == 0)
                    {
                      $logType = "Time-In";
                      $logStatus = 1; 
                      $messageStatus = "On-Going";       
                      //////INSERTING DATA TO TBL_FACULTY_LOG
                      $SQL = $db_found->prepare("INSERT INTO tbl_faculty_log (RFID, schedule_ID, logTime, facultyName, subjectIn,
                                                                room, logType, logDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                      $SQL->bind_param('sissssss', $RFIDCODE, $scheduleID, $timeLog, $facultyName, $subjectCoursedescription,
                                                                                          $scheduleRoom, $logType, $dateToday);
                      $SQL->execute();
                      //////END
                      //////UPDATING LOGSTATUS IF TIME IN IS DONE
                      $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE schedule_ID=?");
                      $SQL->bind_param('isi',  $logStatus, $messageStatus, $scheduleID);
                      $SQL->execute();
                      //////END
                    }
                    else
                    {
                      $_SESSION['RFIDCODE'] = $RFIDCODE;
                      $_SESSION['scheduleID'] = $scheduleID;
                      $_SESSION['timeLog'] = $timeLog;
                      $_SESSION['facultyName'] = $facultyName;
                      $_SESSION['subjectCoursedescription'] = $subjectCoursedescription;
                      $_SESSION['scheduleRoom'] = $scheduleRoom;
                      $_SESSION['endTime'] = $sendTime;
                    }
                  } 
                }
                else if($scheduleLogstatus == 0 && $checkOngoingClass == 1)
                {
                  $message = "Other class is still On-Going! Time out first.";
                  $messageType = 3;
                  MessageDisplay($message, $messageType); 
                }
                //TIME OUT
                else if($scheduleLogstatus == 1)
                { 
                  $checkOngoingClass = 0;
                  $SQL = $db_found->prepare('SELECT * FROM tbl_faculty_log WHERE RFID = ? AND schedule_ID = ? AND pairLog IS NULL');

                  $SQL->bind_param('si', $RFIDCODE, $scheduleID);
                  $SQL->execute();

                  $result = $SQL->get_result();
                  if ($result->num_rows > 0) 
                  {          
                    while ( $db_field2 = $result->fetch_assoc() ) 
                    {          
                      $logType = "Time-Out";
                      $logStatus = 2;
                      $messageStatus = "Completed";
                      //////INSERTING DATA TO TBL_FACULTY_LOG
                      $SQL = $db_found->prepare("INSERT INTO tbl_faculty_log (pairLog, RFID, schedule_ID, subjectIn, facultyName,
                                                        room, logType, logTime, logDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                      $SQL->bind_param('isissssss',$db_field2['log_ID'], $RFIDCODE, $scheduleID, $subjectCoursedescription, $facultyName,
                                                                                    $scheduleRoom, $logType, $timeLog, $dateToday);
                      $SQL->execute();
                      //////END
                      //////UPDATING LOGSTATUS IF TI  ME IN IS DONE
                      $SQL = $db_found->prepare("UPDATE tbl_faculty_log SET pairLog=? WHERE log_ID=?");
                      $SQL->bind_param('ii',  $db_field2['log_ID'], $db_field2['log_ID']);
                      $SQL->execute();
                      //////END   
                      //////UPDATING LOGSTATUS IF TI  ME IN IS DONE
                      $SQL = $db_found->prepare("UPDATE tbl_schedule SET logStatus=?, messageStatus=? WHERE schedule_ID=?");
                      $SQL->bind_param('isi',  $logStatus, $messageStatus, $scheduleID);
                      $SQL->execute();
                      //////END              
                      break;
                    }
                  }
                }
                else if($scheduleLogstatus == 2)
                {
                  $message = "You can do <code>Time-In </code> and <code>Time-out </code> once per day!";
                  $messageType = 2;
                  MessageDisplay($message, $messageType); 
                }
                else if($scheduleLogstatus == 3)
                {
                  $message = "Class not completed, no time out within 20 minutes from the end of class.";
                  $messageType = 2;
                  MessageDisplay($message, $messageType); 
                }  
                else if($scheduleLogstatus == 4)
                {
                  $message = "Class is Done!";
                  $messageType = 2;
                  MessageDisplay($message, $messageType); 
                }
              }
              else
              {
                $message = "Schedule is on the other day.";
                $messageType = 2;
                MessageDisplay($message, $messageType); 

                $facultyImage = file_get_contents("../Images/defaultphoto.png");
                $facultyImage = base64_encode($facultyImage);
                $facultyStatus = "";
              }
            }
          }
          $SQL->close();
          $db_found->close();
        }
      }
      else if(isset($_POST['id']))
      {     
        $ID = $_POST['id']; //value of RFID code scanned
        $atype = "Faculty";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          //////SEARCHING ACCOUNT
          $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE RFID = ? AND accountType = ?');
          $SQL->bind_param('ss', $ID, $atype);
          $SQL->execute();
          //////END
          $result = $SQL->get_result();
          if ($result->num_rows > 0) 
          {    
            while ( $db_field = $result->fetch_assoc() ) 
            {
              //////STORE TEMPORARILY THE ACCOUNT DATA
              if(empty($db_field['middleName']))
              {
                $facultyName = $db_field['lastName'].", ".$db_field['firstName'];
              }
              else
              {
                $facultyName = $db_field['lastName'].", ".$db_field['firstName']." ".$db_field['middleName'][0]."."; //faculty full name
              }    
              $RFIDCODE = $ID; 
              $facultyImage = $db_field['accountImage'];
              $facultyStatus = $db_field['accountStatus'];        
              //////END
            }
          }   
          else
          {
            $message = "<code class='h3'>RFID</code> not Found!";
            $messageType = 3;
            MessageDisplay($message, $messageType); 
          }
          if($facultyStatus == "Inactive")
          {
            $message = "Account is Inactive! Please contact admin.";
            $messageType = 3;
            MessageDisplay($message, $messageType); 
          }   
        }
      }
    }
  }
	
  
  function MessageDisplay($message,$messageType)
  {
    ?>
      <div class="row">
        <div class="col-2">
        </div>
        <div class="col-10">
          <?PHP
            if($messageType == 1)
            {
              echo '<div class="alert alert-success alert-dismissible fade show w-75 text-center border-success mt-2" id="alert" style="position: absolute;z-index: 1;">';
            }
            else if($messageType == 2)
            {
              echo '<div class="alert alert-warning alert-dismissible fade show w-75 text-center border-warning mt-2" id="alert" style="position: absolute;z-index: 1;">';
            }
            else if($messageType == 3)
            {
              echo '<div class="alert alert-danger alert-dismissible fade show w-75 text-center border-danger mt-2" id="alert" style="position: absolute;z-index: 1;">';
            }
          ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong class="h4">
              <?PHP
                printf($message);
              ?>
            </strong> 
          </div>
        </div>
      </div>
    <?PHP
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LOG</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?PHP include '../include/plugins.php' ?>
    <!-- LOCAL TOOLS -->
    <link rel="stylesheet" href="log.css" type="text/css">     
  </head>
  <body style="background-color: #085C2E">
    <form method ="POST" enctype="multipart/form-data" ACTION ="log.php">
      <input type="text" style="opacity: 0" size="1" autocomplete="off" type="text" id="faculty_id" name ='id' value="" 
                                                                            onblur="this.focus()" REQUIRED autofocus>
    </form>
    <div class="container-fluid"> 
      <div class="container-fluid rounded-lg" width="100%" style="background-color: #a2a194">
        <div onKeyPress="return checkSubmit(event)"></div>
        <div class="row">
          <div class="col">
            
            <div class="row text-white h-100 justify-content-center align-items-center">

              <div class="col text-center p-0">
                <div style="background-color: #212121" class="rounded-top">
                  <!-- DATE -->
                  <div id="date_id" class="h3 font-weight-bolder pt-2"></div>
                  <!-- TIME -->
                  <div id="time_id" class="display-3 font-weight-bolder pb-2"></div>            
                </div>
              </div>
            </div>
            
          </div>
        </div>
        <div class="row" style="border-top:9px solid #085C2E;">
          <div class="col-2" style="border-left:3px solid #212121;">         
          </div>
          <div class="col-3 text-center text-justify rounded-left bg-light"  style="border-right:3px solid #a2a194;">
            <div class="form-group">
              <?PHP  print '<img class="pt-3 img-fluid" height="200" width="200" src="data:image;base64,'. $facultyImage .' ">'; ?>
            </div>       
          </div>    
          <div class="col-5 rounded-right bg-light">
            <div class="form-group" style="border-bottom:1px solid #F0C529;">
              <label>Name</label>
              <input type="text" class="form-control-plaintext border-bottom" name ='name' value="<?PHP print $facultyName;?>">
            </div>
            <div class="form-group" style="border-bottom:1px solid #F0C529;">
              <label>RFID#</label>
              <input type="text" class="form-control-plaintext border-bottom" name ='tag' value="<?PHP print $RFIDCODE;?>">
            </div>
            <div class="form-group" style="border-bottom:1px solid #F0C529;">
              <label>Status</label>
              <input type="text" class="form-control-plaintext border-bottom" name ='status' value="<?PHP print $facultyStatus;?>">
            </div>          
          </div>  
          <div class="col-2" style="border-right:3px solid #212121;">         
          </div>
        </div>
        
        <div class="row" style="border-top:9px solid #085C2E;">
          <div class="col col-md-12 border rounded-lg">
            <div class="table-responsive">
              <table id="log_list_id" class="table table-sm table-hover text-center"> 
                <thead>
                  <tr>  
                    <td></td>
                    <th scope="col">Name</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Schedule</th>
                    <th scope="col">Room</th>
                    <th scope="col">Log Time</th>
                    <th scope="col">Log Type</th> 
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
                            $dateTodayCheck = date('m/d/Y');
                            if($dateTodayCheck == $db_field['logDate'])
                            {
                              print("<tr data-toggle='modal' data-target='#verify-modal'>");
                              print("<td>".$db_field['log_ID']."</td>");
                              print("<td scope='row'>".$db_field['facultyName']."</td>");
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
                              print("<td>".$db_field['room']."</td>");
                              print("<td>".date("h:i:s A", strtotime($db_field['logTime']))."</td>");
                              print("<td>".$db_field['logType']."</td>");                                       
                              print("<td>".$db_field['logDate']."</td>");  
                              print("<td></td></tr>");   
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
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- CONFIRM MODAL -->
        <div class="modal fade" id="confirmation-form-modal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header" style="background-color: #F0C529">
                <h3 class="modal-title">Confirmation</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p class='h5 font-italic text-left alert-warning font-weight-bolder'>Warning! You still have
                  <?PHP 
                  if($countWaitingclass > 1)
                  {
                    echo " $countWaitingclass classes before this: </p>";
                  }
                  else
                  {
                    echo "$countWaitingclass class before this: </p>";
                  }        
                  ?>
                  <hr class="dropdown-divider">
                  <?PHP
                  sort($waitingClass);
                  foreach($waitingClass as $sched)
                  {
                    echo "<p class='text-left'> $sched</p>";
                  }
                  ?>
                  <hr class="dropdown-divider">
                  <p class='h6 font-italic text-left alert-danger font-weight-bolder'>If you wish to proceed,                                                                           
                  <?PHP 
                  if($countWaitingclass > 1)
                  {
                    echo "these classes";
                  }
                  else
                  {
                    echo "this class";
                  }        
                  ?>
                  will be mark as absent!</p>
              </div>
              <div class="modal-footer">
                <form method ="POST" enctype="multipart/form-data" ACTION ="log.php" class="form-disable">
                  <input type="text" name ='proceed' readonly hidden>
                  <input type="submit" class="btn btn-success" name="proceed" value="Proceed">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>       
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL LIST OF SUBJECT -->
        <div class="modal fade" id="subject-form-modal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header" style="background-color: #F0C529">
                <h3 class="modal-title">Select Schedule</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <table id="subject_list_id" class="table table-sm table-hover text-center" style="font-size: 13px;background: #a2a194"> 
                  <thead>
                    <tr>
                      <td></td>
                      <th scope="col">Subject</th>
                      <th scope="col">Start Time</th>
                      <th scope="col">End Time</th>
                      <th scope="col">Days</th>
                      <th scope="col">Room</th>
                      <th scope="col">Status</th>
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
                          $SQL = $db_found->prepare('SELECT * FROM tbl_schedule WHERE RFID = ?');
                          $SQL->bind_param('i', $RFIDCODE);  
                          $SQL->execute();
                          $result = $SQL->get_result();
                  
                          if ($result->num_rows > 0) 
                          {
                            while ( $db_field = $result->fetch_assoc() ) 
                            {                   
                              print("<tr data-toggle='modal' data-target='#manage-modal'>");
                              print("<td>".$db_field['schedule_ID']."</td>");

                              $SQL1 = $db_found->prepare('SELECT * FROM tbl_subject WHERE subject_ID = ?');
                              $SQL1->bind_param('i', $db_field['subject_ID']);  
                              $SQL1->execute();
                              $result1 = $SQL1->get_result();
                      
                              if ($result1->num_rows > 0) 
                              {
                                while ( $db_field1 = $result1->fetch_assoc() ) 
                                {                 
                                  print("<td>".$db_field1['description']."</td>");  
                                }
                              }
                              print("<td>".date("h:i:s A", strtotime($db_field['startTime']))."</td>");
                              print("<td>".date("h:i:s A", strtotime($db_field['endTime']))."</td>");
                              print("<td>");
                              if (strpos($db_field['daySchedule'], "Mon") !== false)
                              {
                                print("M");
                              }
                              if (strpos($db_field['daySchedule'], "Tue") !== false) 
                              {
                                print("T");
                              }
                              if (strpos($db_field['daySchedule'], "Wed") !== false) 
                              {
                                print("W");
                              }
                              if (strpos($db_field['daySchedule'], "Thu") !== false) 
                              {
                                print("Th");
                              }
                              if (strpos($db_field['daySchedule'], "Fri") !== false) 
                              {
                                print("F");
                              }
                              if (strpos($db_field['daySchedule'], "Sat") !== false) 
                              {
                                print("Sa");
                              }
                              print("</td>");
                              print("<td>".$db_field['room']."</td>");   
                              if($db_field['logStatus'] == 1)
                              {
                                print("<td class='bg-secondary'>".$db_field['messageStatus']."</td>"); 
                              }
                              else if($db_field['logStatus'] == 2)
                              {
                                print("<td class='bg-success'>".$db_field['messageStatus']."</td>"); 
                              }
                              else if($db_field['logStatus'] == 3)
                              { 
                                print("<td class='bg-warning'>".$db_field['messageStatus']."</td>"); ;
                              }
                              else if($db_field['logStatus'] == 4)
                              { 
                                print("<td class='bg-danger'>".$db_field['messageStatus']."</td>"); ;
                              }
                              else
                              {
                                print("<td>".$db_field['messageStatus']."</td>"); 
                              }
                              print("<td></td></tr>");                              
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
              <div class="modal-footer">
                <form method ="POST" enctype="multipart/form-data" ACTION ="log.php" class="form-disable">
                  <input type="text" class="form-control mb-2" id="rfid" name ='rfid' value="<?PHP print $RFIDCODE; ?>" readonly hidden>
                  <input type="text" class="form-control mb-2" id="choose_subject_id" name ='choose_subject' readonly hidden>
                  <input type="submit" class="btn btn-success" id="confirm_id">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>       
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- LOCAL TOOLS -->
    <script src="log.js"></script>
    <script src="../preventDoubleSubmission.js"></script>
  </body>
</html>
<?PHP
  if(isset($_POST['id']))
  {
    if($facultyStatus == "Active")
    {
?>
      <script>
        $( document ).ready(function() 
        {
          $('#subject-form-modal').modal('show');
        });
      </script>
<?PHP
    }
  }

  if(isset($_POST['rfid']) && isset($_POST['choose_subject']))
  {
    if($countWaitingclass > 0)
    {
      
    ?>
      <script>
      $( document ).ready(function() 
      {
        $('#confirmation-form-modal').modal('show');
      });
    </script>
    <?PHP
    $countWaitingclass = 0;
    }
  }

?>
<script>
  function checkSubmit(e) 
  {
    if(e && e.keyCode == 13) 
    {
        document.forms[0].submit();
    }
  }
  function showTime()
  {
    var date = new Date();
    var dayofweek = "<?PHP echo date('l');?>"; 
    var month = "<?PHP echo date('F');?>"; 
    var dayinmonth = date.getDate();
    var year = date.getFullYear();

    var hour = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();
    var session = "AM";
    if(hour == 0) 
    {
      hour = 12;
    }
    if( hour > 12)
    {
      hour = hour - 12;
      session = "PM";
    }

    hour = (hour < 10) ? "0" + hour : hour;
    minute = (minute < 10) ? "0" + minute : minute;
    second = (second < 10) ? "0" + second : second;

    var dateformat = dayofweek + " | " + month + " " + dayinmonth + ", " + year;
    var time = hour + ":" + minute + ":" + second + " " + session;

    document.getElementById("date_id").innerHTML = dateformat;
    document.getElementById("date_id").textContent = dateformat;
    document.getElementById("time_id").innerHTML = time;
    document.getElementById("time_id").textContent = time;
    setTimeout(showTime, 1000);
  }
  showTime();
</script>

<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $description = '';
  $units = '';
  $stime = '';
  $etime = '';
  $day = '';
  $room = '';

  $RFID = $_SESSION['RFID'];

  $fname = $_SESSION['facultyfname'];
  $lname = $_SESSION['facultylname'];
  $mname = $_SESSION['facultymname'];
  $image = $_SESSION['facultyimage'];

  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      if (isset($_POST['addschedule']))
      {
        if($_POST['subject'] != "Select")
        {
          if(!empty($RFID))
          {
            $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
            if ($db_found) 
            {
              $SQL = $db_found->prepare("INSERT INTO tbl_schedule (RFID, subject_ID, startTime, endTime,daySchedule,
                                                                                room) VALUES (?, ?, ?, ?, ?, ?)");

              $SQL->bind_param('iissss',  $RFID, $_POST['subject'], $_POST['starttime'], $_POST['endtime'], $_POST['day'], $_POST['room']);
              $SQL->execute();

              $SQL->close();
              $db_found->close();
              $message = "Schedule Added.";
              $messageType = 1;
              MessageDisplay($message, $messageType);
            }
          }
          else
          {
            $message = "Faculty does not have RFID!";
            $messageType = 2;
            MessageDisplay($message, $messageType);
          }
        } 
        else
        {
          $message = "Fill-up nessesary data!.";
          $messageType = 2;
          MessageDisplay($message, $messageType);
        }
      } 
      else if (isset($_POST['save']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $scheduleID = $_POST['schedule'];
          $edit_subject = $_POST['edit_subject'];
          $edit_start_time = $_POST['edit_start_time'];
          $edit_end_time = $_POST['edit_end_time'];
          $edit_day = $_POST['edit_day'];
          $edit_room = $_POST['edit_room'];
          $SQL = $db_found->prepare("UPDATE tbl_schedule SET subject_ID=?, startTime=?, endTime=?, daySchedule=?, room=? WHERE schedule_ID=?");
          $SQL->bind_param('issssi',  $edit_subject, $edit_start_time, $edit_end_time, $edit_day, $edit_room, $scheduleID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Saved.";
          $messageType = 1;
          MessageDisplay($message, $messageType);
        }
        else
        {
          $message = "Database not found.";
          $messageType = 3;
          MessageDisplay($message, $messageType);
        }
      }
      elseif (isset($_POST['dropschedule']))
      {
        $dropSchedule = $_POST['schedule'];
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $SQL = $db_found->prepare("DELETE FROM tbl_schedule WHERE schedule_ID=?");

          $SQL->bind_param('i', $dropSchedule);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Schedule Deleted!";
          $messageType = 3;
          MessageDisplay($message, $messageType);
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
    <title>Admin - Manage Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarAdmin.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="admin.css" type="text/css"> 
  </head>
  <body>
    <div class="container-fluid mt-3">
      <div class="container-fluid" width="100%">
        <div class="row">
          <div class="col-6 text-left">
              <a class="h2" href="manageschedule.php"><span class="fas fa-calendar-alt"></span> Schedule</a>
          </div>
          <div class="col-6 text-right">
            <p><?PHP print $lname.", ".$fname ?>
            <?PHP print '<img class="rounded-lg img-fluid" height="40" width="40" src="data:image;base64,'. $image .' ">'; ?>
            </p>
          </div>
        </div>
        <div class="card">
          <div class="card-header navbar navbar-expand-sm">
            <ul class="navbar-nav">
              <li class="nav-item">
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#add_schedule_id">
                  <span class="fas fa-plus mr-1"></span> Add
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="jQuery('#schedule_list_id').print()">
                  <span class="fas fa-print mr-1"></span>Print Preview
                </button>
                <a href="manageprofile.php" class="btn btn-secondary">
                  <span class="fas fa-arrow-left"></span> Back
                </a>
              </li>

            </ul>
          </div>
          <div class="row">
            <div class="col">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="schedule_list_id" class="table table-sm table-hover text-center" width="100%"> 
                    <thead>
                      <tr>
                        <th colspan="7" class="border border-0" style="background-color: #a2a194">
                          <div class="bg-light row text-center d-none d-print-block">
                            <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                            <p class="h5"><?PHP print $fname." ".$lname." - Schedule" ?></p>
                            <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                          </div>
                        </th>
                      </tr>
                      <tr>
                        <td></td>
                        <th scope="col">Subject</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">End Time</th>
                        <th scope="col">Day</th>
                        <th scope="col">Room</th>
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
                            $SQL = $db_found->prepare('SELECT * FROM tbl_schedule');
                            
                            $SQL->execute();
                    
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                   
                                if($db_field['RFID'] == $RFID)
                                {
                                  ?>
                                  <tr data-toggle='modal' data-target='#edit_schedule_id'>
                                  <td scope='row'><?PHP print $db_field['schedule_ID'] ?></td>

                                  <?PHP
                                  $SQL1 = $db_found->prepare('SELECT * FROM tbl_subject WHERE subject_ID = ?');

                                  $SQL1->bind_param('i', $db_field['subject_ID']);  
                                  $SQL1->execute();

                                  $result1 = $SQL1->get_result();

                                  if ($result1->num_rows > 0) 
                                  {
                                    while ( $db_field1 = $result1->fetch_assoc() ) 
                                    {
                                      ?>
                                      <td><?PHP print $db_field1['subjectCode']." - ".$db_field1['description'] ?></td>
                                      <?PHP
                                    }

                                  }
                                  ?>
                                  <td><?PHP print date("h:i:s A", strtotime($db_field['startTime'])) ?></td>
                                  <td><?PHP print date("h:i:s A", strtotime($db_field['endTime'])) ?></td>
                                  <?PHP
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
                                  ?>
                                  <td><?PHP print $db_field['room'] ?></td>

                                  <td><?PHP print $db_field['RFID'] ?></td></tr>
                                  <?PHP
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
          </div>
        </div>
        <!-- Modal Add schedule -->
        <div class="modal fade" id="add_schedule_id" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <form method ="POST" enctype="multipart/form-data" ACTION ="manageschedule.php">
                <div class="modal-header">
                  <h5 class="modal-title">Add Schedule</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select class="form-control" id="subject_id" name="subject" onchange="populate('subject_id')" REQUIRED>
                      <option>Select</option>
                      <?PHP
                        if($connected == true)
                        {
                          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
                          if ($db_found) 
                          { 
                            $SQL = $db_found->prepare('SELECT * FROM tbl_subject');                        
                            $SQL->execute();                     
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                    
                                printf("<option value ='".$db_field['subject_ID']."'>".$db_field['subjectCode']."</option>");
                              }
                            }
                            else 
                            {
                              $message = "No subject list.";
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
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="description_id">Description</label>
                    <input type="text" class="form-control" id="description_id" name ='description' value="<?PHP print $description;?>" READONLY>
                  </div>
                  <div class="form-group">
                    <label for="unit_id">Units</label>
                    <input type="text" class="form-control" id="unit_id" name ='unit' value="<?PHP print $units;?>" READONLY>
                  </div>
                  <div class="form-group">
                    <label for="start-time_id">Start Time</label>
                    <input type="time" class="form-control" id="start_time_id" name ='starttime' value="<?PHP print $stime;?>" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="end_time_id">End Time</label>
                    <input type="time" class="form-control" id="end_time_id" name ='endtime' value="<?PHP print $etime;?>" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="day_id">Day</label>
                    <select class="form-control" id="day_id" name="day" value="<?PHP print $day;?>" REQUIRED>
                      <option value="MonWedFri">MWF</option>
                      <option value="MonWed">MW</option>
                      <option value="WedFri">WF</option>
                      <option value="MonFri">MF</option>
                      <option value="Mon">M</option>
                      <option value="Wed">W</option>
                      <option value="Fri">F</option>
                      <option value="TueThu">TTh</option>
                      <option value="Tue">T</option>
                      <option value="Thu">Th</option>
                      <option value="Sat">Sat</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="room_id">Room</label>
                    <select class="form-control" id="room_id" name="room">
                      <?PHP
                        if($connected == true)
                        {
                          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
                          if ($db_found) 
                          { 
                            $SQL = $db_found->prepare('SELECT * FROM tbl_room');                        
                            $SQL->execute();                     
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                    
                                printf("<option value ='".$db_field['roomCode']."'>".$db_field['roomCode']."</option>");
                              }
                            }
                            else 
                            {
                              $message = "No room list.";
                              $messageType = 3;
                              MessageDisplay($message, $messageType);
                            }
                            $SQL->close();
                            $db_found->close();
                          }
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-success" name="addschedule">Add</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal Edit schedule -->
        <div class="modal fade" id="edit_schedule_id" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <form method ="POST" enctype="multipart/form-data" ACTION ="manageschedule.php">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Schedule</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" class="form-control mb-2" id="schedule_id" name ='schedule' readonly hidden>
                  </div>
                  <div class="form-group">
                    <label for="edit_subject_id">Subject</label>
                    <select class="form-control" id = "edit_subject_id" name = "edit_subject" onchange="editpopulate('edit_subject_id')">
                      <?PHP
                        if($connected == true)
                        {
                          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
                          if ($db_found) 
                          { 
                            $SQL = $db_found->prepare('SELECT * FROM tbl_subject');                        
                            $SQL->execute();                     
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                    
                                printf("<option value ='".$db_field['subject_ID']."'>".$db_field['subjectCode']."</option>");
                              }
                            }
                            else 
                            {
                              $message = "No subject list.";
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
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="edit_description_id">Description</label>
                    <input type="text" class="form-control" id="edit_description_id" name ='edit_description' value="" READONLY>
                  </div>
                  <div class="form-group">
                    <label for="edit_unit_id">Units</label>
                    <input type="text" class="form-control" id="edit_unit_id" name ='edit_unit' value="" READONLY>
                  </div>
                  <div class="form-group">
                    <label for="edit_start_time_id">Start Time</label>
                    <input type="time" class="form-control" id="edit_start_time_id" name ='edit_start_time' value="12:30:00 PM">
                  </div>
                  <div class="form-group">
                    <label for="edit_end_time_id">End Time</label>
                    <input type="time" class="form-control" id="edit_end_time_id" name ='edit_end_time' value="">
                  </div>
                  <div class="form-group">
                    <label for="edit_day_id">Day</label>
                    <select class="form-control" id="edit_day_id" name="edit_day">
                      <option value="MonWedFri">MWF</option>
                      <option value="MonWed">MW</option>
                      <option value="WedFri">WF</option>
                      <option value="MonFri">MF</option>
                      <option value="Mon">M</option>
                      <option value="Wed">W</option>
                      <option value="Fri">F</option>
                      <option value="TueThu">TTh</option>
                      <option value="Tue">T</option>
                      <option value="Thu">Th</option>
                      <option value="Sat">Sat</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="edit_room_id">Room</label>
                    <select class="form-control" id="edit_room_id" name="edit_room">
                      <?PHP
                        if($connected == true)
                        {
                          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
                          if ($db_found) 
                          { 
                            $SQL = $db_found->prepare('SELECT * FROM tbl_room');                        
                            $SQL->execute();                     
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                    
                                printf("<option value ='".$db_field['roomCode']."'>".$db_field['roomCode']."</option>");
                              }
                            }
                            else 
                            {
                              $message = "No room list.";
                              $messageType = 3;
                              MessageDisplay($message, $messageType);
                            }
                            $SQL->close();
                            $db_found->close();
                          }
                        } 
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-danger ml-auto" name="dropschedule">Delete</button>
                  <button type="submit" class="btn btn-success" name = "save">Save</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- LOCAL TOOLS -->
    <?PHP include '../include/manageschedulejs.php' ?>
    <script src="../Index/index.js"></script>      
    <script src="admin.js"></script>
    </body>
</html>

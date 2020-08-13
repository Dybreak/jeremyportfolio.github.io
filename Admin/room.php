<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $roomcode ='';

  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      if (isset($_POST['addroom']))
      {
        $addroomcode = $_POST['addroomCode'];
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $SQL = $db_found->prepare("INSERT INTO tbl_room (roomCode) VALUES (?)");
          $SQL->bind_param('s', $addroomcode);
          $SQL->execute();
          
          $SQL->close();
          $db_found->close();
          $message = "Room Added Successfully!";
          $messageType = 1;
          MessageDisplay($message, $messageType);
        }
      } 
      else if (isset($_POST['editroom']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $editroomID = $_POST['roomID'];
          $editroomcode = $_POST['editroomCode'];
          $SQL = $db_found->prepare("UPDATE tbl_room SET roomCode=? WHERE room_ID=?");
          $SQL->bind_param('si',  $editroomcode, $editroomID);
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
      elseif (isset($_POST['droproom']))
      {
        $droproomID = $_POST['roomID'];
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $SQL = $db_found->prepare("DELETE FROM tbl_room WHERE room_ID=?");

          $SQL->bind_param('i', $droproomID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Room deleted!";
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
    <title>Admin - Room List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarAdmin.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="admin.css" type="text/css"> 
  </head>
  <body>
    <div class="container-fluid mt-3 w-75">
      <div class="container-fluid w-50">
        <a class="h2" href="room.php"><span class="fas fa-school"></span> Rooms</a>

        <div class="card">
          <div class="card-header navbar navbar-expand-sm">
            <ul class="navbar-nav">
              <li class="nav-item">
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#addroom_form_modal">
                  <span class="fas fa-plus mr-1"></span> Add
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="jQuery('#room_list_id').print()">
                  <span class="fas fa-print mr-1"></span>Print Preview
                </button>
                <a href="admin.php" class="btn btn-secondary">
                  <span class="fas fa-arrow-left"></span> Back
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="table-responsive">
                  <table id="room_list_id" class="table table-sm table-hover text-center" width="100%">
                    <thead>
                      <tr>
                        <th colspan="7" class="border border-0" style="background-color: #a2a194">
                          <div class="bg-light row text-center d-none d-print-block">
                            <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                            <p class="h5">ROOMS</p>
                            <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                          </div>
                        </th>
                      </tr>
                      <tr>
                        <th scope="col">room_ID</th>
                        <th scope="col">Room Code</th>
                      </tr>
                    </thead>

                    <tbody>         
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
                                ?>
                                <tr data-toggle='modal' data-target='#edit_room_modal'>
                                <td scope='row'><?PHP print $db_field['room_ID'] ?></td>
                                <td><?PHP print $db_field['roomCode'] ?></td></tr> 
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
    </div>

    <!-- Modal Add Room -->
    <div class="modal fade" id="addroom_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="room.php">
            <div class="modal-header">
              <h5 class="modal-title">Add Room</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="room_code_id">Room Code</label>
                <input type="text" class="form-control" id="room_code_id" name ='addroomCode' value="<?PHP print $roomcode;?>"                    
                                                                                        placeholder="Enter Room Code">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="addroom">Add</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Edit Room -->
    <div class="modal fade" id="edit_room_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="room.php">
            <div class="modal-header" style="background-color: #F0C529">
              <h5 class="modal-title">Edit Room</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="text" class="form-control" id="edit_room_id" name ='roomID' readonly hidden>
              <div class="form-group">
                <label for="subject_code_id">Room Code</label>
                <input type="text" class="form-control" id="edit_room_code_id" name ='editroomCode' value="" placeholder="Enter Room Code">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger ml-auto" name="droproom">Delete</button>
              <button type="submit" class="btn btn-success" name="editroom">Save</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- LOCAL TOOLS -->
    <?PHP include '../include/roomjs.php' ?>
    <script src="admin.js"></script>
  </body>
</html>
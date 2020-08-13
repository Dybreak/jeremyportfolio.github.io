<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $subjectcode = '';
  $subjectdescription = '';
  $subjectunits = '';

  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      if (isset($_POST['addsubject']))
      {
        $subjectcode = $_POST['subjectCode'];
        $subjectdescription = $_POST['subjectDescription'];
        $subjectunits = $_POST['subjectUnits'];
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $SQL = $db_found->prepare("INSERT INTO tbl_subject (subjectCode, description, units) VALUES (?, ?, ?)");
          $SQL->bind_param('ssi', $subjectcode, $subjectdescription, $subjectunits);
          $SQL->execute();
          
          $SQL->close();
          $db_found->close();
          $message = "Subject Added Successfully!";
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
      else if (isset($_POST['editsubject']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $subjectID = $_POST['editID'];
          $subjectcode = $_POST['editsubjectCode'];
          $subjectdescription = $_POST['editsubjectDescription'];
          $subjectunits = $_POST['editsubjectUnits'];
          $SQL = $db_found->prepare("UPDATE tbl_subject SET subjectCode=?, description=?, units=? WHERE subject_ID=?");
          $SQL->bind_param('ssii',  $subjectcode, $subjectdescription, $subjectunits, $subjectID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Saved.";
          $messageType = 1;
          MessageDisplay($message, $messageType);
        }
      }  
      elseif (isset($_POST['dropsubject']))
      {
        $subjectID = $_POST['editID'];
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {   
          $SQL = $db_found->prepare("DELETE FROM tbl_subject WHERE subject_ID=?");

          $SQL->bind_param('i', $subjectID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();
          $message = "Subject Deleted!";
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
    <title>Admin - Subject List</title>
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
      <div class="container-fluid w-75">
        <a class="h2" href="subject.php"><span class="fas fa-book"></span> Subjects</a>
        <div class="card">
          <div class="card-header navbar navbar-expand-sm">
            <ul class="navbar-nav">
              <li class="nav-item">
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#addsubject_form_modal">
                  <span class="fas fa-plus mr-1"></span> Add
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="jQuery('#subject_list_id').print()">
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
                  <table id="subject_list_id" class="table table-sm table-hover text-center" width="100%">
                    <thead>
                      <tr>
                        <th colspan="7" class="border border-0" style="background-color: #a2a194">
                          <div class="bg-light row text-center d-none d-print-block">
                            <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                            <p class="h5">SUBJECTS</p>
                            <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                          </div>
                        </th>
                      </tr>
                      <tr>
                        <th scope="col">subject_ID</th>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Description</th>
                        <th scope="col">Units</th>
                      </tr>
                    </thead>
                    <tbody>      
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
                                ?>
                                <tr data-toggle='modal' data-target='#edit_subject_modal'>
                                <td scope='row'><?PHP print $db_field['subject_ID'] ?></td>
                                <td><?PHP print $db_field['subjectCode'] ?></td>
                                <td><?PHP print $db_field['description'] ?></td>
                                <td><?PHP print $db_field['units'] ?></td></tr> 
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
    <!-- Modal Add subject -->
    <div class="modal fade" id="addsubject_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="subject.php">
            <div class="modal-header">
              <h5 class="modal-title">Add Subject</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="subject_code_id">Subject Code</label>
                <input type="text" class="form-control" id="subject_code_id" name ='subjectCode' value="<?PHP print $subjectcode;?>"                    
                                                                                        placeholder="Enter Subject Code">
              </div>
              <div class="form-group">
                <label for="subject_description_id">Description</label>
                <input type="text" class="form-control" id="subject_description_id" name ='subjectDescription' value="<?PHP print $subjectdescription;?>"                    
                                                                                        placeholder="Enter Description">
              </div>
              <div class="form-group">
                <label for="subject_unit_id">Unit/s</label>
                <input type="text" class="form-control" id="subject_unit_id" name ='subjectUnits' value="<?PHP print $subjectunits;?>"                    
                                                                                        placeholder="Enter Unit/s">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="addsubject">Add</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Edit subject -->
    <div class="modal fade" id="edit_subject_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="subject.php">
            <div class="modal-header">
              <h5 class="modal-title">Edit Subject</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="text" class="form-control" id="edit_subject_id" name ='editID' readonly hidden>
              <div class="form-group">
                <label for="subject_code_id">Subject Code</label>
                <input type="text" class="form-control" id="edit_subject_code_id" name ='editsubjectCode' value=""                    
                                                                                        placeholder="Enter Subject Code">
              </div>
              <div class="form-group">
                <label for="subject_description_id">Description</label>
                <input type="text" class="form-control" id="edit_subject_description_id" name ='editsubjectDescription' value=""                    
                                                                                        placeholder="Enter Description">
              </div>
              <div class="form-group">
                <label for="subject_unit_id">Username</label>
                <input type="text" class="form-control" id="edit_subject_unit_id" name ='editsubjectUnits' value=""                    
                                                                                        placeholder="Enter Unit/s">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger ml-auto" name="dropsubject">Delete</button>
              <button type="submit" class="btn btn-success" name="editsubject">Save</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- LOCAL TOOLS -->
    <?PHP include '../include/subjectjs.php' ?>
    <script src="admin.js"></script>
  </body>
</html>
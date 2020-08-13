<?PHP
  require '../../../../configure.php'; //connect to data base
  include '../restrictADMIN.php'; //restrict anonymous
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $fname = '';
  $lname = '';
  $mname = '';
  $cnumber = '';
  $email = '';
  $status = '';
  $atype = '';
  $uname = '';
  $pword = '';
  $cpword = '';
  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      if(isset($_POST['editschedule']))
      {
        $_SESSION['ID'] = $_POST['ID'];
        header ("Location: manageprofile.php"); 
      }
      else if (isset($_POST['addaccountbutton']))
      {
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $mname = $_POST['middlename'];
        $cnumber = $_POST['cnumber'];
        $email = $_POST['email'];
        $status = "Active";
        $atype = $_POST['atype'];
        $uname = $_POST['username'];
        $pword = $_POST['password'];
        $cpword = $_POST['cpassword'];
        if($pword == $cpword)
        {
          //Store image chosen
          if ( filesize( $_FILES['image']['tmp_name'] ) )
          {
            $image = addslashes($_FILES['image']['tmp_name']);
            $image = file_get_contents($image);
            $image = base64_encode($image);
          }
          //Store default image
          else 
          {
            $image = file_get_contents("../Images/defaultphoto.png");
            $image = base64_encode($image);
          }
          $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database); 
          if ($db_found) 
          {   
            $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE accountUsername = ?');
            $SQL->bind_param('s', $uname);
            $SQL->execute();
            $result = $SQL->get_result();
    
            if ($result->num_rows > 0) 
            {
              $message = "Username already taken!";
              $messageType = 3;
              MessageDisplay($message, $messageType);
            }
            else 
            {
              $phash = password_hash($pword, PASSWORD_DEFAULT);
              $SQL = $db_found->prepare("INSERT INTO tbl_account (firstName, middleName, lastName, contactNumber, emailAddress,
          accountStatus, accountType, accountUsername, accountPassword, accountImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $SQL->bind_param('ssssssssss', $fname, $mname, $lname, $cnumber, $email, $status, $atype, $uname, $phash, $image);
              $SQL->execute();
              
              $SQL->close();
              $db_found->close();
              $message = "Account Registered Successfully!";
              $messageType = 1;
              MessageDisplay($message, $messageType);

            }    
          }
        }     
        else
        {
          $message = "Password does not match!";
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
    <title>Admin - Account List</title>
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
      <div class="container-fluid w-75">
        <a class="h2" href="accountlist.php"><span class="fas fa-users"></span> Accounts</a>

        <div class="card">
          <div class="card-header navbar navbar-expand-sm">
            <ul class="navbar-nav">             
              <li class="nav-item">
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#signup_form_modal">
                  <span class="fas fa-plus mr-1"></span> Add
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="jQuery('#account_list_id').print()">
                  <span class="fas fa-print mr-1"></span>Print Preview
                </button>
                <a href="admin.php" class="btn btn-secondary">
                  <span class="fas fa-arrow-left"></span> Back
                </a>
              </li>
            </ul>
            <ul class="navbar-nav ml-auto">
              <form method ="POST" enctype="multipart/form-data" ACTION ="accountlist.php"> 
              <li class="nav-item">
                <input type="text" class="form-control mb-2" id="id" name ='ID' readonly hidden>
                <input type="submit" class="btn btn-lg btn-warning" id="manage_account_id" name="editschedule" value="Manage">
              </li>
              </form> 
            </ul>          
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="table-responsive">
                  <table id="account_list_id" class="table table-sm table-hover text-center" width="100%"> 
                    <thead>
                      <tr>
                        <th colspan="7" class="border border-0" style="background-color: #a2a194">
                          <div class="bg-light row text-center d-none d-print-block">
                            <p class="h2 font-weight-bolder">Computer Engineering Attendance Monitoring</p>
                            <p class="h5">REGISTERED FACULTY</p>
                            <p class="h4 font-weight-bolder">COLLEGE OF ENGINEERING</p>
                          </div>
                        </th>
                      </tr>
                      <tr>
                        <td></td>
                        <th scope="col">First name</th>
                        <th scope="col">Last name</th>
                        <th scope="col">Middle name</th>
                        <th scope="col">Account Type</th>
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
                            $SQL = $db_found->prepare('SELECT * FROM tbl_account');
                            
                            $SQL->execute();
                            $result = $SQL->get_result();
                    
                            if ($result->num_rows > 0) 
                            {
                              while ( $db_field = $result->fetch_assoc() ) 
                              {                   
                                if($db_field['accountStatus'] != "Pending")
                                {
                                  print("<tr data-toggle='modal' data-target='#manage-modal'>");
                                  print("<td>".$db_field['account_ID']."</td>");
                                  print("<td>".$db_field['firstName']."</td>");
                                  print("<td>".$db_field['lastName']."</td>");  
                                  print("<td>".$db_field['middleName']."</td>");
                                  print("<td>".$db_field['accountType']."</td>");
                                  print("<td>".$db_field['accountStatus']."</td>");
                                  printf("<td></td></tr>"); 
                                }      
                              }
                            }
                            else
                            {
                              $message = "No records found.";
                              $messageType = 1;
                              MessageDisplay($message, $messageType);
                            }
                            $SQL->close();
                            $db_found->close();
                          }
                          else
                          {
                            $message = "Database not found.";
                            $messageType = 1;
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

    <!-- Modal Sign Up -->
    <div class="modal fade" id="signup_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="accountlist.php">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">SIGN-UP FORM</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6 text-left">
                  <div class="form-group">
                    <label for="first-name-id">First Name</label>
                    <input type="text" class="form-control" id="first-name-id" name ='firstname' value="<?PHP print $fname;?>" 
                                                                                      placeholder="Enter Firstname" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="last-name-id">Last Name</label>
                    <input type="text" class="form-control" id="last-name-id" name ='lastname' value="<?PHP print $lname;?>" 
                                                                                      placeholder="Enter Lastname" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="middle-name-id">Middle Name</label>
                    <input type="text" class="form-control" id="middle-name-id" name ='middlename' value="<?PHP print $mname;?>" 
                                                                                        placeholder="Optional">
                  </div>
                  <div class="form-group">
                    <label for="contact-number-id">Contact Number</label>
                    <input type="text" class="form-control" id="contact-number-id" name ='cnumber' value="<?PHP print $cnumber;?>" 
                                                                                      placeholder="Enter Contact number" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="email-address-id">Email</label>
                    <input type="email" class="form-control" id="email-address-id" name ='email' value="<?PHP print $email;?>" 
                                                                                            placeholder="Enter Email" REQUIRED>
                  </div>  
                </div>
                <div class="col-6 text-left">
                  <div class="form-group">
                    <label for="account-type-id">Account Type:</label>
                    <select class="form-control" id="account-type-id" name="atype">
                      <option Value="Faculty"SELECTED>Faculty</option>
                      <option Value="READS">READS</option>
                      <option Value="Admin">Admin</option>
                      <option Value="Volunteer">Volunteer</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="user-name-id">Username</label>
                    <input type="text" class="form-control" id="user-name-id" name ='username' value="<?PHP print $uname;?>" 
                                                                                    placeholder="Enter User name" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="password-id">Password</label>
                    <input type="password" class="form-control" id="password-id" name ='password' value="<?PHP print $pword;?>" 
                                                                                      placeholder="Enter Password" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="verify-password-id">Verify Password</label>
                    <input type="password" class="form-control" id="verify-password-id" name ='cpassword' value="<?PHP print $cpword;?>" 
                                                                                            placeholder="Confirm Password" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="image-file-id">Choose image:</label>
                    <IMG SRC="" style="display:none" height="150" width="150" id="image">
                    <input type="file" class="form-control-file" id="image-file-id" name="image" onchange="showImage.call(this)">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success btn-block" name = "addaccountbutton">Add</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- LOCAL TOOLS -->
    <script src="admin.js"></script>
  </body>
</html>

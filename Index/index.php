<?PHP
  include '../restrictHome.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
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
      if (isset($_POST['signupbutton']))
      {
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $mname = $_POST['middlename'];
        $cnumber = $_POST['cnumber'];
        $email = $_POST['email'];
        $status = "Pending";
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
      
          if ($db_found) 
          {   
            $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE accountUsername = ?');
            $SQL->bind_param('s', $uname);
            $SQL->execute();
            $result = $SQL->get_result();
    
            if ($result->num_rows > 0) 
            {
              printf('<script> alert("Username already taken"); </script>');
            }
            else 
            {
              $phash = password_hash($pword, PASSWORD_DEFAULT);
              $SQL = $db_found->prepare("INSERT INTO tbl_account (firstName, middleName, lastName, contactNumber, emailAddress, accountStatus, accountType, accountUsername, accountPassword, accountImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $SQL->bind_param('ssssssssss', $fname, $mname, $lname, $cnumber, $email, $status, $atype, $uname, $phash, $image);
              $SQL->execute();
              $SQL->close();
              $db_found->close();
              printf('<script> alert("Account Registered Successfully!"); </script>');
              header( "refresh:0; url=index.php" );
            }    
          }
        }     
        else
        {
          printf('<script> alert("Password does not match"); </script>');
        }
      }      
      else if (isset($_POST['loginbutton']))
      {
        $uname = $_POST['username'];
        $pword = $_POST['password'];

        if ($db_found) 
        {
          $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE accountUsername = ?');
          $SQL->bind_param('s', $uname);
          $SQL->execute();
          $result = $SQL->get_result();

          if ($result->num_rows == 1) 
          {
            $db_field = $result->fetch_assoc();

            if (password_verify($pword, $db_field['accountPassword'])) 
            {   
              session_start();
              $_SESSION['login'] = "1";
              $_SESSION['account_ID'] = $db_field['account_ID'];
              $_SESSION['firstName'] = $db_field['firstName'];
              $_SESSION['lastName'] = $db_field['lastName'];
              $_SESSION['middleName'] = $db_field['middleName'];
              $_SESSION['contactNumber'] = $db_field['contactNumber'];
              $_SESSION['emailAddress'] = $db_field['emailAddress'];
              $_SESSION['accountStatus'] = $db_field['accountStatus'];
              $_SESSION['accountType'] = $db_field['accountType'];
              $_SESSION['accountPassword'] = $db_field['accountPassword'];
              $_SESSION['accountImage'] = $db_field['accountImage'];
              $_SESSION['RFID'] = $db_field['RFID'];

              printf('<script> alert("Login successful."); </script>');
              if($db_field['accountType'] == "Admin")
              {
                header ("Location: ../Admin/admin.php");   
              }
              else if($db_field['accountType'] == "Faculty")
              {        
                header ("Location: ../Faculty/faculty.php"); 
              }
              else if($db_field['accountType'] == "READS" || $db_field['accountType'] == "Volunteer")
              {      
                header ("Location: ../Reads/verify.php"); 
              }
            }
            else 
            {
              printf('<script> alert("Invalid Password!"); </script>');
              $_SESSION['login'] = '';
            }
          }
          else
          {
            printf('<script> alert("Invalid Username!"); </script>');
            $message = "user";
          }
        }
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Home Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarIndex.php' ?>

    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="index.css" type="text/css"> 
  </head>
  <body>
    <!-- Modal Sign Up -->
    <div class="modal fade" id="signup-form-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="index.php">
            <div class="modal-header" style="background-color: #F0C529">
              <h5 class="modal-title font-weight-bolder h3" id="exampleModalLabel">SIGN-UP FORM</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6 text-left">
                  <div class="form-group">
                    <label for="first-name-id">First Name</label>
                    <input type="text" autocomplete="off" class="form-control" id="first-name-id" name ='firstname' value="<?PHP print $fname;?>" 
                                                                                      placeholder="Enter Firstname" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="last-name-id">Last Name</label>
                    <input type="text" autocomplete="off" class="form-control" id="last-name-id" name ='lastname' value="<?PHP print $lname;?>" 
                                                                                      placeholder="Enter Lastname" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="middle-name-id">Middle Name</label>
                    <input type="text" autocomplete="off" class="form-control" id="middle-name-id" name ='middlename' value="<?PHP print $mname;?>" 
                                                                                        placeholder="Optional">
                  </div>
                  <div class="form-group">
                    <label for="contact-number-id">Contact Number</label>
                    <input type="text" maxlength="13" onkeypress="return isNumber(event)" autocomplete="off" class="form-control" id="contact-number-id" name ='cnumber' value="<?PHP print $cnumber;?>" 
                                                                                      placeholder="Enter Contact number" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="email-address-id">Email</label>
                    <input type="email" autocomplete="off" class="form-control" id="email-address-id" name ='email' value="<?PHP print $email;?>" 
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
                    <input type="text" autocomplete="off" class="form-control" id="user-name-id" name ='username' value="<?PHP print $uname;?>" 
                                                                                    placeholder="Enter User name" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="password-id">Password</label>
                    <input type="password" autocomplete="off" class="form-control" id="password-id" name ='password' value="<?PHP print $pword;?>" 
                                                                                      placeholder="Enter Password" REQUIRED>
                  </div>
                  <div class="form-group">
                    <label for="verify-password-id">Verify Password</label>
                    <input type="password" autocomplete="off" class="form-control" id="verify-password-id" name ='cpassword' value="<?PHP print $cpword;?>" 
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
              <button type="submit" class="btn btn-success btn-block" name = "signupbutton">Register</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Modal Log-in -->
    <div class="modal fade" id="login-form-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="index.php">
            <div class="modal-header" style="background-color: #F0C529">
              <h5 class="modal-title font-weight-bolder h3">LOG-IN FORM</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="user-name-id">Username</label>
                <input type="text" class="form-control" id="user-name-id" name ='username' value="<?PHP print $uname;?>"                    
                                                                                        placeholder="Enter User name">
              </div>
              <div class="form-group">
                <label for="password-id">Password</label>
                <input type="password" class="form-control" id="password-id" name ="password" placeholder="Enter Password">
                <p class="text-right">Forgot password? Contact admin.</p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="loginbutton">Login</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <div class="container">
      <div class="row">
        <div class="col-6 logo text-center font-italic font-weight-bolder h6">
          <p>
            <a href="http://usjr.edu.ph/">
              <img class="rounded-circle img-fluid" width="100" src="../images/USJR1.png" title="USJR LOGO"> 
            </a>
          </p>
          <p>
            University of San Jose – Recoletos
          </p>
        </div>

        <div  class="col-6 logo text-center font-italic font-weight-bolder h6">
          <p>
            <a  href="https://www.facebook.com/groups/260904277344906/">
              <img class="rounded-circle img-fluid" width="100" src="../images/cpelogo.jpg" title="CPE LOGO">
            </a>
          </p>
          <p>
            Computer Engineering Department
          </p>
        </div>
      </div>
      <div class="jumbotron" style="border-top:4px solid #085C2E;border-bottom:4px solid #085C2E;border-right:1px solid #085C2E;border-left:1px solid #085C2E;">
        <code style="color:rgb(14, 63, 35);">
        <h1 class="display-4 text-center">RFID-based Attendance Checker</h1>
        <p class="lead text-center">at Computer Engineering Laboratories</p>
        </code>
        <hr>
      </div>
    </div>
    
    <script src="index.js"></script>
  </body>
</html>
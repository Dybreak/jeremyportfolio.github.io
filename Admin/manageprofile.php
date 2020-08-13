<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $selecteda = '';
  $selectedr = '';
  $selectedf = '';

  $fname = '';
  $lname = '';
  $mname = '';
  $cnumber = '';
  $email = '';
  $status = '';
  $atype = '';
  $image = '';
  $uname= '';
  $pword = '';
  $cpword = '';
  $indicate = '';
  
  $addRFIDtemp = "";
  $addRFID = "";
  
  if(empty($_SESSION['ID']))
  {
    $ID = $_SESSION['account_ID'];
  }
  else
  {
    $ID = $_SESSION['ID'];
  }
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
          if($db_field['account_ID'] == $ID)      
          {
            $fname = $db_field['firstName'];
            $lname = $db_field['lastName'];
            $mname = $db_field['middleName'];
            $cnumber = $db_field['contactNumber'];
            $email = $db_field['emailAddress'];
            $atype = $db_field['accountType'];                   
            $status = $db_field['accountStatus'];
            $uname = $db_field['accountUsername'];
            $pword = $db_field['accountPassword'];
            $image = $db_field['accountImage']; 
            $RFID = $db_field['RFID']; 
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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      if (isset($_POST['addRFID_button']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database); 
        if ($db_found) 
        {
          $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE RFID = ?');
          $SQL->bind_param('s', $_POST['addRFID_name']);
          $SQL->execute();
          $result = $SQL->get_result();

          if ($result->num_rows > 0) 
          {
            $message = "RFID Already taken!";
            $messageType = 3;
        MessageDisplay($message, $messageType);
          }
          else 
          {
            $SQL = $db_found->prepare("UPDATE tbl_account SET RFID=? WHERE account_ID=?");

            $SQL->bind_param('si',$_POST['addRFID_name'], $ID);
            $SQL->execute();

            $SQL->close();
            $db_found->close();

            $message = "RFID Added.";
            $messageType = 1;
            MessageDisplay($message, $messageType);
            $RFID = $_POST['addRFID_name'];
          }      
        }
        else 
        {
          $message = "Database not found.";
          $messageType = 3;
          MessageDisplay($message, $messageType);
        }
      }
      else if (isset($_POST['changeRFID_button']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database); 
        if ($db_found) 
        { 
          $SQL = $db_found->prepare('SELECT * FROM tbl_account WHERE RFID = ?');
          $SQL->bind_param('s', $_POST['changeRFID_name']);
          $SQL->execute();
          $result = $SQL->get_result();

          if ($result->num_rows > 0) 
          {
            $message = "RFID Already taken!";
            $messageType = 3;
            MessageDisplay($message, $messageType);
          }
          else 
          { 
            $SQL = $db_found->prepare("UPDATE tbl_schedule SET RFID=? WHERE RFID=?");

            $SQL->bind_param('si',$_POST['changeRFID_name'], $RFID);
            $SQL->execute();

            $SQL = $db_found->prepare("UPDATE tbl_account SET RFID=? WHERE RFID=?");

            $SQL->bind_param('si',$_POST['changeRFID_name'], $RFID);
            $SQL->execute();

            $SQL->close();
            $db_found->close();

            $message = "RFID changed.";
            $messageType = 1;
            MessageDisplay($message, $messageType);
            $RFID = $_POST['changeRFID_name'];
          }      
        }
        else 
        {
          $message = "Database not found.";
          $messageType = 3;
          MessageDisplay($message, $messageType);
        }
      }
      else if (isset($_POST['removeRFID_button']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          $SQL = $db_found->prepare("UPDATE tbl_account SET RFID=null WHERE RFID=?");

          $SQL->bind_param('s',$RFID);
          $SQL->execute();

          $SQL = $db_found->prepare("DELETE FROM tbl_schedule WHERE RFID=?");

          $SQL->bind_param('s',$RFID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "RFID Removed!";
          $messageType = 3;
          MessageDisplay($message, $messageType);
          $RFID = null;
        }
        else 
        {
          $message = "Database not found.";
          $messageType = 3;
          MessageDisplay($message, $messageType);
        }
      }
      else if (isset($_POST['editbutton']))
      {
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $mname = $_POST['middlename'];
        $cnumber = $_POST['cnumber'];
        $email = $_POST['email'];
        $atype = $_POST['atype'];
        if($_POST['password'] != '' && $_POST['cpassword'] != '')
        {
          if($_POST['password'] == $_POST['cpassword'])
          {
            $pword = $_POST['password'];
          }
          else
          {
            $message = "Password does not match!";
            $messageType = 3;
            MessageDisplay($message, $messageType);
          }
        }
        else
        {
          $indicate = '1';
          $phash = $pword;
        }
        if ( 0 != filesize( $_FILES['image']['tmp_name'] ) )
        {
          $image = addslashes($_FILES['image']['tmp_name']);
          $image = file_get_contents($image);
          $image = base64_encode($image);
        }
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          if($indicate != 1)
          {
              $phash = password_hash($pword, PASSWORD_DEFAULT);
          }
          $SQL = $db_found->prepare("UPDATE tbl_account SET firstName=?, lastName=?, middleName=?, contactNumber=?, emailAddress=?,
                                              accountStatus=?, accountType=?, accountPassword=?, accountImage=? WHERE account_ID=?");

          $SQL->bind_param('ssssssssss',$fname,$lname,$mname,$cnumber,$email,$status,$atype,$phash,$image,$ID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();
          $message = "Saved.";
          $messageType = 1;
          MessageDisplay($message, $messageType);
        }
      }
      else if (isset($_POST['activate']))
      {
        //ACTIVATE ACCOUNT
        $status = "Active";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          $SQL = $db_found->prepare("UPDATE tbl_account SET accountStatus=? WHERE account_ID=?");

          $SQL->bind_param('si',$status, $ID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Account Activated!";
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
      else if (isset($_POST['deactivate']))
      {
        //DEACTIVATE ACCOUNT
        $status = "Inactive";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        if ($db_found) 
        {
          $SQL = $db_found->prepare("UPDATE tbl_account SET accountStatus=? WHERE account_ID=?");

          $SQL->bind_param('si',$status, $ID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();

          $message = "Account Deactivated!";
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
      else if(isset($_POST['manage_schedule']))
      {
        $_SESSION['RFID'] = $RFID;
        $_SESSION['facultyimage'] = $image;
        $_SESSION['facultyfname'] = $fname;
        $_SESSION['facultymname'] = $mname;
        $_SESSION['facultylname'] = $lname;
        header ("Location: manageschedule.php");
      }
      else if(isset($_POST['back']))
      {
        header ("Location: accountlist.php");
      }
    }
  }
  
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Admin - Manage Account</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarAdmin.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="admin.css" type="text/css"> 
    <link rel="shortcut icon" href="http://sstatic.net/stackoverflow/img/favicon.ico">
  </head>
  <body>
    <div class="container-fluid w-75">
      <div class="row">
        <div class="col-3 text-center">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="btn-group-vertical btn-block">
              <button type="button" class="btn btn-outline-warning btn-lg" data-toggle="modal" data-target="#edit_form_modal">
                <span class="fas fa-edit"></span> Edit Profile
              </button>          
              <?PHP
                if($status == "Inactive")
                {
                  ?><input type ="submit" class="btn btn-outline-success btn-lg" name="activate" value="Activate">
                  <?PHP
                }
                else if($status == "Active")
                {
                  ?><input type ="submit" class="btn btn-outline-danger btn-lg" name="deactivate" value="Deactivate">
                  <?PHP      
                }
                if($atype == "Faculty")
                {
                  if($RFID == null)
                  {
                    ?>
                    <input type ="button" class="btn btn-outline-secondary btn-lg" value="Add RFID" data-toggle="modal"
                                                                                           data-target="#add_rfid_modal"> 
                    <?PHP
                  }
                  else if(!empty($RFID))
                  {
                    ?><input type ="button" class="btn btn-outline-info btn-lg" value="Change RFID" data-toggle="modal"
                                                                                 data-target="#change_rfid_modal">

                      <input type ="button" class="btn btn-outline-danger btn-lg" value="Remove RFID" data-toggle="modal"
                                                                                 data-target="#remove_rfid_confirm_modal">
                    <?PHP      
                  }
                  ?>
                  <input id ="addschedule" type ="submit" class="btn btn-outline-primary btn-lg" name="manage_schedule" value="Manage schedule">
                  <?PHP
                }
              ?>
              <a href="accountlist.php" class="btn btn-outline-secondary btn-lg">
                <span class="fas fa-arrow-left"></span> Back
              </a>
            </div>
          </form>
        </div>
        <div class="col-9">
          <div class="row" style="border-top:9px solid #085C2E;">
            <div class="col-4 border-left border-bottom text-center">
              <?PHP  print '<img class=" mt-4 rounded-lg img-fluid" height="200" width="200" src="data:image;base64,'. $image .' ">'; ?>
            </div>
            <div class="col-8 border">
              <div class="row">
                <div class="col-6 text-left">                                   
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">
                    <label for="first-name-id">First Name</label>
                    <input type="text" class="form-control-plaintext" name ='firstname'
                     value="<?PHP print $lname.", ".$fname." ".$mname.".";?>" readonly>
                  </div>
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">
                    <label for="contact-number-id">Contact Number</label>
                    <input type="text" class="form-control-plaintext" name ='cnumber' value="<?PHP print $cnumber;?>" readonly>
                  </div>
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">             
                    <label for="email-address-id">Email</label>
                    <input type="email" class="form-control-plaintext" name ='email' value="<?PHP print $email;?>" readonly>
                  </div>   
                </div>
                <div class="col-6 text-left">
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">
                    
                    <label for="email-address-id">Account Type</label>
                    <input type="email" class="form-control-plaintext" id="email-address-id" name ='email' value="<?PHP print $atype;?>" 
                                                                                                                                  readonly>
                  </div>
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">
                    <label for="user-name-id">Username</label>
                    <input type="text" class="form-control-plaintext" id="user-name-id" name ='username' value="<?PHP print $uname;?>" 
                                                                                                                                  readonly>
                  </div>     
                  <div class="form-group" style="border-bottom:1px solid #F0C529;">             
                    <label for="email-address-id">Status</label>
                    <input type="email" class="form-control-plaintext" name ='status' value="<?PHP print $status;?>" readonly>
                  </div>  
                </div>
              </div>
            </div>
          </div>
          <?PHP
            if($atype == "Faculty")
            {
              if($RFID == null)
              {
                ?>
                <div class="row" style="border-top:1px solid #085C2E;">
                  <div class="col text-center">  
                    <code><strong>
                      <p class="h3 pt-3 font-italic"> NO RFID REGISTERED!</p>
                    </strong></code>
                    <img SRC="../images/rfid_no.png" height="200" width="200">
                  </div>
                </div>
                <?PHP
              }
              else if(!empty($RFID))
              {
                ?>
                <div class="row" style="border-top:1px solid #085C2E;">
                  <div class="col text-center">
                    <code><strong>
                      <p class="h3 pt-3"><b>RFID:</b> <?PHP echo $RFID ?></p>
                    </strong></code>
                    <img SRC="../images/rfid_yes.png" height="200" width="200">
                  </div>
                </div>
                <?PHP
              }
            }
          ?>
        </div>
      </div>
      
    <!-- Modal Edit -->
    <div class="modal fade" id="edit_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              <h5 class="modal-title">EDIT FORM</h5>
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
                                                                                        placeholder="Enter Middle name">
                  </div>
                  <div class="form-group">
                    <label for="contact-number-id">Contact Number</label>
                    <input type="text" class="form-control" id="contact-number-id" name ='cnumber' value="<?PHP print $cnumber;?>" 
                                                                                      placeholder="Enter Contact number" REQUIRED>
                  </div>
                  <div class="form-group">             
                    <label for="email-address-id">Email</label>
                    <input type="email" class="form-control" id="email-address-id" name ='email' value="<?PHP print $email;?>" 
                                                                                    placeholder="Enter Email Address" REQUIRED>
                  </div>
                  
                </div>
                <div class="col-6 text-left">
                  <div class="form-group">
                    <?PHP
                        if($atype == "Admin")
                        {
                          $selecteda = "SELECTED";
                        }
                        else if($atype == "READS")
                        {
                          $selectedr = "SELECTED";
                        }
                        else if($atype == "Faculty")
                        {
                          $selectedf = "SELECTED";
                        }
                    ?>
                    <label for="account-type-id">Account Type:</label>
                    <select class="form-control" id="account-type-id" name="atype">
                      <option Value="Faculty" <?PHP print $selectedf;?>>Faculty</option>
                      <option Value="READS" <?PHP print $selectedr;?>>READS</option>
                      <option Value="Admin" <?PHP print $selecteda;?>>Admin</option>
                    </select>
                  </div>


                  <div class="form-group">
                    <label for="user-name-id">Username</label>
                    <input type="text" class="form-control" id="user-name-id" name ='username' value="<?PHP print $uname;?>" 
                                                                                    placeholder="Enter User name" READONLY>
                  </div>
                  <div class="form-group">
                    <label for="password-id">Password</label>
                    <input type="password" class="form-control" id="password-id" name ='password' value="" 
                                                                    placeholder="Enter Password">
                  </div>
                  <div class="form-group">
                    <label for="verify-password-id">Verify Password</label>
                    <input type="password" class="form-control" id="verify-password-id" name ='cpassword' value=""
                                                                          placeholder="Confirm Password">
                  </div>
                  <div class="form-group">
                    <label for="image-file-id">Choose image:</label>
                    <img SRC="" style="display:none" height="200" width="200" id="image">
                    <input type="file" class="form-control-file" id="image-file-id" name="image" onchange="showImage.call(this)">
                  </div>
                  
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success btn-block pt-2 pb-2" name = "editbutton">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Add RFID -->
    <div class="modal fade" id="add_rfid_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              
              <h5 class="modal-title font-weight-bolder h4"><span class="fas fa-cog fa-spin h2"></span>  Getting Started</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group text-center">
                <label for="user-name-id" class="h4">SCAN</label>
                <input type="text" autocomplete="off" class="form-control" id="rfid_id" name ='addRFIDtemp' value=""
                                                              onblur="this.focus()" placeholder="Scan here" autofocus>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Change RFID -->
    <div class="modal fade" id="change_rfid_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              
              <h5 class="modal-title font-weight-bolder h4"><span class="fas fa-wrench h4"></span> Change RFID</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group text-center">
                <label for="user-name-id" class="h4">SCAN</label>
                <input type="text" autocomplete="off" class="form-control" id="rfid_id" name ='changeRFIDtemp' value=""
                                                              onblur="this.focus()" placeholder="Scan here" autofocus>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?PHP 
      if(isset($_POST['addRFIDtemp']))
        {
          $addRFIDtemp = $_POST['addRFIDtemp'];
      ?>
          <script>
            $( document ).ready(function() 
            {
              $('#add_rfid_confirm_modal').modal('show');
            });
          </script>
      <?PHP
        }
      else if(isset($_POST['changeRFIDtemp']))
        {
          $changeRFIDtemp = $_POST['changeRFIDtemp'];
      ?>
          <script>
            $( document ).ready(function() 
            {
              $('#change_rfid_confirm_modal').modal('show');
            });
          </script>
      <?PHP
        }
    ?>
    <!-- Modal Change RFID Confirmation -->
    <div class="modal fade" id="change_rfid_confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              
              <h5 class="modal-title font-weight-bolder h4"><span class="fas fa-wrench h4"></span> Confirm Change</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group text-center">
                <label for="user-name-id" class="h4">RFID:</label>
                <code>
                  <input type="text" class="form-control-plaintext text-center h3" id="changeRFID_id" name ='changeRFID_name'
                                                                                   value="<?PHP echo $changeRFIDtemp ?>" readonly>
                </code>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="changeRFID_button">Change</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Add RFID Confirmation -->
    <div class="modal fade" id="add_rfid_confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              
              <h5 class="modal-title font-weight-bolder h4"><span class="fas fa-cog fa-spin h2"></span> Getting Started</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group text-center">
                <label for="user-name-id" class="h4">RFID:</label>
                <code>
                  <input type="text" class="form-control-plaintext text-center h3" id="addRFID_id" name ='addRFID_name'
                                                                                   value="<?PHP echo $addRFIDtemp ?>" readonly>
                </code>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="addRFID_button">Add</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal RFID Removed -->
    <div class="modal fade" id="remove_rfid_confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <div class="modal-header">
              
              <h5 class="modal-title font-weight-bolder h5"><span class="fas fa-wrench h4"></span>CONFIRM</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group text-center">
                <code><div class="border-danger bg-danger text-white">This will remove all schedules from this RFID.</div></code>
                <!-- <label for="user-name-id" class="h4">RFID:</label> -->
                <input type="text" class="form-control-plaintext text-center h3" id="removeRFID_id" name ='removeRFID_name'
                                                                                   value="<?PHP echo "RFID: ".$RFID ?>" readonly>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" name="removeRFID_button">Remove</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- LOCAL TOOLS -->
    <script src="admin.js"></script>
    <script src="../Index/index.js"></script>
  </body>
</html>

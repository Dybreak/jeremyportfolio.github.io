<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $status="Active";
  $ID;

  if($connected == true)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
      $ID = $_POST['ID'];
      if (isset($_POST['accept']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        //ACCEPT REQUEST
        if ($db_found) 
        {
          $SQL = $db_found->prepare("UPDATE tbl_account SET accountStatus=? WHERE account_ID=?");

          $SQL->bind_param('si',  $status, $ID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();
          $message = "Account Added.";
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
      else if (isset($_POST['decline']))
      {
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database);
        //DECLINE REQUEST
        if ($db_found) 
        {
          $SQL = $db_found->prepare("DELETE FROM tbl_account WHERE account_ID=?");

          $SQL->bind_param('s', $ID);
          $SQL->execute();

          $SQL->close();
          $db_found->close();
          $message = "Account declined.";
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
    <title>Admin - Account Request</title>
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
        <a class="h2" href="request.php"><span class="fas fa-tasks"></span> Requests</a>

        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="table-responsive">
                  <table id="request_list_id" class="table table-sm table-hover text-center" width="100%">   
                    <thead>
                      <tr>  
                        <td></td>
                        <th scope="col">First name</th>
                        <th scope="col">Last name</th>
                        <th scope="col">Middle name</th>
                        <th scope="col">Contact Number</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Account Type</th>
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
                                if($db_field['accountStatus'] == "Pending")
                                {
                                  ?>
                                  <tr data-toggle='modal' data-target='#option_modal'>
                                  <td><?PHP print $db_field['account_ID'] ?></td>
                                  <td><?PHP print $db_field['firstName'] ?></td>
                                  <td><?PHP print $db_field['lastName'] ?></td>
                                  <?PHP
                                    if($db_field['middleName'] == '')
                                    {
                                      ?>
                                      <td><?PHP print '-'; ?></td>
                                      <?PHP
                                    }
                                    else
                                    {
                                      ?><td><?PHP print $db_field['middleName'] ?></td>
                                      <?PHP
                                    }
                                  ?>
                                  <td><?PHP print $db_field['contactNumber'] ?></td>
                                  <td><?PHP print $db_field['emailAddress'] ?></td>
                                  <td><?PHP print $db_field['accountType'] ?></td>
                                  <td></td></tr>
                                  <?PHP
                                }      
                              }
                            }
                            else
                            {
                              $message = "No record found.";
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
      <!-- Modal Accept or not -->
    <div class="modal fade " id="option_modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <form method ="POST" enctype="multipart/form-data" ACTION ="request.php">
            <div class="modal-header">
              <h5 class="modal-title">Account Request</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="request_id" name ='ID' readonly hidden>
                <div class="card">
                  <img class="card-img-top" src="" id="image_id" alt="Faculty image">
                  <div class="card-body bg-white">
                    <h4 class="card-title">
                      <input type="text" class="form-control-plaintext" id="name_id" name ='input_text_name' value="" readonly>
                    </h4>
                    <p class="card-text">
                      <input type="text" class="form-control-plaintext" id="description_id" name ='input_text_description' value="" readonly>
                    </p>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="accept">ACCEPT</button>
              <button type="submit" class="btn btn-danger" name="decline">DECLINE</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- LOCAL TOOLS -->
    <?PHP include '../include/requestjs.php' ?>
    <script src="admin.js"></script>
  </body>
</html>
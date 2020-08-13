<?PHP
  include '../restrictADMIN.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
  $faculty = 0;
  $admin = 0;
  $reads = 0;

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
          if($db_field['accountType'] == "Faculty")
          {
            $faculty++;
          }
          else if($db_field['accountType'] == "READS")
          {
            $reads++;
          }
          else if($db_field['accountType'] == "Admin")
          {
            $admin++;
          }
        }
      }
      else
      {
        $message = "No records found";
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
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Admin - Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarAdmin.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="admin.css" type="text/css"> 
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-4">
         
          <div class="card">
            <a href="accountlist.php" class="text-decoration-none text-dark">
              <div class="card-header h3" style="background-color: #F0C529;">
                FACULTY
              </div>
              <div class="card-body bg-white">
                <p> There are <strong><?PHP print $faculty ?></strong> Faculty registered.</p>
              </div> 
             </a>
          </div>
         
        </div>
        <div class="col-4">
          <div class="card">
            <a href="accountlist.php" class="text-decoration-none text-dark">
              <div class="card-header h3" style="background-color: #F0C529;">
                READS
              </div>
              <div class="card-body bg-white">
                <p> There are <strong><?PHP print $reads ?></strong> READS registered.</p>
              </div> 
            </a>
          </div>
        </div>
        <div class="col-4">
          <div class="card">
            <a href="accountlist.php" class="text-decoration-none text-dark">
              <div class="card-header h3" style="background-color: #F0C529;">
                ADMIN
              </div>
              <div class="card-body bg-white">
                <p> There are <strong><?PHP print $admin ?></strong> admin registered.</p>
              </div> 
            </a>
          </div>
        </div>
      </div>
    </div>
    <script src="admin.js"></script>
  </body>
</html>
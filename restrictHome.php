<?PHP
  session_start();
  if ((isset($_SESSION['login']) && $_SESSION['login'] != '')) 
  {
    if($_SESSION['accountType'] == "READS")
    {
      header ("Location: ../Reads/reads.php"); 
    }
    else if($_SESSION['accountType'] == "Faculty")
    {
      header ("Location: ../Faculty/Faculty.php"); 
    }
    else if($_SESSION['accountType'] == "Admin")
    {
      header ("Location: ../Admin/admin.php"); 
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
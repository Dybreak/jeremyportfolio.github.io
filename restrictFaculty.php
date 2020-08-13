<?PHP
  session_start();
  if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) 
  {
    // printf('<script> alert("You are not allowed on this page! Please log-in first."); </script>');
    session_destroy();
    //header( "refresh:0; url=../Index/index.php" ); 
    header ("Location: ../BrokenPage/brokenpage.php"); 
  }
  else
  {
    if($_SESSION['accountType'] == "Faculty")
    {
      if($_SESSION['accountStatus'] == "Inactive")
      {
        printf('<script> alert("Account is not activated! Contact Admin to activate."); </script>');
        session_destroy();
        header( "refresh:0; url=../Index/index.php" );  
      }
      else if($_SESSION['accountStatus'] == "Pending")
      {
        printf('<script> alert("Pending request."); </script>');
        session_destroy();
        header( "refresh:0; url=../Index/index.php" );
      }
    }
    else
    {
      header ("Location: ../BrokenPage/brokenpage.php"); 
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
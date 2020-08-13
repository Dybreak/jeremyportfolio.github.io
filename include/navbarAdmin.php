<?PHP
  if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    if(isset($_POST['profile']))
    {
      $_SESSION['ID'] = $_SESSION['account_ID'];
      header ("Location: manageprofile.php");
    }
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow mb-3" style="background-color: #085C2E;">
  <!-- HOME BUTTON -->
  <a class="navbar-brand" href="admin.php" title="Home">
    <span class="fas fa-qrcode"></span> RFID Based Attendance Checker
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
                                                                    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item profile">
        <a class="nav-link" id="myProfile" style="padding: 0"
                           title="<?PHP print $_SESSION['lastName'].", ".$_SESSION['firstName']?>"> 
          <form method ="POST" enctype="multipart/form-data" ACTION ="manageprofile.php">
            <button class="btn p-0" type="submit" name ='profile'>
          
         <?PHP 
          print '<img class="rounded-circle img-fluid" alt="Cinque Terre" src="data:image;base64,'. $_SESSION['accountImage'] .' ">'; 
         ?>
         </button>
         </form>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link typeColor" style="color: #F0C529;"> 
          <code>
          <?PHP 
          print $_SESSION['accountType']; 
          ?>
          </code>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logrecord.php" id="navbar_dropdown_Log"> 
          <span class="fas fa-calendar-check"></span> LOG RECORD</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbar_dropdown_manage" role="button"
                                                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fas fa-edit"></span> MANAGE
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbar_dropdown_manage">
          <a class="dropdown-item" href="accountlist.php"> <span class="fas fa-users"></span> Account</a>
          <li class="dropdown-divider"></li>
          <a class="dropdown-item" href="subject.php"> <span class="fas fa-book"></span> Subject</a>
          <a class="dropdown-item" href="room.php"> <span class="fas fa-school"></span> Room</a>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="request.php" id="navbar_request">
          <span class="fas fa-tasks"></span> REQUEST
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../Logout/logout.php"> <span class="fas fa-sign-out-alt"></span> LOG-OUT</a>
      </li>
    </ul>
  </div>
</nav>
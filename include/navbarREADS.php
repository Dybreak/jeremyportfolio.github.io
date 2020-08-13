<nav class="navbar navbar-expand-lg navbar-dark shadow mb-3" style="background-color: #085C2E;">
  <!-- HOME BUTTON -->
  <a class="navbar-brand" href="verify.php" title="Home">
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
         <?PHP 
          print '<img class="rounded-circle img-fluid" alt="Cinque Terre" src="data:image;base64,'. $_SESSION['accountImage'] .' ">'; 
         ?>
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
        <a class="nav-link" href="verify.php" id="navbar_dropdown_Log"> 
          <span class="fas fa-calendar-check"></span> VERIFY</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../Logout/logout.php"> <span class="fas fa-sign-out-alt"></span> LOG-OUT</a>
      </li>
    </ul>
  </div>
</nav>
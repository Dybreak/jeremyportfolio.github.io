<?PHP
  include '../restrictREADS.php'; //restrict anonymous
  require '../../../../configure.php'; //connect to data base
  ini_set("mysql.connect_timeout",300); 
  ini_set("default_socket_timeout",300);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>READS - Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?PHP include '../include/plugins.php' ?>
    <?PHP include '../include/navbarREADS.php' ?>
    <!-- LOCAL TOOLS --> 
    <link rel="stylesheet" href="reads.css" type="text/css"> 
  </head>
  <body>
    
    <!-- LOCAL TOOLS -->
    <script src="reads.js"></script>
  </body>
</html>
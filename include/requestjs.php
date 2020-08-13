<script>
  function optionRequest(request_id)
  {
    var request = document.getElementById(request_id);  
    <?PHP
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
          
      if ($db_found) 
      { 
          $SQL = $db_found->prepare('SELECT * FROM tbl_account');                        
          $SQL->execute();                     
          $result = $SQL->get_result();

          if ($result->num_rows > 0) 
          {
              while( $db_field = $result->fetch_assoc() )
              {
                  ?>    
                  if(request.value == "<?PHP printf($db_field['account_ID']); ?>")
                  {
                    document.getElementById("image_id").src = "data:image/jpeg;base64,<?php printf( $db_field['accountImage'] ); ?>";
                    document.getElementById("name_id").value = "<?PHP printf($db_field['lastName'].", ".$db_field['firstName']); ?>";
                    document.getElementById("description_id").value = "<?PHP printf($db_field['accountType']." Registration."); ?>";
                  }
                  <?PHP  
              }   
          }
          else 
          {
              print "No Request found";      
          }
          $SQL->close();
          $db_found->close();
  
      }
      else 
      {
          print "Database NOT Found ";
      }
    ?>
  }


</script>

<script>
  function editRoom(edit_room_id)
  {
    var edit_room = document.getElementById(edit_room_id);  
    <?PHP
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
          
      if ($db_found) 
      { 
          $SQL = $db_found->prepare('SELECT * FROM tbl_room');                        
          $SQL->execute();                     
          $result = $SQL->get_result();

          if ($result->num_rows > 0) 
          {
              while( $db_field = $result->fetch_assoc() )
              {
                  ?> 
                  
                  if(edit_room.value == "<?PHP printf($db_field['room_ID']); ?>")
                  {
                    document.getElementById("edit_room_code_id").value = "<?PHP printf($db_field['roomCode']); ?>";
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

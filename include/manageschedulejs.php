<script>
  function populate(subject_id)
  {
    var subject = document.getElementById(subject_id);  
    
    <?PHP
    
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
          
      if ($db_found) 
      { 
        $SQL = $db_found->prepare('SELECT * FROM tbl_subject');                        
        $SQL->execute();                     
        $result = $SQL->get_result();
        
        if ($result->num_rows > 0) 
        {
          while( $db_field = $result->fetch_assoc() )
          {
            ?>
            
            if(subject.value == "<?PHP printf($db_field['subject_ID']); ?>")
            {
              document.getElementById("description_id").value = "<?PHP printf($db_field['description']); ?>";
              document.getElementById("unit_id").value = "<?PHP printf($db_field['units']); ?>";
            }
            else if(subject.value == "Select")
            {
              document.getElementById("description_id").value = "";
              document.getElementById("unit_id").value = "";
            }
            <?PHP  
          }   
        }
        $SQL->close();
        $db_found->close();
  
      }
    ?>
  }
  function editpopulate(edit_subject_id)
  {
    var editsubject = document.getElementById(edit_subject_id);
    
    <?PHP

      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) 
      { 
        $SQL = $db_found->prepare('SELECT * FROM tbl_subject');                       
        $SQL->execute();                     
        $result = $SQL->get_result();
        
        if ($result->num_rows > 0) 
        {
          while( $db_field = $result->fetch_assoc() )
          {
            ?> 
            if(editsubject.value == "<?PHP printf($db_field['subject_ID']); ?>")
            {
              document.getElementById("edit_description_id").value = "<?PHP printf($db_field['description']); ?>";
              document.getElementById("edit_unit_id").value ="<?PHP printf($db_field['units']); ?>";
            }
            <?PHP  
          }   
        }
        else 
        {
          ?>
          alert("No subject list found"); 
          <?PHP      
        }
        $SQL->close();
        $db_found->close(); 
      }
    ?>
  }
  function editSchedule(schedule_id)
  {
    var schedule = document.getElementById(schedule_id);  

    <?PHP
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
          
      if ($db_found) 
      { 
        $SQL = $db_found->prepare('SELECT * FROM tbl_schedule');                        
        $SQL->execute();                     
        $result = $SQL->get_result();

        if ($result->num_rows > 0) 
        {
          while( $db_field = $result->fetch_assoc() )
          {
            ?>    
            if(schedule.value == "<?PHP printf($db_field['schedule_ID']); ?>")
            {
              document.getElementById("edit_subject_id").value = "<?PHP printf($db_field['subject_ID']); ?>";
              editpopulate('edit_subject_id');
              document.getElementById("edit_start_time_id").value = "<?PHP printf(date("H:i", strtotime($db_field['startTime']))); ?>";
              document.getElementById("edit_end_time_id").value = "<?PHP printf(date("H:i", strtotime($db_field['endTime']))); ?>";
              document.getElementById("edit_day_id").value = "<?PHP printf($db_field['daySchedule']); ?>";
              document.getElementById("edit_room_id").value = "<?PHP printf($db_field['room']); ?>";
            }
            <?PHP  
          }   
        }
        else 
        {
          ?>
          alert("No schedule found"); 
          <?PHP  
        }
        $SQL->close();
        $db_found->close();
      }
    ?>
  }
</script>
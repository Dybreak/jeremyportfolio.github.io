<script>
  function editSubject(edit_subject_id)
  {
    var edit_subject = document.getElementById(edit_subject_id);  
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
                  if(edit_subject.value == "<?PHP printf($db_field['subject_ID']); ?>")
                  {
                    document.getElementById("edit_subject_code_id").value = "<?PHP printf($db_field['subjectCode']); ?>";
                    document.getElementById("edit_subject_description_id").value = "<?PHP printf($db_field['description']); ?>";
                    document.getElementById("edit_subject_unit_id").value = "<?PHP printf($db_field['units']); ?>";
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

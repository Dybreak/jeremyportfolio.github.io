//When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) 
{
  if (event.target == modalSignup) 
  {  
    modalSignup.style.display = "none";
  }
  else if (event.target == modalLogin) 
  {
    modalLogin.style.display = "none";
  }
}
//Display image
function showImage()
{
  if(this.files && this.files[0])
  {
    var obj = new FileReader();
    obj.onload = function(data)
    {
      var image = document.getElementById("image");
      image.src = data.target.result;
      image.style.display = "block";
    }
    obj.readAsDataURL(this.files[0]);
  }
}
function isNumber(event)
{
  var keycode = event.keyCode;
  if(keycode > 47 && keycode < 59 || keycode == 43)
  {
    return true;
  }
  return false;
}
$(document).ready(function() 
{
  $("#alert").fadeTo(5500, 1500).slideUp(1500, function(){
    $("#alert").slideUp(1500);
  });
});

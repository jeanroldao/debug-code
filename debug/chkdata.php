<?php
if (!empty($_POST['data'])) {
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
}
?>
<script type="text/javascript" src="jquery.uploadify-v2.1.0/jquery-1.3.2.min.js"></script>
<form method="post" name="mform">
  <div id="cont">
    <input type="text" name="data[]" /><br />
    <input type="checkbox" value="123" name="data[]" onclick="disb(this);" />t <br />
  </div>
  <input type="submit" />
  <input type="button" value="t" onclick="dob()" />
</form>
<script>
contudo = document.getElementById('cont').innerHTML;
function dob() {
  document.getElementById('cont').innerHTML += contudo;
}

function disb(t) {
  var obj = document.getElementsByName('data[]'); 
  if(t.checked){
    var r = true;
  }else{
    var r = false;
  }
  //alert(obj[obj.length-1] == t);
  //alert(r);
  for (var i in obj) {
    if (obj[i] == t) {
      $(obj[i-1]).attr('disabled', r);
      break;
    }
  }
}
</script>
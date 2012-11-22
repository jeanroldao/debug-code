<?php
if ($_POST['serialize']) {
  eval('$_POST[\'code\'] = serialize('.$_POST['code'].');');
}
if ($_POST['unserialize']) {
  $_POST['code'] = var_export(unserialize($_POST['code']), true);
}
?>
<form method="post">
<input type="submit" name="serialize" value="Serializar" />
<input type="submit" name="unserialize" value="Deserializar" />
<div><textarea cols="100" rows="100" name="code"><?=$_POST['code']?></textarea><div>
</form>
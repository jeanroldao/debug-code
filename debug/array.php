<pre><?php
if (!empty($_POST['_SERVER'])) {
  print_r(unserialize(base64_decode($_POST['_SERVER'])));
}
$dados = base64_encode(serialize($_SERVER));
?>
<form method="POST">
<input type="hidden" name="_SERVER" value="<?php echo $dados;?>" />
<input type="submit" />
</form>
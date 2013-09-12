<script type="text/javascript">
function auto_submit(event) {
	var x = parseInt((event.x - 14) / 8) + 65;
	//var x = event.x;
	var y = parseInt((event.y - 56) / 16) + 65;
	//var y = event.y;
	
	//console.log([String.fromCharCode(x), String.fromCharCode(y)]);
	
	document.getElementById('input').value = String.fromCharCode(y) + String.fromCharCode(x);
	document.getElementById('input').form.submit();
}
</script>
<pre onclick="auto_submit(event)" style="cursor: pointer;">
<?php
include 'php_campominado.php';

class CampoMinadoWeb extends CampoMinado {

	#override
	public function readLine() {
		$input = explode(',', $_POST['input']);
		$ret = array_shift($input);
		$_POST['input'] = implode(',', $input);
		return $ret;
	}
}

//CampoMinado
if ($_POST['campoMinado']) {
	$campoMinado = unserialize(base64_decode($_POST['campoMinado']));
	$campoMinado->readGameInput();
} else {
	$campoMinado = new CampoMinadoWeb();
}
?>
</pre>
<form method="post">
	<input type="hidden" name="campoMinado" value="<?=base64_encode(serialize($campoMinado))?>" />
	<input type="text"   name="input" id="input" />
	<input type="submit" />
</form>
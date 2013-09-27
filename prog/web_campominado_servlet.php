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
include_once 'php_campominado.php';

if (!class_exists('CampoMinadoWeb')) {
	class CampoMinadoWeb extends CampoMinado {

		#override
		public function readLine() {
			@$input = explode(',', $_GET['input']);
			$ret = array_shift($input);
			$_GET['input'] = implode(',', $input);
			return $ret;
		}
	}
}
global $campoMinado;
//CampoMinado
if (!$campoMinado) {
	$campoMinado = new CampoMinadoWeb();
}

if (isset($_GET['input'])) {
	$campoMinado->readGameInput();
} else {
	$campoMinado->update();
}
?>
</pre>
<form method="get">
	<input type="text"   name="input" id="input" />
	<input type="submit" />
</form>
<img src="wide.bmp" />
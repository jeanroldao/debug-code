<?php
require_once("dompdf/dompdf_config.inc.php");
$dompdf = new DOMPDF();
//var_dump(get_class_methods($dompdf));
$dompdf->load_html('
<html>
<head></head>
<style>
h1 {color:#333; size:20px; margin-bottom:5px;}
h3 {color:#222;}
</style>
<body>

<h1>IgorEscobar.com</h1>
<h3>Desenvolvimento, Tecnologia e Informação, na ponta do lápis.</h3>

</body></html>
');
$dompdf->set_paper('letter', 'landscape');
$dompdf->render();
$dompdf->stream("exemplo-01.pdf");

?>
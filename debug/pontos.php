<?php
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

$pontos = csv_to_array('C:\Documents and Settings\jean\Meus documentos\ponto.csv');
//die('<pre>'.print_r($ponto,true));

if (!empty($_POST['hora'])) {
  $codigo_novo = $pontos[count($pontos)-1]['codigo'];
  $dt = new datetime();
  $datahora_novo = $dt->format('Y-m-d H:i');
  $pontos[] = array(
    'codigo' => $codigo_novo,
    'datahora' => $datahora_novo
  );
}
?>
<form method="POST">
<input type="text" name="hora" />
<input type="submit" />
</form>
<table border="1">
  <tr>
    <th>codigo</th>
    <th>datahora</th>
  </tr>
  <?php
  foreach ($pontos as $linha) {
  $datahora = new DateTime($linha['datahora']);
  ?>
  <tr>
    <td><?php echo $linha['codigo']; ?></td>
    <td><?php echo $datahora->format('d/m/Y H:i'); ?></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php 
?>
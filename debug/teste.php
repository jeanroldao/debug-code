<?php

$servername = 'LUCAS\SQLEXPRESS';
$usuario = 'sa';
$senha = '1234';
$dbase = 'autom';

$link = @mssql_connect($servername, $usuario, $senha); // Conexao com o SQL Server
if(!$link) { die("Não foi possível estabelecer conexão com o SQL Server."); } // Verifica a conexao com o SQL Server
$db = @mssql_select_db($dbase, $link); // Selecao do Banco de Dados
if(!$db) { die("Não foi possível estabelecer conexão com o banco de dados."); } //Verifica a conexao com o Banco de Dados

$query = mssql_query('SELECT * FROM dbo.tblLOG WHERE logNumCta = 177113');

if (!mssql_num_rows($query)) {
    echo 'No records found';
} else {
    // The following is equal to the code below:
    //
    // while ($row = mssql_fetch_row($query)) {

    while ($row = mssql_fetch_array($query, MSSQL_ASSOC)) {
        $aArray[] = $row;
    }
}

echo '<pre>';
print_r($aArray);
echo '</pre>';
exit;

// Free the query result
mssql_free_result($query);

?>
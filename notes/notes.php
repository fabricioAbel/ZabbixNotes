<?php
#CONECTAR AO BANCO DE DADOS
include "dbConnects.php";
$host = strtoupper($_GET["host"]);
#VERSAO 1
#$sql = "select notes_durante, notes_pos from tabela_notes where lower(hosts) like '%".$host.",%'";
#versao 2
$sql = "select acao_expediente as notes_durante, acao_extra as notes_pos from sistemanotes where host = '".$host."' and status='A'";
$result = mysqli_query($conn, $sql);
$dados = mysqli_fetch_array($result);
$conteudo_durante = $dados[0];
$conteudo_pos = $dados[1]. "<p>";
#VERIFICANDO INFORMAÇÃO ADICIONAL
$sql = "select info from sistemanotes_info_complementar WHERE status='A'";
$result = mysqli_query($conn, $sql);
$dados = mysqli_fetch_array($result);
$conteudo_pos .= $dados[0];

if(strlen(trim($conteudo_durante)) == 0)
 $conteudo_durante = "NENHUM NOTES CADASTRADO DURANTE EXPEDIENTE";
if(strlen(trim($conteudo_pos)) == 0)
 $conteudo_pos = "NENHUM NOTES CADASTRADO PÓS EXPEDIENTE";

?>
<html>
<!doctype html>
<head>
  <meta charset="utf-8">
  <title> ZABBIX: <?=strtoupper($host)?> NOTES </title>
</head>
<style>
.fonte
{
   font-family: verdana, arial, helvetica, sans-serif; 
   font-size: 30px;
}
.cabecalho
{
   font-size: 40px;
   font-weight: bold;
}

.container
{
   width: 80%;   border: 4px solid #779DC0;
}

.iframe
{
  top : 0;
  width: 100%;
  height: 100%;
}
</style>
<body bgcolor='#A3BCD4'> 
<table style='width: 100%'>
<tr>
  <td>
    <img src='zabbix.png' height='100px' width='330px' style='margin-left: 70px'>
  </td>
</tr>
<tr>
<td height='80px'>
</td>
</tr>
<tr>
<td colspan='2' style='text-align: right;' class='fonte' > 
   <a style='cursor: pointer;margin-right: 160px;font-size: 20px;text-decoration: underline' onclick='window.history.go(-1)'> VOLTAR </a> </td>
</tr>
</table>
<center>
<table class='container fonte'>
<tr bgcolor='#EBEFF2'>
   <td height='300px' width='50%' bgcolor='#696969'>
       PROBLEMA DURANTE EXPEDIENTE 
   </td>
   <td  width='50%'> 
  <div style="width:100%; max-height:300px; overflow:auto">
       <?=$conteudo_durante?>
  </div>
</tr>

<tr bgcolor='#EBEFF2'>
	   <td height='300px' bgcolor='#696969'>
       PROBLEMA FORA EXPEDIENTE 
   </td>
   <td>
  <div style="width:100%; max-height:300px; overflow:auto">
        <?=$conteudo_pos?>
  </div>
   </td>
</tr>
</table>
</center>
<table style='width: 100%'>
<tr>
<td colspan='2' style='text-align: right;' class='fonte' > 
   <a style='cursor: pointer;margin-right: 160px;font-size: 20px; text-decoration: underline' onclick='window.history.go(-1)'> VOLTAR </a> </td>
</tr>
</table>

</body>
</html>


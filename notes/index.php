<?php
session_start();
include "dbConnects.php";
include "ldap.php";

if( $_SESSION["loginManZbx"] != "" )
{
 echo "<script> window.location.href = 'manutencao.php'; </script>";
 exit;
}

$acao = $_REQUEST["acao"];

if($acao == 1) //TENTA FAZER O LOGIN
{
   $login = $_REQUEST["txtLogin"];
   $pwd   = $_REQUEST["txtPwd"];
   
   $success = "N";
   #CONSULTA O BANCO DE DADOS - ZABBIX E LDAP
   $sql = "select login from usuario_notes where status='A' and login = '".$login."'";
   $result = mysqli_query($conn, $sql);
   if (mysqli_num_rows($result) == 0) #faz autenticação LDAP 
     $msgErro = "Usuário não cadastrado na lista de permissão.";
   else
   { 
      #AUTENTICACAO LDAP
      $autenticou = autent_ldap($login, $pwd);
      #SE NAO AUTENTICOU, TENTA O USUARIO E SENHA DO SISTEMA
      if(!$autenticou)
      {	
	#SE SENHA DIGITADA POR NULA, AQUI DA O ERRO, POIS NAO HOUVE AUTENTICACAO LDAP, N POSSO DEIXAR AUTENTICACAO SEM SENHAi
   	   $sql = "select login from usuario_notes where status='A' and login = '".$login."' and password = '".md5($pwd)."'";
  	   $result = mysqli_query($conn, $sql);
   	   if (mysqli_num_rows($result) > 0 && $pwd != "") #faz autenticação PROPRIA
	   {
	     $success = "Y";
             $_SESSION["loginManZbx"] = $login;
 	   }
	   else
             $msgErro = "Login ou Senha inválidos.";

      }
      else
      {
	$success = "Y";
        $_SESSION["loginManZbx"] = $login;
      }	
   }
   #GUARDA LOG DE LOGIN NO SISTEMA
   $sql = "insert into log_login_notes (data, login, success) values (now(),'".$login."','".$success."')";
   $result = mysqli_query($conn, $sql);

   if($success == "Y")
   {
      echo "<script type='text/javascript'>
                window.location = 'manutencao.php';
      </script>";
      exit;
   }

}
elseif($acao == 2) //LOGOUT
{
    $_SESSION["loginManZbx"] = "";
    unset ($_SESSION["loginManZbx"]);
    $entrou = false;
}

?>
<html>
<!doctype html>
<head>
  <meta charset="utf-8">
  <title> ZABBIX: SISTEMA DE NOTES - LOGIN </title>
</head>
<style>
.fonte
{
   font-family: verdana, arial, helvetica, sans-serif; 
   font-size: 30px;
}
.fonte2
{
  font-family: verdana, arial, helvetica, sans-serif;
  font-size: 18px;
}
.fonte3
{
  font-family: verdana, arial, helvetica, sans-serif;
  font-size: 14px;
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
<script>
function logout(form)
{
  form.acao.value = 2;
  form.submit();
}
function entrar(form)
{
  form.acao.value = 1;
  form.submit();
}
</script>
<body bgcolor='#A3BCD4' onload="document.frmLogin.txtLogin.focus()">  
<form name='frmLogin' method='post' action='#'>
<table style='width: 100%'>
<tr>
  <td>
    <img src='zabbix.png' height='100px' width='330px' style='margin-left: 70px'>
  </td>
<!--  <td align='right'> 
    <img src='unicamp.svg' height='100px' width='100px' style='margin-right: 70px'>
   </td>-->
</tr>
<tr>
<td height='50px'>
</td>
</tr>
</table>
<center>
<span class='fonte'>SISTEMA DE NOTES<span> <p>
<table bgcolor='#EBEFF2' class='container fonte2' border='0'>
<tr>
 <td style='text-align: right;padding-right: 10px' width='45%'>
  LOGIN:
 </td>
 <td style='text-align: left;padding-left: 10px' width='55%' height='30px'>
    <input style='width: 140px' type='text' name='txtLogin' class='fonte3'/>  
 </td>
</tr>
<tr>
  <td style='text-align: right;padding-right: 10px'>
   SENHA:
  </td>
  <td style='text-align: left;padding-left: 10px'>
    <input style='width: 140px' type='password' name='txtPwd' class='fonte3' onkeydown="if (event.keyCode == 13) entrar(document.frmLogin)"/>  
  </td>
 </tr>
 <?php if($msgErro != "")
   echo "<tr><td colspan='2' style='text-align: center'><span class='fonte3' style='color: red'>*".$msgErro." </span></td></tr>"?>
 <tr>
  <td colspan='2' style='text-align: center'>
     <input type='button' value="ENTRAR" onclick="entrar(document.frmLogin)">
  </td>
</tr>
</table>
 <input type='hidden' name='acao' value=''/>
 <input type='hidden' name='renovaSessao' value='<?=$_SESSION["loginManZbx"]?>' />
 
</form>
</center>
</body>
</html>


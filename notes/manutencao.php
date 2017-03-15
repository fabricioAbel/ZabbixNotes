<?php
session_start();
include "dbConnects.php";

function salvaLog($conn, $host, $acao, $user)
{
  $sql = "INSERT INTO sistemanotes_log (elemento, acao, usuario, data)
	 VALUES ('".$host."','".$acao."','".$user."',now())";
  $result = mysqli_query($conn, $sql);
}
function removeSpecialChar($str)
{
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
    // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
    $str = preg_replace('/[^a-z0-9]/i', '_', $str);
    $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
    //$str = preg_replace('', '', $str); // ideia do Bacco :)
    return $str;
}

if($_REQUEST["renovaSessao"] != "")
  $_SESSION["loginManZbx"] = $_REQUEST["renovaSessao"];

if($_SESSION["loginManZbx"] == "")
{
    $_SESSION["loginManZbx"] = "";
    unset ($_SESSION["loginManZbx"]);
     echo "<script type='text/javascript'>

		alert('Usuário não logado ou Sessão expirada!');
                window.location = 'index.php';
      </script>";
      exit; 
}
#DEFAULT
$checkedExpediente[0] = "checked";
$checkedExpediente[1] = "";
$checkedExtra[0] = "checked";
$checkedExtra[1] = "";
$displayExpediente = "none";
$displayExtra = "none";

$acao = $_REQUEST["acao"];
#var_dump($acao);
#tenho que tirar toda a acentuação e caracteres especiais
#PEGANDO O ID NA BASE DE DADOS
$host = strtoupper(removeSpecialChar(trim($_REQUEST["txtHost"])));
//var_dump($host);
if($acao == 1) #SALVANDO
{
  $rdExpediente = $_REQUEST["rdExpediente"];
  $rdExtra = $_REQUEST["rdExtra"];
  $textExpediente = $rdExpediente == "S"?$_REQUEST["textExpediente"]: "Sem ação.";
  $textExtra = $rdExtra == "S"?$_REQUEST["textExtra"]:"Sem ação.";
  
  $sql = "SELECT host FROM sistemanotes WHERE host = '".$host."' AND status='A'";
  $result = mysqli_query($conn, $sql);
  $var_log = "";
  if(mysqli_num_rows($result) > 0)
  {
    #$sql = "UPDATE sistemanotes SET notes_expediente = '".$rdExpediente."', acao_expediente = '".$textExpediente."', notes_extra = '".$rdExtra."', acao_extra = '".$textExtra."' WHERE host = '".$host."'";
    #$result = mysqli_query($conn, $sql);
   $var_log = 'A';
   $sql = "UPDATE sistemanotes SET status = 'X' WHERE host = '".$host."'";
   $result = mysqli_query($conn, $sql);


  }
#  else
#  {
    $sql = "INSERT INTO sistemanotes (host, notes_expediente, acao_expediente, notes_extra, acao_extra) VALUES ('".$host."','".$rdExpediente."','".$textExpediente."','".$rdExtra."','".$textExtra."')";
    if($var_log != 'A')
       $var_log = 'I';
#  }
  #var_dump($sql);exit;
  $result = mysqli_query($conn, $sql);
  salvaLog($conn, $host, $var_log, $_SESSION['loginManZbx']);

  #INCLUIU COM SUCESSO, NOTIFICAR USUARIO
  #echo "<script> alert('Notes cadastrado.');</script>";
  echo "<script> alert('Informações salvas com sucesso.');</script>";
  $host = "";
  $rdExpediente = "";
  $rdExtra = "";
  $textExpediente = "";
  $textExtra = "";
  $acao = "";
 
}
else if($acao==2)#EXCLUIR VINCULO 
{
   $sql = "UPDATE sistemanotes SET status = 'X' WHERE host = '".$host."'";
   $result = mysqli_query($conn, $sql);
   salvaLog($conn, $host, 'X', $_SESSION['loginManZbx']);
      
   echo "<script> alert('Host agora está sem Notes.');</script>";
   $host = "";
   $rdExpediente = "";
   $rdExtra = "";
   $textExpediente = "";
   $textExtra = "";
   $acao = "";

}
else if($acao==3)#ACABOU DE DIGITAR O NOME DO HOST POPULANDO POSSIVEL NOTES NA TELA
{
   #BUSCANDO AS INFORMAÇÕES NA BASE DE DADOS
   $sql = "SELECT notes_expediente, acao_expediente, notes_extra, acao_extra FROM sistemanotes WHERE host = '".$host."' AND status='A'"; 
   $result = mysqli_query($conn, $sql);
   if(mysqli_num_rows($result) > 0)
   {
        $dados = mysqli_fetch_array($result);
	$rdExpediente = $dados["notes_expediente"];
        $textExpediente = $dados["acao_expediente"];
	$rdExtra = $dados["notes_extra"];
        $textExtra = $dados["acao_extra"];

   }


}
else if($acao == 4) #CLONAR
{
  
  #CASO POSSUA REGISTRO , EXCLUO.
  $sql = "SELECT host FROM sistemanotes WHERE host = '".$host."' AND status='A'";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) > 0)
  {
     $sql = "UPDATE sistemanotes SET status = 'X' WHERE host = '".$host."'";
     $result = mysqli_query($conn, $sql);
     salvaLog($conn, $host, 'X', $_SESSION['loginManZbx']);
  }
  
  $host_modelo = $_REQUEST["selClone"];
  $sql = "INSERT INTO sistemanotes SELECT '".$host."', notes_expediente, acao_expediente, notes_extra, acao_extra,status FROM sistemanotes WHERE host = '".$host_modelo."' AND status='A'";
  #die($sql);
  $result = mysqli_query($conn, $sql);
  salvaLog($conn, 'CLONAGEM DE '.$host_modelo.' PARA '.$host, 'C', $_SESSION['loginManZbx']);

  #INCLUIU COM SUCESSO, NOTIFICAR USUARIO
  echo "<script> alert('Notes clonado.');</script>";
  $host = "";
  $rdExpediente = "";
  $rdExtra = "";
  $textExpediente = "";
  $textExtra = "";
  $acao = "";
  $sql = "";
}
else if($acao == 5) #INFO COMPLEMENTAR
{
  $info = $_REQUEST["textInfo"];

  $sql = "UPDATE sistemanotes_info_complementar SET status='X'";
  $result = mysqli_query($conn, $sql);   

  salvaLog($conn, 'INFO_COMPLEMENTAR', 'X', $_SESSION['loginManZbx']);

  $sql = "INSERT INTO sistemanotes_info_complementar (info, status) values ('".$info."','A')";
  $result = mysqli_query($conn, $sql);

  salvaLog($conn, 'INFO_COMPLEMENTAR', 'I', $_SESSION['loginManZbx']);
  
  echo "<script> alert('Informação complementar salva.');</script>";
}
else if($acao == 6) #LOGOUT
{
  $_SESSION['loginManZbx'] == "";
  unset ($_SESSION['loginManZbx']);

  echo "<script> window.location.href = 'index.php'; </script>";
  exit;
}
else if($acao == 7) #ADDUSER
{
  $user = strtolower(removeSpecialChar(trim($_REQUEST["txtUser"])));
  $password = $_REQUEST["pwdSenha"];  
  $sql = "SELECT login from usuario_notes WHERE login = '".$user."' AND status='A'";
  $result = mysqli_query($conn, $sql);
  $rows = mysqli_num_rows($result);
  if($rows > 0)
    echo "<script>alert('Usuário já está ativo no sistema! Inclusão não efetuada.')</script>";
  else
  {
        $sql = "INSERT INTO usuario_notes (login, password) values ('".$user."', '".md5($password)."')";
        $result = mysqli_query($conn, $sql);

	salvaLog($conn, "USUARIO-".$user, 'I', $_SESSION['loginManZbx']);
  
	echo "<script> alert('Permissão de acesso ao sistema concedida.');</script>";
  }
  
}
else if($acao == 8) #REMOVE USER
{  
  $user = strtolower(removeSpecialChar(trim($_REQUEST["selUsers"])));
  $sql = "UPDATE usuario_notes SET status ='X' WHERE login = '".$user."'";
  $result = mysqli_query($conn, $sql);

  salvaLog($conn, "USUARIO-".$user, 'X', $_SESSION['loginManZbx']);
  
  echo "<script> alert('Permissão de acesso removida.');</script>";
}
else if($acao == 9) #ATUALIZAÇAO EM MASSA
{
  $busca = $_REQUEST["txtLook"];
  $substitui = $_REQUEST["txtReplace"];
  
  $sql = "UPDATE sistemanotes SET acao_expediente = replace(acao_expediente, '".$busca."','".$substitui."'), acao_extra = replace(acao_extra, '".$busca."', '".$substitui."') WHERE status='A'";
  $result = mysqli_query($conn, $sql);
  
  $sql = "INSERT INTO sistemanotes_log_atualizacao (procurado, substitui, usuario, data) VALUES ('".$busca."','".$substitui."','".$_SESSION['loginManZbx']."', now())";  
  $result = mysqli_query($conn, $sql);

  echo "<script> alert('Atualização realizada.');</script>";  
}
if($rdExpediente == "S")
{
  $displayExpediente = "";
  $checkedExpediente[1] = "checked";  
}
else
  $textExpediente = "";
if($rdExtra == "S")
{
  $displayExtra = "";
  $checkedExtra[1] = "checked";
}
else
  $textExtra = "";
?>
<html>
<head>
  <meta charset="utf-8">
  <title> ZABBIX: SISTEMA DE NOTES - MANUTENÇÃO </title>
  
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script type="text/javascript" src="scriptAbas.js"></script>
  <script type="text/javascript" src="scripts.js"></script>
  <link rel="stylesheet" type="text/css" href="estiloAbas.css" />    

</head>
<body bgcolor='#A3BCD4'> 
<form name="frmManutencao" method="POST" action="#">

<div class="content">
<table style='width: 100%'>
<tr>
  <td>
    <img src='zabbix.png' height='100px' width='330px' style='margin-left: 70px'>
  </td>
<!--  <td align='right'> 
    <img src='unicamp.svg' height='100px' width='100px' style='margin-right: 70px'>
   </td> -->
</tr>
<tr>
<td height='50px'>
</td>
</tr>
</table>
<center>
<span class='titulo'>SISTEMA DE NOTES<span>
</center>
<div style='text-align: right'>
Usuário: <?=$_SESSION['loginManZbx']?> <a href='#' onclick='logout(document.frmManutencao)'>SAIR</a>
</div>
  <div class="tabs-content" style='margin-top: 15px'>
    <div class="tabs-menu clearfix">
      <ul>
        <li><a class="active-tab-menu" href="#" data-tab="tab1">MANUTENÇÃO NOTES</a></li>
        <li><a href="#" data-tab="tab2">INFO ADICIONAL EXTRA EXPEDIENTE</a></li>
        <li><a href="#" data-tab="tab3">USUÁRIOS</a></li>
        <li><a href="#" data-tab="tab4">ATUALIZAÇÃO EM MASSA</a></li>
      </ul>
    </div> <!-- tabs-menu -->
    
    <!-- CONTEÚDO ABA 1 -->
    <div class="tab1 tabs first-tab">

       <div> HOST: <input type="text" name="txtHost" value="<?=$host?>" onblur="carregaInfo(document.frmManutencao)" style="margin-left: 30px" />
	     <span style='margin-left: 80px'> COPIAR NOTES DO HOST </span>
	    <select style='margin-left: 15px' name='selClone' width='100px' onchange="clonar(document.frmManutencao, document.frmManutencao.txtHost.value, this.value)"> 
            <option value="0">SELECIONE</option>
	   <?php
		$sql = "SELECT host FROM sistemanotes WHERE host <> '".$host."' AND status='A' ORDER BY host ASC";
		$result = mysqli_query($conn, $sql);
		while($dados = mysqli_fetch_array($result))
		   echo "<option value='".$dados["host"]."'>".$dados["host"]."</option>";

	   ?>	
	    </select>	</div> 
        <div style="padding-top: 30px"> POSSUI AÇÃO DURANTE EXPEDIENTE COMUM ? </div>
       <div> <input type="radio" name="rdExpediente" value="N" <?=$checkedExpediente[0]?> onclick="show('divExpediente',this.value)" /> NÃO
	     <input type="radio" name="rdExpediente" value="S" <?=$checkedExpediente[1]?> onclick="show('divExpediente',this.value)" /> SIM </div>
       <div id="divExpediente" style="display: <?=$displayExpediente?>"> AÇÃO DURANTE EXPEDIENTE: <p>
				<textarea name="textExpediente" rows="4" cols="50"><?=$textExpediente?> </textarea>
        </div> 
	
       <div style="margin-top: 30px"> POSSUI AÇÃO EXTRA EXPEDIENTE ? </div>
       <div> <input type="radio" name="rdExtra" value="N" <?=$checkedExtra[0]?> onclick="show('divExtra',this.value)" /> NÃO
	     <input type="radio" name="rdExtra" value="S" <?=$checkedExtra[1]?> onclick="show('divExtra',this.value)" /> SIM </div>
       <div id="divExtra" style="display: <?=$displayExtra?>"> AÇÃO EXTRA EXPEDIENTE: <p>
				<textarea name="textExtra" rows="4" cols="50"><?=$textExtra?> </textarea></div> 	
	<div> 

	      <input type="button" style="margin-top:50px;" value="SALVAR" onclick="salvar(document.frmManutencao)" />
	      <input type="button" style="margin-top:50px;margin-left: 20px" value="REMOVER" onclick="remover(document.frmManutencao, document.frmManutencao.txtHost.value)" />
	      <input type="button" style="margin-top:50px;margin-left: 20px" value="LIMPAR" onclick="document.frmManutencao.txtHost.value = '';document.frmManutencao.acao.value = 0;document.frmManutencao.action='#';document.frmManutencao.submit();" /> </div> 
   
    </div> <!-- .tab1 -->
    
    <div class="tab2 tabs"> <!-- INFO ADICIONAL EXTRA EXPEDIENTE -->
       <div>Preencha aqui, informação complementar que será mostrada junto com todas as ações extra expedientes cadastradas.</div>
 	<?php
	   $sql = "SELECT info from sistemanotes_info_complementar WHERe status = 'A'";
           $result = mysqli_query($conn, $sql);
           $dados = mysqli_fetch_array($result);
	   $info_complementar = $dados[0];
		?>
      <div style='margin-top: 20px;'>
	     <textarea name="textInfo" rows="4" cols="50"><?=$info_complementar?> </textarea>
       </div>
       <div>
	      <input type="button" name="btSalvarInfo" style="margin-top:50px;" value="SALVAR" onclick="salvarInfo(document.frmManutencao)" />
       </div>
    </div> <!-- .tab2 -->
    
     <div class="tab3 tabs"> <!-- USUÁRIOSS -->
       <div style="width: 20%" >
	<fieldset>
	 <legend>Novo Usuário</legend>
		   Login:  <input type="text" name="txtUser" style="width: 160px; margin-left: 7px" /> <br/> <br/>
		   Senha:  <input type="password" name="pwdSenha" style="width: 160px"  onkeydown="if (event.keyCode == 13) addUser(document.frmManutencao)" />

 		 <input type="button" name="btAddUser" value="Adicionar" onclick="addUser(document.frmManutencao)" />
		   <div><i> {Para autenticação LDAP, deixar campo Senha em branco}</i></div>
		

	</fieldset>
	 <div style="margin-top: 20px"> Lista de usuários com permissão de acesso ao sistema: </div>
         <div><i>{Clique no usuário para removê-lo}</i> </div>
         <select size="6" name="selUsers" multiple onchange="removeUser(document.frmManutencao)">
          <?php
	   $sql = "SELECT login FROM usuario_notes WHERE status = 'A' ORDER BY login ASC";
           $result = mysqli_query($conn, $sql);
           while($dados = mysqli_fetch_array($result))
	   {
		echo "<option value='".$dados["login"]."'>".$dados["login"]."</option>";
	   }
	 ?>
         </select>
       </div>
    </div> <!-- .tab3 -->
    <div class="tab4 tabs"> <!-- ATUALIZAÇÃO EM MASSA -->
     <div> Preencha aqui os trechos de NOTES que deseja atualizar em massa. </div>
     <div><i>{Case sensitive}</i> </div>
     <div style='margin-top: 20px'>PROCURAR POR: </div>    
     <div> <input type="text" style="width: 400px" name="txtLook" /> </div>

     <div>SUBSTITUIR POR: </div>
     <div> <input type="text" style="width: 400px" name="txtReplace" /> </div>
     <div style="margin-top: 20px">
      <input type="button" value="ATUALIZAR" name="btnAtualizar" onclick="updateNotes(document.frmManutencao, document.frmManutencao.txtLook.value, document.frmManutencao.txtReplace.value)" />
    </div>

    </div> <!-- .tab4 -->
  </div> <!-- .tabs -->
</div> <!-- .content -->


 <input type='hidden' name='acao' value=''/>
 <input type='hidden' name='host_modelo' value=''/>
 <input type='hidden' name='renovaSessao' value='<?=$_SESSION["loginManZbx"]?>' />
 
</form>
</body>
</html>


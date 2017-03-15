<?php
session_start();
include "dbConnects.php";

function salvaLog($conn, $host, $acao, $user)
{
  $sqlLog = "INSERT INTO sistemanotes_log (elemento, acao, usuario, data)
	 VALUES ('".$host."','".$acao."','".$user."',now())";
  $resultLog = mysqli_query($conn, $sqlLog);
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


$sql = "truncate sistemanotes;";
$result = mysqli_query($conn, $sql);
$sql = "truncate sistemanotes_log";
$result = mysqli_query($conn, $sql);
$sql = "select hosts, notes_durante, notes_pos from tabela_notes";
$result = mysqli_query($conn, $sql);
while($dados=mysqli_fetch_array($result))
{
   $listaHosts = explode(",",$dados["hosts"]);
   echo "<pre>".var_dump($listaHosts)."</pre>";
   foreach($listaHosts as $host)
   {     
     if(trim($host) != "")
     {
        $host = strtoupper(removeSpecialChar(trim($host)));
        #CASO POSSUA REGISTRO , EXCLUO.
        $sql2 = "SELECT host FROM sistemanotes WHERE host = '".$host."' AND status='A'";
        $result2 = mysqli_query($conn, $sql2);
        if(mysqli_num_rows($result2) > 0)
        {
         $sql2 = "UPDATE sistemanotes SET status = 'X' WHERE host = '".$host."'";
         $result2 = mysqli_query($conn, $sql2);
         salvaLog($conn, $host, 'X', $_SESSION['loginManZbx']);
        }
     
        $sql2 = "INSERT INTO sistemanotes (host, notes_expediente, acao_expediente, notes_extra, acao_extra) VALUES ('".$host."','N','".$dados["notes_durante"]."','N','".$dados["notes_pos"]."')";
        $result2 = mysqli_query($conn, $sql2);
        salvaLog($conn, $host, 'I', $_SESSION['loginManZbx']);
      }
   }

}

$sql = "UPDATE sistemanotes SET notes_expediente = 'S' WHERE  lower(acao_expediente) not like '%sem ação%' and lower(acao_expediente) not like '%apenas registrar no logbook%' and lower(acao_expediente) not like '%anotar no logbook%'";
$result = mysqli_query($conn, $sql);
$sql = "UPDATE sistemanotes SET notes_extra = 'S' WHERE  lower(acao_extra) not like '%sem ação%' and lower(acao_extra) not like '%apenas registrar no logbook%' and lower(acao_extra) not like '%não é necessário%' and lower(acao_extra) not like '%não acionar%' and lower(acao_extra) not like '%anotar no logbook.%'";
$result = mysqli_query($conn, $sql);
die("fim");


?>

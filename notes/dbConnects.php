<?php
include "dbConfigs.php";
$conn   = mysqli_connect($server, $user, $pwd, $dbName) or die("Erro de conexão com o banco de dados.");
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, "SET character_set_connection=utf8");
mysqli_query($conn, "SET character_set_client=utf8");
mysqli_query($conn, "SET character_set_results=utf8");
?>


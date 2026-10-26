<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id		 		= $_POST["id"];
$modelo_id		= $_POST["modelo_id"];
$serie			= $_POST["serie"];
$imei1			= $_POST["imei1"];
$imei2			= $_POST["imei2"];
$observacao		= $_POST["observacao"];
$status_id		= $_POST["status_id"];

	
	$gravacao = $dados->gravaDadosAparelho($id, $modelo_id, $serie, $imei1, $imei2, $observacao, $status_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


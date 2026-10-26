<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");
require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id		 		= $_POST["id"];
$num_celular 	= $_POST["num_celular"];
$operadora_id	= $_POST["operadora_id"];
$imei			= $_POST["imei"];
$observacao		= $_POST["observacao"];
$status_id		= $_POST["status_id"];

	
	$gravacao = $dados->gravaDadosCelular($id, $num_celular, $operadora_id, $imei, $observacao, $status_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


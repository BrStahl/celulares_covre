<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$responsavel_id 		= $_POST["responsavel_id"];
$usuario_id		 		= $_POST["usuario_id"];
$adm_id		 			= $_POST["adm_id"];
$gerar_vale_id			= $_POST["gerar_vale_id"];
$status_id	 			= $_POST["status_id"];
$estoque_id	 			= $_POST["estoque_id"];

	
	$gravacao = $dados->gravaResponsavel($responsavel_id,$usuario_id, $estoque_id, $adm_id, $gerar_vale_id, $status_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


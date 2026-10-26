<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id		 		= $_POST["id"];
$acao			= $_POST["acao"];

	
	$gravacao = $dados->aceitaAparelho($id, $acao);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


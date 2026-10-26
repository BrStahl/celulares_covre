<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	

$id	 				= $_POST["id"];
$celular_covre_id 	= $_POST["celular_covre_id"];
$data 				= $_POST["data"];
$valor 				= $_POST["valor"];
$observacao 		= $_POST["observacao"];

	
	$data_despesa = 
	implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
		
	
	$gravacao = $dados->gravaDespesaExtra($id, $celular_covre_id, $data_despesa, $valor, $observacao);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


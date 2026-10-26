<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$responsavel_id 		= $_POST["responsavel_id"];
$centro_custo_id		= $_POST["centro_custo_id"];

	
	$gravacao = $dados->gravaCentroCusto($responsavel_id,$centro_custo_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


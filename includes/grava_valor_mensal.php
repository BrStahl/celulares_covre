<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$plano_id 			= $_POST["plano_id"];
$valor				= $_POST["valor"];
$mes				= $_POST["mes"];
$ano				= $_POST["ano"];


	
	$gravacao = $dados->gravaValorMensal($plano_id, $valor, $mes, $ano);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


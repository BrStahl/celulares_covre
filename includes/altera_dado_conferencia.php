<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$valor				= $_POST["valor"];
$conferencia_id		= $_POST["conferencia_id"];
$numero_campo		= $_POST["numero_campo"];


	
	$gravacao = $dados->alteraDadoConferencia($valor, $conferencia_id, $numero_campo);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


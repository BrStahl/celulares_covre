<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$valor				= $_POST["valor"];
$id					= $_POST["id"];
$numero_campo		= $_POST["numero_campo"];

	$data = 
	implode(preg_match("~\/~", $valor) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $valor) == 0 ? "-" : "/", $valor)));
	
	
	$gravacao = $dados->alteraData($data, $id, $numero_campo);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


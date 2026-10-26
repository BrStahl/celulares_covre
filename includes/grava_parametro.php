<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");
require_once "../class/GravaDados.php";

$dados = new GravaDados;	



$valor 			= $_POST["valor"];

	
	$gravacao = $dados->gravaParametro($valor);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id		 			= $_POST["id"];
$plano_id 			= $_POST["plano_id"];

	
	$gravacao = $dados->alteraPlano($plano_id, $id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


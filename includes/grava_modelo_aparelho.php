<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id		 		= $_POST["id"];
$marca_id		= $_POST["marca_id"];
$modelo			= $_POST["modelo"];
$status_id		= $_POST["status_id"];

	
	$gravacao = $dados->gravaModeloAparelho($id, $marca_id, $modelo, $status_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>


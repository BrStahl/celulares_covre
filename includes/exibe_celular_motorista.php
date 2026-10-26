<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	

	$nome_motorista = $_POST["nome"];


	$relatorio_celular = $dados->exibeCelMotorista($nome_motorista);  
	
	$rel_celular = $relatorio_celular->celular;
	
	print $rel_celular;



?>


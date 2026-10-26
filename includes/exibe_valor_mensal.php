<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_valor = $dados->exibeValorMensal();  
	
	$rel_valor = $relatorio_valor->valor;
	
	print $rel_valor;



?>


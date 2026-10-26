<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_despesa = $dados->exibeDespesaExtra();  
	
	$rel_despesa = $relatorio_despesa->despesa;
	
	print $rel_despesa;



?>


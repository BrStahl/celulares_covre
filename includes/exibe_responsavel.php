<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_responsavel = $dados->exibeResponsavel();  
	
	$rel_responsavel = $relatorio_responsavel->responsavel;
	
	print $rel_responsavel;



?>


<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_aparelho = $dados->exibeDadosAparelho();  
	
	$rel_aparelho = $relatorio_aparelho->aparelho;
	
	print $rel_aparelho;



?>


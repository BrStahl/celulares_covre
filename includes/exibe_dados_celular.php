<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_celular = $dados->exibeDadosCelular();  
	
	$rel_celular = $relatorio_celular->celular;
	
	print $rel_celular;



?>


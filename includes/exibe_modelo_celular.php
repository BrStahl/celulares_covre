<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$relatorio_modelo = $dados->exibeModeloCelular();  
	
	$rel_modelo = $relatorio_modelo->modelo;
	
	print $rel_modelo;



?>


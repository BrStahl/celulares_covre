<?
include('../class/Dados.php');


$dados = new Dados();


	$rel_agregados = $dados->buscaAgregados();  

	print $rel_agregados->agregados;


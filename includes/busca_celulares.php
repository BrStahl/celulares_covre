<?
include('../class/Dados.php');


$dados = new Dados();


	$rel_agregados = $dados->buscaCelulares();  

	print $rel_agregados->celulares;

